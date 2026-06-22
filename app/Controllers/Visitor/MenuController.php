<?php
namespace App\Controllers\Visitor;

use App\Core\Controller;
use App\Services\MenuService;

class MenuController extends Controller
{
    private MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        parent::__construct();
        $this->menuService = $menuService;
    }

    public function index(): void
    {
        // Récupérer les paramètres de filtrage
        $categorieId = isset($_GET['categorie']) ? (int)$_GET['categorie'] : null;
        $recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : null;

        // Récupérer les catégories actives pour le filtre
        $categories = $this->menuService->getCategoriesActives();

        // Récupérer les plats selon les filtres
        if ($recherche) {
            $plats = $this->menuService->rechercherPlats($recherche);
        } elseif ($categorieId) {
            $plats = $this->menuService->getPlatsParCategorie($categorieId);
        } else {
            $plats = $this->menuService->getTousLesPlats();
        }

        $this->render('visitor/menu', [
            'title' => 'Menu - Restaurant RYOHA',
            'plats' => $plats,
            'categories' => $categories,
            'categorie_active' => $categorieId,
            'recherche' => $recherche
        ]);
    }
}