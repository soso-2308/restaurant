<?php
namespace App\Services;

use App\Helpers\Mailer;
use App\Helpers\SmsSender;
use App\Entities\Reservation;
use App\Entities\Creneau;
use App\Entities\Client;

class NotificationService
{
    private Mailer $mailer;
    private SmsSender $smsSender;

    public function __construct()
    {
        $this->mailer = new Mailer();
        $this->smsSender = new SmsSender();
    }

    /**
     * Envoyer les confirmations au client
     */
    public function sendConfirmation(Client $client, Creneau $creneau, int $nombrePersonnes): void
    {
        // Email
        if ($client->getEmail()) {
            $this->mailer->sendConfirmationClient(
                $client->getEmail(),
                $client->getNom(),
                $creneau,
                $nombrePersonnes
            );
        }

        // SMS
        if ($client->getTelephone()) {
            $this->smsSender->sendConfirmation(
                $client->getTelephone(),
                $client->getNom(),
                $creneau,
                $nombrePersonnes
            );
        }
    }

    /**
     * Notifier le restaurant (email)
     */
    public function notifyRestaurant(Reservation $reservation, Creneau $creneau, Client $client): void
    {
        $this->mailer->sendNotificationRestaurant(
            $reservation,
            $creneau,
            $client
        );
    }

    /**
     * Envoyer une notification d'annulation au client
     */
    public function sendCancellation(Client $client, Creneau $creneau): void
    {
        // SMS
        if ($client->getTelephone()) {
            $this->smsSender->sendCustomMessage(
                $client->getTelephone(),
                "Bonjour {$client->getNom()}, votre réservation du " .
                date('d/m/Y', strtotime($creneau->getDateReservation())) .
                " à " . substr($creneau->getHeureDebut(), 0, 5) .
                " a été annulée. Contactez-nous pour plus d'informations."
            );
        }
    }
}