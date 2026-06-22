<?php
namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Repositories\CreneauRepository;
use App\Repositories\ClientRepository;
use App\Helpers\Mailer;
use App\Helpers\SmsSender;
use App\Helpers\Session;
use App\Entities\Reservation;
use App\Entities\Client;
use App\Entities\Creneau;

class ReservationService
{
    private ReservationRepository $reservationRepo;
    private CreneauRepository $creneauRepo;
    private ClientRepository $clientRepo;
    private Mailer $mailer;
    private SmsSender $smsSender;
    private Session $session;

    public function __construct(
        ReservationRepository $reservationRepo,
        CreneauRepository $creneauRepo,
        ClientRepository $clientRepo,
        Mailer $mailer,
        SmsSender $smsSender,
        Session $session
    ) {
        $this->reservationRepo = $reservationRepo;
        $this->creneauRepo = $creneauRepo;
        $this->clientRepo = $clientRepo;
        $this->mailer = $mailer;
        $this->smsSender = $smsSender;
        $this->session = $session;
    }

    /**
     * Récupérer les créneaux disponibles pour une date
     */
    public function getCreneauxDisponibles(string $date, int $minimumCouverts = 1): array
    {
        return $this->creneauRepo->findDisponibles($date, $minimumCouverts);
    }

    /**
     * Récupérer les réservations par date
     */
    public function getReservationsByDate(string $date): array
    {
        return $this->reservationRepo->findByDate($date);
    }

    /**
     * Créer une réservation
     */
    public function reserver(array $data): Reservation
    {
        // 1. Vérifier la disponibilité du créneau
        $creneau = $this->creneauRepo->find($data['creneau_id']);
        if (!$creneau) {
            throw new \Exception('Créneau non trouvé');
        }
        if ($creneau->getCouvertsDisponibles() < $data['nb_personnes']) {
            throw new \Exception('Plus assez de places disponibles pour ce créneau');
        }

        // 2. Créer ou récupérer le client
        $client = new Client();
        $client->setNom($data['nom']);
        $client->setTelephone($data['telephone']);
        if (!empty($data['email'])) {
            $client->setEmail($data['email']);
        }
        $clientId = $this->clientRepo->save($client);

        // 3. Créer la réservation
        $reservation = new Reservation();
        $reservation->setClientId($clientId);
        $reservation->setCreneauId($creneau->getId());
        $reservation->setNombrePersonnes($data['nb_personnes']);
        if (!empty($data['commentaire'])) {
            $reservation->setCommentaire($data['commentaire']);
        }
        $reservationId = $this->reservationRepo->save($reservation);

        // 4. Décrémenter les couverts disponibles
        $this->creneauRepo->decrementerCouverts($creneau->getId(), $data['nb_personnes']);

        // 5. Envoyer les notifications
        try {
            $this->mailer->sendConfirmationClient(
                $client->getEmail(),
                $client->getNom(),
                $creneau,
                $data['nb_personnes']
            );
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas la réservation
            error_log('Erreur envoi email : ' . $e->getMessage());
        }

        try {
            $this->smsSender->sendConfirmation(
                $client->getTelephone(),
                $client->getNom(),
                $creneau,
                $data['nb_personnes']
            );
        } catch (\Exception $e) {
            error_log('Erreur envoi SMS : ' . $e->getMessage());
        }

        // 6. Retourner l'entité réservation complète
        return $this->reservationRepo->find($reservationId);
    }

    /**
     * Annuler une réservation
     */
    public function annulerReservation(int $id): void
    {
        $reservation = $this->reservationRepo->find($id);
        if (!$reservation) {
            throw new \Exception('Réservation non trouvée');
        }

        if ($reservation->getStatut() === 'annulee') {
            throw new \Exception('Cette réservation est déjà annulée');
        }

        // Annuler la réservation
        $this->reservationRepo->updateStatut($id, 'annulee');
        
        // Libérer les couverts
        $this->creneauRepo->incrementerCouverts(
            $reservation->getCreneauId(),
            $reservation->getNombrePersonnes()
        );
    }

    /**
     * Changer le statut d'une réservation
     */
    public function changerStatut(int $id, string $statut): void
    {
        $reservation = $this->reservationRepo->find($id);
        if (!$reservation) {
            throw new \Exception('Réservation non trouvée');
        }

        if ($reservation->getStatut() === 'annulee' && $statut !== 'annulee') {
            throw new \Exception('Impossible de réactiver une réservation annulée');
        }

        $this->reservationRepo->updateStatut($id, $statut);
    }
}