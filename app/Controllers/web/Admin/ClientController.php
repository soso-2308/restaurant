<?php
namespace App\Controllers\Web\Admin;

use App\Core\Controller;
use App\Models\Client;
use App\Middlewares\AuthMiddleware;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ClientController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        (new AuthMiddleware())->handle();
    }

    /**
     * Liste des clients avec filtres, recherche, tri et pagination
     */
    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;
        $minReservations = $_GET['min_reservations'] ?? null;
        $sort = $_GET['sort'] ?? 'created_at';
        $direction = $_GET['dir'] ?? 'DESC';

        $filters = [];
        if ($dateDebut && $dateFin) {
            $filters['date_debut'] = $dateDebut;
            $filters['date_fin'] = $dateFin;
        }
        if ($minReservations !== null && is_numeric($minReservations)) {
            $filters['min_reservations'] = (int)$minReservations;
        }

        $query = Client::adminList($filters, $search)->orderBy($sort, $direction);
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $pagination = $query->paginate(15, $page);

        // Statistiques
        $stats = [
            'total' => (new Client())->count(),
            'avec_reservations' => (new Client())->whereIn('id', function($q) {
                $q->queryWhere[] = "id IN (SELECT DISTINCT client_id FROM reservations)";
            })->count(),
            'top_telephone' => Client::adminList([], null)->orderBy('telephone')->first()?->getTelephone() ?? 'N/A'
        ];

        $this->render('admin/clients/index', [
            'title' => 'Gestion des clients - RYOHA',
            'layout' => 'admin',
            'active_page' => 'clients',
            'clients' => $pagination['data'],
            'pagination' => $pagination['pagination'],
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'min_reservations' => $minReservations,
                'sort' => $sort,
                'dir' => $direction
            ]
        ]);
    }

    /**
     * Voir le détail d'un client (avec ses réservations)
     */
    public function show(int $id): void
    {
        $client = (new Client())->find($id);
        if (!$client) {
            $this->session->setFlash('Client non trouvé', 'error');
            $this->redirect('/admin/clients');
        }
        $reservations = $client->reservations();

        $this->render('admin/clients/show', [
            'title' => 'Détail client - RYOHA',
            'layout' => 'admin',
            'active_page' => 'clients',
            'client' => $client,
            'reservations' => $reservations
        ]);
    }

    /**
     * Export PDF
     */
    public function exportPdf(): void
    {
        $search = trim($_GET['search'] ?? '');
        $filters = [];
        if (!empty($_GET['date_debut']) && !empty($_GET['date_fin'])) {
            $filters['date_debut'] = $_GET['date_debut'];
            $filters['date_fin'] = $_GET['date_fin'];
        }
        if (isset($_GET['min_reservations']) && is_numeric($_GET['min_reservations'])) {
            $filters['min_reservations'] = (int)$_GET['min_reservations'];
        }

        $data = Client::exportData($filters, $search);

        $html = '<h1 style="text-align:center;">Liste des clients</h1>';
        $html .= '<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">';
        $html .= '<thead><tr>';
        foreach (array_keys($data[0] ?? []) as $header) {
            $html .= "<th>{$header}</th>";
        }
        $html .= '</tr></thead><tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= "<td>{$cell}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="clients_' . date('Y-m-d') . '.pdf"');
        echo $dompdf->output();
        exit;
    }

    /**
     * Export Excel
     */
    public function exportExcel(): void
    {
        $search = trim($_GET['search'] ?? '');
        $filters = [];
        if (!empty($_GET['date_debut']) && !empty($_GET['date_fin'])) {
            $filters['date_debut'] = $_GET['date_debut'];
            $filters['date_fin'] = $_GET['date_fin'];
        }
        if (isset($_GET['min_reservations']) && is_numeric($_GET['min_reservations'])) {
            $filters['min_reservations'] = (int)$_GET['min_reservations'];
        }

        $data = Client::exportData($filters, $search);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!empty($data)) {
            $headers = array_keys($data[0]);
            foreach ($headers as $colIndex => $header) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
            }
            $rowIndex = 2;
            foreach ($data as $row) {
                $colIndex = 1;
                foreach ($row as $cell) {
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $cell);
                    $colIndex++;
                }
                $rowIndex++;
            }
            foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="clients_' . date('Y-m-d') . '.xlsx"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}