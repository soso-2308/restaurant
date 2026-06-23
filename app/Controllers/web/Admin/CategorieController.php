<?php
namespace App\Controllers\Web\Admin;

use App\Core\Controller;
use App\Models\Categorie;
use App\Models\Plat;
use App\Middlewares\AuthMiddleware;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CategorieController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        (new AuthMiddleware())->handle();
    }

    /**
     * Liste des catégories avec filtres, recherche et tri
     */
    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $ordre = $_GET['ordre'] ?? null;
        $sort = $_GET['sort'] ?? 'ordre';
        $direction = $_GET['dir'] ?? 'ASC';

        // Construction de la requête avec recherche et tri
        $query = new Categorie();

        if ($search) {
            $query->search(['nom', 'description'], $search);
        }

        // Filtre sur l'ordre (ex: afficher les catégories ayant un ordre > 0)
        if ($ordre !== null && is_numeric($ordre)) {
            $query->where('ordre', $ordre);
        }

        // Tri
        $query->orderBy($sort, $direction);

        // Récupération paginée (10 par page)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $pagination = $query->paginate(10, $page);

        $this->render('admin/categories/index', [
            'title' => 'Gestion des catégories - RYOHA',
            'layout' => 'admin',
            'active_page' => 'categories',
            'categories' => $pagination['data'],
            'pagination' => $pagination['pagination'],
            'filters' => ['search' => $search, 'ordre' => $ordre, 'sort' => $sort, 'dir' => $direction]
        ]);
    }

    /**
     * Formulaire d'ajout
     */
    public function create(): void
    {
        $this->render('admin/categories/form', [
            'title' => 'Ajouter une catégorie - RYOHA',
            'layout' => 'admin',
            'active_page' => 'categories',
            'categorie' => null
        ]);
    }

    /**
     * Sauvegarder une nouvelle catégorie
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/categories');
        }

        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $ordre = (int)($_POST['ordre'] ?? 0);

        // Validation
        if (empty($nom)) {
            $this->session->setFlash('Le nom est obligatoire', 'error');
            $this->redirect('/admin/categories/create');
        }

        // Vérifier l'unicité du nom
        $existing = (new Categorie())->where('nom', $nom)->first();
        if ($existing) {
            $this->session->setFlash('Une catégorie avec ce nom existe déjà', 'error');
            $this->redirect('/admin/categories/create');
        }

        $categorie = new Categorie();
        $categorie->setNom($nom)
                  ->setDescription($description ?: null)
                  ->setOrdre($ordre);
        if ($categorie->save()) {
            $this->session->setFlash('Catégorie ajoutée avec succès', 'success');
        } else {
            $this->session->setFlash('Erreur lors de l\'ajout', 'error');
        }
        $this->redirect('/admin/categories');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(int $id): void
    {
        $categorie = (new Categorie())->find($id);
        if (!$categorie) {
            $this->session->setFlash('Catégorie non trouvée', 'error');
            $this->redirect('/admin/categories');
        }

        $this->render('admin/categories/form', [
            'title' => 'Modifier une catégorie - RYOHA',
            'layout' => 'admin',
            'active_page' => 'categories',
            'categorie' => $categorie
        ]);
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/categories');
        }

        $categorie = (new Categorie())->find($id);
        if (!$categorie) {
            $this->session->setFlash('Catégorie non trouvée', 'error');
            $this->redirect('/admin/categories');
        }

        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $ordre = (int)($_POST['ordre'] ?? 0);

        if (empty($nom)) {
            $this->session->setFlash('Le nom est obligatoire', 'error');
            $this->redirect('/admin/categories/edit/' . $id);
        }

        // Vérifier l'unicité du nom (sauf si c'est le même ID)
        $existing = (new Categorie())->where('nom', $nom)->first();
        if ($existing && $existing->getId() !== $categorie->getId()) {
            $this->session->setFlash('Une catégorie avec ce nom existe déjà', 'error');
            $this->redirect('/admin/categories/edit/' . $id);
        }

        $categorie->setNom($nom)
                  ->setDescription($description ?: null)
                  ->setOrdre($ordre);
        if ($categorie->save()) {
            $this->session->setFlash('Catégorie mise à jour', 'success');
        } else {
            $this->session->setFlash('Erreur lors de la mise à jour', 'error');
        }
        $this->redirect('/admin/categories');
    }

    /**
     * Suppression (AJAX)
     */
    public function delete(): void
    {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Requête invalide'], 400);
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ID invalide'], 400);
        }

        $categorie = (new Categorie())->find($id);
        if (!$categorie) {
            $this->json(['success' => false, 'message' => 'Catégorie non trouvée'], 404);
        }

        try {
            $categorie->safeDelete();
            $this->json(['success' => true, 'message' => 'Catégorie supprimée']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Export PDF
     */
    public function exportPdf(): void
    {
        $search = trim($_GET['search'] ?? '');
        $query = new Categorie();
        if ($search) {
            $query->search(['nom', 'description'], $search);
        }
        $categories = $query->orderBy('ordre', 'ASC')->get();

        $html = '<h1 style="text-align:center;">Liste des catégories</h1>';
        $html .= '<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">';
        $html .= '<thead><tr><th>ID</th><th>Nom</th><th>Description</th><th>Ordre</th></tr></thead><tbody>';
        foreach ($categories as $cat) {
            $html .= "<tr>
                <td>{$cat->getId()}</td>
                <td>{$cat->getNom()}</td>
                <td>{$cat->getDescription()}</td>
                <td>{$cat->getOrdre()}</td>
            </tr>";
        }
        $html .= '</tbody></table>';

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="categories_' . date('Y-m-d') . '.pdf"');
        echo $dompdf->output();
        exit;
    }

    /**
     * Export Excel
     */
    public function exportExcel(): void
    {
        $search = trim($_GET['search'] ?? '');
        $query = new Categorie();
        if ($search) {
            $query->search(['nom', 'description'], $search);
        }
        $categories = $query->orderBy('ordre', 'ASC')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Ordre');

        $row = 2;
        foreach ($categories as $cat) {
            $sheet->setCellValue('A' . $row, $cat->getId());
            $sheet->setCellValue('B' . $row, $cat->getNom());
            $sheet->setCellValue('C' . $row, $cat->getDescription());
            $sheet->setCellValue('D' . $row, $cat->getOrdre());
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="categories_' . date('Y-m-d') . '.xlsx"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}