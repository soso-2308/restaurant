<?php
require_once __DIR__ . '/../vendor/autoload.php';

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

echo "🧪 Test des Services\n";
echo "--------------------\n\n";

// --- 1. MenuService ---
$platRepo = new PlatRepository();
$categorieRepo = new CategorieRepository();
$menuService = new MenuService($platRepo, $categorieRepo);

$categories = $menuService->getCategoriesActives();
echo "1️⃣ MenuService\n";
echo "   ✅ Catégories actives : " . count($categories) . "\n";

$populaires = $menuService->getPlatsPopulaires(3);
echo "   ✅ Plats populaires : " . count($populaires) . "\n\n";

// --- 2. ReservationService ---
$reservationRepo = new ReservationRepository();
$creneauRepo = new CreneauRepository();
$clientRepo = new ClientRepository();
$session = new Session();
$mailer = new Mailer();
$smsSender = new SmsSender();

$reservationService = new ReservationService(
    $reservationRepo,
    $creneauRepo,
    $clientRepo,
    $mailer,
    $smsSender,
    $session
);

$date = date('Y-m-d');
$disponibles = $reservationService->getCreneauxDisponibles($date);
echo "2️⃣ ReservationService\n";
echo "   ✅ Créneaux disponibles : " . count($disponibles) . "\n\n";

// --- 3. StatsService ---
$avisRepo = new AvisRepository();
$configRepo = new ConfigRepository();
$statsService = new StatsService(
    $reservationRepo,
    $platRepo,
    $avisRepo,
    $configRepo
);

$stats = $statsService->getGeneralStats();
echo "3️⃣ StatsService\n";
echo "   ✅ Réservations du mois : " . $stats['reservations_mois'] . "\n";
echo "   ✅ Couverts servis : " . $stats['couverts_mois'] . "\n";
echo "   ✅ Croissance : " . $stats['croissance'] . "%\n";
echo "   ✅ Taux d'occupation aujourd'hui : " . $stats['taux_occupation'] . "%\n";

$evolution = $statsService->getReservationsEvolution(7);
echo "   ✅ Évolution sur 7 jours : " . implode(' → ', $evolution['values']) . "\n";

$topPlats = $statsService->getTopPlats(3);
echo "   ✅ Top plats : " . count($topPlats) . " trouvés\n";

echo "\n🎉 Tous les services fonctionnent !\n";