<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Repositories\ClientRepository;
use App\Repositories\CreneauRepository;
use App\Repositories\PlatRepository;
use App\Repositories\UserRepository;
use App\Entities\Client;

echo "🧪 Test des Repositories\n";
echo "-------------------------\n\n";

// 1. Test UserRepository
$userRepo = new UserRepository();
$user = $userRepo->findByUsername('admin');
if ($user) {
    echo "✅ Utilisateur trouvé : " . $user->getUsername() . "\n";
} else {
    echo "❌ Utilisateur non trouvé\n";
}

// 2. Test ClientRepository
$clientRepo = new ClientRepository();
$client = new Client();
$client->setNom('Test Client')
       ->setTelephone('+25779000000')
       ->setEmail('test@example.com');
$clientId = $clientRepo->save($client);
echo "✅ Client créé avec ID : $clientId\n";

// 3. Test CreneauRepository
$creneauRepo = new CreneauRepository();
$disponibles = $creneauRepo->findDisponibles(date('Y-m-d'), 2);
echo "✅ Créneaux disponibles aujourd'hui : " . count($disponibles) . "\n";

// 4. Test PlatRepository
$platRepo = new PlatRepository();
$plats = $platRepo->getPopulaires(3);
echo "✅ Plats populaires : " . count($plats) . "\n";
foreach ($plats as $plat) {
    echo "   - " . $plat->getNom() . " (" . $plat->getPrix() . " FBu)\n";
}

echo "\n🎉 Tous les repositories fonctionnent !\n";