<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Entities\Client;
use App\Entities\Creneau;
use App\Entities\Reservation;

// Test Client
$client = new Client();
$client->setNom('Jean Dupont')
       ->setTelephone('+25779123456')
       ->setEmail('jean@example.com');

echo "✅ Client créé : " . $client->getNom() . "\n";
echo "Tableau du client : " . print_r($client->toArray(), true) . "\n";

// Test Creneau
$creneau = new Creneau();
$creneau->setDateReservation('2026-06-25')
        ->setHeureDebut('12:00:00')
        ->setHeureFin('12:30:00')
        ->setCapaciteMax(50)
        ->setCouvertsDisponibles(50);

echo "✅ Créneau créé : " . $creneau->getDateReservation() . " à " . $creneau->getHeureDebut() . "\n";

// Test Reservation (avec IDs factices pour l'exemple)
$reservation = new Reservation();
$reservation->setClientId(1)           // ← ID factice
            ->setCreneauId(1)          // ← ID factice
            ->setNombrePersonnes(4)
            ->setCommentaire('Fenêtre côté jardin');

echo "✅ Réservation créée pour " . $reservation->getNombrePersonnes() . " personnes\n";
echo "Tableau de la réservation : " . print_r($reservation->toArray(), true) . "\n";

echo "\n🎉 Toutes les entités fonctionnent !\n";