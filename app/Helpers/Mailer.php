<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        
        try {
            $this->mail->isSMTP();
            $this->mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $_ENV['SMTP_USER'] ?? '';
            $this->mail->Password = $_ENV['SMTP_PASS'] ?? '';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = (int)($_ENV['SMTP_PORT'] ?? 587);
            $this->mail->CharSet = 'UTF-8';
            $this->mail->setFrom(
                $_ENV['SMTP_USER'] ?? 'contact@ryoha.com',
                'Restaurant RYOHA'
            );
        } catch (Exception $e) {
            // Log error
        }
    }

    /**
     * Envoyer un email de confirmation de réservation
     */
    public function sendConfirmationClient(string $email, string $nom, array $creneau, int $nbPersonnes): bool
    {
        if (empty($email)) {
            return false;
        }

        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $nom);
            $this->mail->Subject = 'Confirmation de votre réservation - RYOHA';

            $date = date('d/m/Y', strtotime($creneau['date_reservation']));
            $heure = substr($creneau['heure_debut'], 0, 5) . ' - ' . substr($creneau['heure_fin'], 0, 5);

            $message = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; color: #333; }
                        .header { background: #1a1a1a; color: #f5e6d3; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .footer { background: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #999; }
                    </style>
                </head>
                <body>
                    <div class='header'>
                        <h1 style='color: #e8a87c;'>Restaurant RYOHA</h1>
                    </div>
                    <div class='content'>
                        <h2>Bonjour $nom,</h2>
                        <p>Votre réservation a été confirmée avec succès !</p>
                        <p><strong>Date :</strong> $date</p>
                        <p><strong>Heure :</strong> $heure</p>
                        <p><strong>Nombre de personnes :</strong> $nbPersonnes</p>
                        <p>Nous vous attendons avec plaisir !</p>
                        <p>Pour toute modification, contactez-nous au +257 79 123 456.</p>
                    </div>
                    <div class='footer'>
                        &copy; " . date('Y') . " Restaurant RYOHA - Bujumbura, Burundi
                    </div>
                </body>
                </html>
            ";

            $this->mail->isHTML(true);
            $this->mail->Body = $message;
            $this->mail->AltBody = strip_tags($message);

            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Envoyer une notification au restaurant pour une nouvelle réservation
     */
    public function sendNotificationRestaurant(array $reservation, array $creneau): bool
    {
        $adminEmail = $_ENV['SMTP_USER'] ?? 'admin@ryoha.com';

        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($adminEmail);
            $this->mail->Subject = 'Nouvelle réservation - RYOHA';

            $message = "
                <html>
                <body>
                    <h2>Nouvelle réservation !</h2>
                    <p><strong>Client :</strong> {$reservation['nom']}</p>
                    <p><strong>Téléphone :</strong> {$reservation['telephone']}</p>
                    <p><strong>Email :</strong> {$reservation['email']}</p>
                    <p><strong>Date :</strong> " . date('d/m/Y', strtotime($creneau['date_reservation'])) . "</p>
                    <p><strong>Heure :</strong> " . substr($creneau['heure_debut'], 0, 5) . "</p>
                    <p><strong>Personnes :</strong> {$reservation['nombre_personnes']}</p>
                    <hr>
                    <p><a href='http://localhost/restaurant-ryoha/admin'>Voir toutes les réservations</a></p>
                </body>
                </html>
            ";

            $this->mail->isHTML(true);
            $this->mail->Body = $message;

            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}