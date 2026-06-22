<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\MenuService;
use App\Services\UploadService;
use App\Middlewares\AuthMiddleware;
use App\Helpers\Csrf;

class MenuController extends Controller
{
    private MenuService $menuService;
    private UploadService $uploadService;

    public function __construct(MenuService $menuService, UploadService $uploadService)
    {
        parent::__construct();
        
        $auth = new AuthMiddleware();
        $auth->handle();
        
        $this->menuService = $menuService;
        $this->uploadService = $uploadService;
    }

    /**
     * Liste des plats
     */
    public function index(): void
    {
        $plats = $this->menuService->getTousLesPlats();
        $categories = $this->menuService->getCategoriesActives();
        
        $this->render('admin/menu/index', [
            'title' => 'Gestion du menu - RYOHA',
            'layout' => 'admin',
            'active_page' => 'menu',
            'plats' => $plats,
            'categories' => $categories
        ]);
    }

    /**
     * Formulaire d'ajout
     */
    public function create(): void
    {
        $categories = $this->menuService->getCategoriesActives();
        
        $this->render('admin/menu/form', [
            'title' => 'Ajouter un plat - RYOHA',
            'layout' => 'admin',
            'active_page' => 'menu',
            'plat' => null,
            'categories' => $categories,
            'csrf_token' => Csrf::generateToken()
        ]);
    }

    /**
     * Sauvegarder un nouveau plat
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/menu');
        }

        // Vérifier CSRF
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('Erreur de sécurité', 'error');
            $this->redirect('/admin/menu/create');
        }

        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'prix' => (float)($_POST['prix'] ?? 0),
            'categorie_id' => (int)($_POST['categorie_id'] ?? 0),
            'disponible' => isset($_POST['disponible']) ? 1 : 0
        ];

        // Validation
        $errors = [];
        if (empty($data['nom'])) $errors[] = "Le nom est obligatoire";
        if ($data['prix'] <= 0) $errors[] = "Le prix doit être supérieur à 0";
        if ($data['categorie_id'] <= 0) $errors[] = "Veuillez sélectionner une catégorie";

        if (!empty($errors)) {
            $this->session->setFlash(implode('<br>', $errors), 'error');
            $this->redirect('/admin/menu/create');
        }

        // Gérer l'upload de l'image
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->uploadService->uploadImage($_FILES['image'], 'plats');
            if (!$imageUrl) {
                $this->session->setFlash('Erreur lors de l\'upload de l\'image', 'error');
                $this->redirect('/admin/menu/create');
            }
        }

        try {
            $platId = $this->menuService->creerPlat($data, $imageUrl);
            $this->session->setFlash('Plat ajouté avec succès !', 'success');
            $this->redirect('/admin/menu');
        } catch (\Exception $e) {
            $this->session->setFlash('Erreur : ' . $e->getMessage(), 'error');
            $this->redirect('/admin/menu/create');
        }
    }

    /**
     * Formulaire d'édition
     */
    public function edit(int $id): void
    {
        $plat = $this->menuService->getPlat($id);
        if (!$plat) {
            $this->session->setFlash('Plat non trouvé', 'error');
            $this->redirect('/admin/menu');
        }

        $categories = $this->menuService->getCategoriesActives();

        $this->render('admin/menu/form', [
            'title' => 'Modifier un plat - RYOHA',
            'layout' => 'admin',
            'active_page' => 'menu',
            'plat' => $plat,
            'categories' => $categories,
            'csrf_token' => Csrf::generateToken()
        ]);
    }

    /**
     * Mettre à jour un plat
     */
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/menu');
        }

        // Vérifier CSRF
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('Erreur de sécurité', 'error');
            $this->redirect('/admin/menu/edit/' . $id);
        }

        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'prix' => (float)($_POST['prix'] ?? 0),
            'categorie_id' => (int)($_POST['categorie_id'] ?? 0),
            'disponible' => isset($_POST['disponible']) ? 1 : 0
        ];

        // Validation
        $errors = [];
        if (empty($data['nom'])) $errors[] = "Le nom est obligatoire";
        if ($data['prix'] <= 0) $errors[] = "Le prix doit être supérieur à 0";
        if ($data['categorie_id'] <= 0) $errors[] = "Veuillez sélectionner une catégorie";

        if (!empty($errors)) {
            $this->session->setFlash(implode('<br>', $errors), 'error');
            $this->redirect('/admin/menu/edit/' . $id);
        }

        // Gérer l'upload de l'image
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->uploadService->uploadImage($_FILES['image'], 'plats');
            if (!$imageUrl) {
                $this->session->setFlash('Erreur lors de l\'upload de l\'image', 'error');
                $this->redirect('/admin/menu/edit/' . $id);
            }
        }

        try {
            $this->menuService->modifierPlat($id, $data, $imageUrl);
            $this->session->setFlash('Plat modifié avec succès !', 'success');
            $this->redirect('/admin/menu');
        } catch (\Exception $e) {
            $this->session->setFlash('Erreur : ' . $e->getMessage(), 'error');
            $this->redirect('/admin/menu/edit/' . $id);
        }
    }

    /**
     * Supprimer un plat (AJAX)
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

        try {
            $this->menuService->supprimerPlat($id);
            $this->json([
                'success' => true,
                'message' => 'Plat supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}