<?php
namespace App\Controllers\Visitor;

use App\Core\Controller;
use App\Services\MenuService;
use App\Services\StatsService;

class HomeController extends Controller
{
    private MenuService $menuService;
    private StatsService $statsService;

    public function __construct(MenuService $menuService, StatsService $statsService)
    {
        parent::__construct();
        $this->menuService = $menuService;
        $this->statsService = $statsService;
    }

    public function index(): void
    {
        // Récupérer les plats populaires
        $platsPopulaires = $this->menuService->getPlatsPopulaires(6);
        
        // Récupérer les catégories actives
        $categories = $this->menuService->getCategoriesActives();

        // Récupérer les avis récents
        $avisRecents = $this->statsService->getRecentAvis(5);

        $this->render('visitor/home', [
            'title' => 'Restaurant RYOHA - Accueil',
            'plats_populaires' => $platsPopulaires,
            'categories' => $categories,
            'avis_recents' => $avisRecents
        ]);
    }
}