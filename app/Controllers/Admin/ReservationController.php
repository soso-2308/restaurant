<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ReservationService;
use App\Middlewares\AuthMiddleware;

class ReservationController extends Controller
{
    private ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        parent::__construct();
        
        $auth = new AuthMiddleware();
        $auth->handle();
        
        $this->reservationService = $reservationService;
    }

    /**
     * Liste des réservations
     */
    public function index(): void
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $reservations = $this->reservationService->getReservationsByDate($date);
        
        $this->render('admin/reservations/index', [
            'title' => 'Gestion des réservations - RYOHA',
            'layout' => 'admin',
            'active_page' => 'reservations',
            'reservations' => $reservations,
            'date_selected' => $date
        ]);
    }

    /**
     * Annuler une réservation (AJAX)
     */
    public function annuler(): void
    {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Requête invalide'], 400);
        }

        $id = (int)($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ID invalide'], 400);
        }

        try {
            $this->reservationService->annulerReservation($id);
            $this->json([
                'success' => true,
                'message' => 'Réservation annulée avec succès'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Changer le statut d'une réservation (AJAX)
     */
    public function changerStatut(): void
    {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Requête invalide'], 400);
        }

        $id = (int)($_POST['id'] ?? 0);
        $statut = $_POST['statut'] ?? '';

        if ($id <= 0 || !in_array($statut, ['confirmee', 'terminee'])) {
            $this->json(['success' => false, 'message' => 'Données invalides'], 400);
        }

        try {
            $this->reservationService->changerStatut($id, $statut);
            $this->json([
                'success' => true,
                'message' => 'Statut mis à jour'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}