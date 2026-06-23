<?php
require_once __DIR__ . '/../app/Config/app.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Container;
use Dotenv\Dotenv;

// Configuration de session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_set_cookie_params([
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger l'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->load();

// Conteneur
$container = new Container();

// --- Repositories ---
$container->set(\App\Repositories\ReservationRepository::class, function($c) {
    return new \App\Repositories\ReservationRepository();
});
$container->set(\App\Repositories\CreneauRepository::class, function($c) {
    return new \App\Repositories\CreneauRepository();
});
$container->set(\App\Repositories\ClientRepository::class, function($c) {
    return new \App\Repositories\ClientRepository();
});
$container->set(\App\Repositories\PlatRepository::class, function($c) {
    return new \App\Repositories\PlatRepository();
});
$container->set(\App\Repositories\CategorieRepository::class, function($c) {
    return new \App\Repositories\CategorieRepository();
});
$container->set(\App\Repositories\AvisRepository::class, function($c) {
    return new \App\Repositories\AvisRepository();
});
$container->set(\App\Repositories\ConfigRepository::class, function($c) {
    return new \App\Repositories\ConfigRepository();
});
$container->set(\App\Repositories\UserRepository::class, function($c) {
    return new \App\Repositories\UserRepository();
});

// --- Helpers ---
$container->set(\App\Helpers\Session::class, function($c) {
    return new \App\Helpers\Session();
});
$container->set(\App\Helpers\Mailer::class, function($c) {
    return new \App\Helpers\Mailer();
});
$container->set(\App\Helpers\SmsSender::class, function($c) {
    return new \App\Helpers\SmsSender();
});

// --- Services ---
$container->set(\App\Services\MenuService::class, function($c) {
    return new \App\Services\MenuService(
        $c->get(\App\Repositories\PlatRepository::class),
        $c->get(\App\Repositories\CategorieRepository::class)
    );
});
$container->set(\App\Services\ReservationService::class, function($c) {
    return new \App\Services\ReservationService(
        $c->get(\App\Repositories\ReservationRepository::class),
        $c->get(\App\Repositories\CreneauRepository::class),
        $c->get(\App\Repositories\ClientRepository::class),
        $c->get(\App\Helpers\Mailer::class),
        $c->get(\App\Helpers\SmsSender::class),
        $c->get(\App\Helpers\Session::class)
    );
});
$container->set(\App\Services\StatsService::class, function($c) {
    return new \App\Services\StatsService(
        $c->get(\App\Repositories\ReservationRepository::class),
        $c->get(\App\Repositories\PlatRepository::class),
        $c->get(\App\Repositories\AvisRepository::class),
        $c->get(\App\Repositories\ConfigRepository::class)
    );
});
$container->set(\App\Services\UploadService::class, function($c) {
    return new \App\Services\UploadService();
});
$container->set(\App\Services\ConfigService::class, function($c) {
    return new \App\Services\ConfigService(
        $c->get(\App\Repositories\ConfigRepository::class)
    );
});

// --- Contrôleurs Admin ---
$container->set(\App\Controllers\Admin\AuthController::class, function($c) {
    return new \App\Controllers\Admin\AuthController(
        $c->get(\App\Repositories\UserRepository::class)
    );
});
$container->set(\App\Controllers\Admin\DashboardController::class, function($c) {
    return new \App\Controllers\Admin\DashboardController(
        $c->get(\App\Services\StatsService::class),
        $c->get(\App\Services\ReservationService::class)
    );
});
$container->set(\App\Controllers\Admin\ReservationController::class, function($c) {
    return new \App\Controllers\Admin\ReservationController(
        $c->get(\App\Services\ReservationService::class)
    );
});
$container->set(\App\Controllers\Admin\MenuController::class, function($c) {
    return new \App\Controllers\Admin\MenuController(
        $c->get(\App\Services\MenuService::class),
        $c->get(\App\Services\UploadService::class)
    );
});
$container->set(\App\Controllers\Admin\ConfigController::class, function($c) {
    return new \App\Controllers\Admin\ConfigController(
        $c->get(\App\Services\ConfigService::class)
    );
});

// --- Routeur ---
$router = new Router($container);

// Charger les routes
require_once __DIR__ . '/../app/Routes/web.php';

// Lancer
$router->dispatch();