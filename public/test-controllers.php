<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\Visitor\HomeController;
use App\Controllers\Visitor\MenuController;
use App\Controllers\Visitor\ReservationController;
use App\Services\MenuService;
use App\Services\ReservationService;
use App\Services\StatsService;
use App\Repositories\PlatRepository;
use App\Repositories\CategorieRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\CreneauRepository;
use App\Repositories\ClientRepository;
use App\Repositories\AvisRepository;
use App\Repositories\ConfigRepository;
use App\Helpers\Session;
use App\Helpers\Mailer;
use App\Helpers\SmsSender;

echo "🧪 Test des Contrôleurs Visiteur\n";
echo "--------------------------------\n\n";

// Instancier les services
$platRepo = new PlatRepository();
$categorieRepo = new CategorieRepository();
$menuService = new MenuService($platRepo, $categorieRepo);

$reservationRepo = new ReservationRepository();
$creneauRepo = new CreneauRepository();
$clientRepo = new ClientRepository();
$session = new Session();
$mailer = new Mailer();
$smsSender = new SmsSender();
$reservationService = new ReservationService(
    $reservationRepo, $creneauRepo, $clientRepo, $mailer, $smsSender, $session
);

$avisRepo = new AvisRepository();
$configRepo = new ConfigRepository();
$statsService = new StatsService($reservationRepo, $platRepo, $avisRepo, $configRepo);

// Tester les contrôleurs
try {
    $homeController = new HomeController($menuService, $statsService);
    echo "✅ HomeController créé avec succès\n";
} catch (\Exception $e) {
    echo "❌ HomeController : " . $e->getMessage() . "\n";
}

try {
    $menuController = new MenuController($menuService);
    echo "✅ MenuController créé avec succès\n";
} catch (\Exception $e) {
    echo "❌ MenuController : " . $e->getMessage() . "\n";
}

try {
    $reservationController = new ReservationController($reservationService);
    echo "✅ ReservationController créé avec succès\n";
} catch (\Exception $e) {
    echo "❌ ReservationController : " . $e->getMessage() . "\n";
}

echo "\n🎉 Tous les contrôleurs sont prêts !\n";