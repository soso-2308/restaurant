<?php
namespace App\Controllers\Web\Admin;

use App\Core\Controller;
use App\Models\Reservation;
use App\Middlewares\AuthMiddleware;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReservationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        (new AuthMiddleware())->handle();
    }

    /**
     * Liste des réservations avec filtres (GET /admin/reservations)
     */
    public function index(): void
    {
        // Récupération des filtres depuis l'URL
        $statut = $_GET['statut'] ?? null;
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;
        $search = trim($_GET['search'] ?? '');

        // Construction du tableau de filtres
        $filters = [];
        if ($statut) $filters['statut'] = $statut;
        if ($dateDebut && $dateFin) {
            $filters['date'] = ['from' => $dateDebut, 'to' => $dateFin];
        }

        // Requête avec filtres et recherche
        $query = Reservation::adminList($filters, $search);
        $reservations = $query->get();

        // Statistiques rapides
        $stats = [
            'total' => count($reservations),
            'confirmees' => Reservation::adminList(['statut' => 'confirmee'])->count(),
            'terminees' => Reservation::adminList(['statut' => 'terminee'])->count(),
            'annulees' => Reservation::adminList(['statut' => 'annulee'])->count()
        ];

        $this->render('admin/reservations/index', [
            'title' => 'Gestion des réservations - RYOHA',
            'layout' => 'admin',
            'active_page' => 'reservations',
            'reservations' => $reservations,
            'stats' => $stats,
            'filters' => ['statut' => $statut, 'date_debut' => $dateDebut, 'date_fin' => $dateFin, 'search' => $search]
        ]);
    }

    /**
     * EXPORT PDF (GET /admin/reservations/export/pdf)
     */
    public function exportPdf(): void
    {
        $search = trim($_GET['search'] ?? '');
        $statut = $_GET['statut'] ?? null;
        $filters = $statut ? ['statut' => $statut] : [];

        // Récupération des données formatées
        $data = Reservation::exportData($filters, $search);

        // Génération du HTML pour le PDF
        $html = '<h1 style="text-align:center;">Liste des réservations</h1>';
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

        // Configuration DomPDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Envoi du fichier
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="reservations_' . date('Y-m-d') . '.pdf"');
        echo $dompdf->output();
        exit;
    }

    /**
     * EXPORT EXCEL (GET /admin/reservations/export/excel)
     */
    public function exportExcel(): void
    {
        $search = trim($_GET['search'] ?? '');
        $statut = $_GET['statut'] ?? null;
        $filters = $statut ? ['statut' => $statut] : [];

        $data = Reservation::exportData($filters, $search);

        // Création du fichier Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Entêtes
        if (!empty($data)) {
            $headers = array_keys($data[0]);
            foreach ($headers as $colIndex => $header) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
            }

            // Données
            $rowIndex = 2;
            foreach ($data as $row) {
                $colIndex = 1;
                foreach ($row as $cell) {
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $cell);
                    $colIndex++;
                }
                $rowIndex++;
            }
        }

        // Ajustement auto des colonnes
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Envoi du fichier
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="reservations_' . date('Y-m-d') . '.xlsx"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}