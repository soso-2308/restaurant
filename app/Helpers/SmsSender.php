<?php
namespace App\Helpers;

use AfricasTalking\SDK\AfricasTalking;

class SmsSender
{
    private ?AfricasTalking $client = null;
    private string $username;
    private string $apiKey;

    public function __construct()
    {
        $this->username = $_ENV['SMS_USERNAME'] ?? 'sandbox';
        $this->apiKey = $_ENV['SMS_API_KEY'] ?? '';

        if (!empty($this->apiKey)) {
            try {
                $this->client = new AfricasTalking($this->username, $this->apiKey);
            } catch (\Exception $e) {
                // Log error
            }
        }
    }

    /**
     * Envoyer un SMS de confirmation de réservation
     */
    public function sendConfirmation(string $telephone, string $nom, array $creneau, int $nbPersonnes): bool
    {
        if (!$this->client || empty($telephone)) {
            return false;
        }

        try {
            $sms = $this->client->sms();

            $date = date('d/m/Y', strtotime($creneau['date_reservation']));
            $heure = substr($creneau['heure_debut'], 0, 5);

            $message = sprintf(
                "Bonjour %s, votre réservation chez RYOHA est confirmée pour le %s à %s (%d personnes). Merci !",
                $nom,
                $date,
                $heure,
                $nbPersonnes
            );

            $result = $sms->send([
                'to' => $telephone,
                'message' => $message,
                'from' => 'RYOHA'
            ]);

            return $result['status'] === 'success';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Envoyer un SMS personnalisé
     */
    public function sendCustom(string $telephone, string $message): bool
    {
        if (!$this->client || empty($telephone)) {
            return false;
        }

        try {
            $sms = $this->client->sms();
            $result = $sms->send([
                'to' => $telephone,
                'message' => $message,
                'from' => 'RYOHA'
            ]);
            return $result['status'] === 'success';
        } catch (\Exception $e) {
            return false;
        }
    }
}