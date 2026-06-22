<?php
namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Repositories\PlatRepository;
use App\Repositories\AvisRepository;
use App\Repositories\ConfigRepository;

class StatsService
{
    private ReservationRepository $reservationRepo;
    private PlatRepository $platRepo;
    private AvisRepository $avisRepo;
    private ConfigRepository $configRepo;

    public function __construct(
        ReservationRepository $reservationRepo,
        PlatRepository $platRepo,
        AvisRepository $avisRepo,
        ConfigRepository $configRepo
    ) {
        $this->reservationRepo = $reservationRepo;
        $this->platRepo = $platRepo;
        $this->avisRepo = $avisRepo;
        $this->configRepo = $configRepo;
    }

    /**
     * Obtenir les statistiques générales
     */
    public function getGeneralStats(): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m-01');
        $lastMonth = date('Y-m-01', strtotime('-1 month'));

        // Réservations du mois en cours
        $reservationsMois = $this->reservationRepo->findByDateRange($thisMonth, $today);
        $totalReservations = count($reservationsMois);
        $totalCouverts = array_sum(array_map(function($r) { return $r->getNombrePersonnes(); }, $reservationsMois));

        // Réservations du mois dernier
        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd = date('Y-m-t', strtotime('-1 month'));
        $reservationsLastMonth = $this->reservationRepo->findByDateRange($lastMonthStart, $lastMonthEnd);
        $totalLastMonth = count($reservationsLastMonth);

        // Taux de croissance
        $croissance = 0;
        if ($totalLastMonth > 0) {
            $croissance = (($totalReservations - $totalLastMonth) / $totalLastMonth) * 100;
        }

        // Note moyenne des avis
        $noteMoyenne = 0;
        $totalAvis = 0;
        try {
            $avisStats = $this->avisRepo->getNoteMoyenne();
            $noteMoyenne = $avisStats['moyenne'] ?? 0;
            $totalAvis = $avisStats['total'] ?? 0;
        } catch (\Exception $e) {
            // Si la table n'existe pas
        }

        // Taux d'occupation du jour
        $capacite = (int)$this->configRepo->get('salle_capacite', 50);
        $reservationsToday = $this->reservationRepo->findByDate($today);
        $couvertsToday = array_sum(array_map(function($r) { return $r->getNombrePersonnes(); }, $reservationsToday));
        $tauxOccupation = $capacite > 0 ? round(($couvertsToday / $capacite) * 100) : 0;

        return [
            'reservations_mois' => $totalReservations,
            'couverts_mois' => $totalCouverts,
            'croissance' => round($croissance, 1),
            'note_moyenne' => $noteMoyenne,
            'total_avis' => $totalAvis,
            'taux_occupation' => $tauxOccupation,
            'couverts_today' => $couvertsToday
        ];
    }

    /**
     * Évolution des réservations sur les 7 derniers jours
     */
    public function getReservationsEvolution(int $days = 7): array
    {
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $reservations = $this->reservationRepo->findByDate($date);
            $labels[] = date('d/m', strtotime($date));
            $values[] = count($reservations);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Top des plats les plus commandés
     */
    public function getTopPlats(int $limit = 5): array
    {
        return $this->platRepo->getPopulaires($limit);
    }

    /**
     * Derniers avis approuvés
     */
    public function getRecentAvis(int $limit = 5): array
    {
        try {
            return $this->avisRepo->getApprouves($limit);
        } catch (\PDOException $e) {
            // Si la table n'existe pas, on retourne un tableau vide
            return [];
        }
    }
}