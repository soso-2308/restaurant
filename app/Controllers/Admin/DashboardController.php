<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\StatsService;
use App\Services\ReservationService;
use App\Middlewares\AuthMiddleware;

class DashboardController extends Controller
{
    private StatsService $statsService;
    private ReservationService $reservationService;

    public function __construct(StatsService $statsService, ReservationService $reservationService)
    {
        parent::__construct();
        
        // Appel direct du middleware (utilise $_SESSION)
        $auth = new AuthMiddleware();
        $auth->handle();
        
        $this->statsService = $statsService;
        $this->reservationService = $reservationService;
    }

    public function index(): void
    {
        // Statistiques générales
        $stats = $this->statsService->getGeneralStats();
        
        // Réservations du jour
        $reservationsToday = $this->reservationService->getReservationsByDate(date('Y-m-d'));
        
        // Évolution des réservations (7 jours)
        $evolution = $this->statsService->getReservationsEvolution(7);
        
        $this->render('admin/dashboard', [
            'title' => 'Dashboard - RYOHA',
            'layout' => 'admin',
            'active_page' => 'dashboard',
            'stats' => $stats,
            'reservations_today' => $reservationsToday,
            'evolution' => $evolution
        ]);
    }
}