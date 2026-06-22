<?php
use App\Core\Router;

/** @var Router $router */

// ========== ROUTES VISITEUR ==========
$router->get('/', 'Visitor\HomeController@index');
$router->get('/menu', 'Visitor\MenuController@index');
$router->get('/reservation', 'Visitor\ReservationController@index');
$router->get('/contact', 'Visitor\ContactController@index');

// ========== ROUTES API (AJAX) ==========
$router->post('/api/disponibilites', 'Visitor\ReservationController@getDisponibilites');
$router->post('/api/reservation/confirmer', 'Visitor\ReservationController@confirmer');

// Route de test BDD
$router->get('/test-db', 'Visitor\TestController@db');

$router->get('/test-helpers', 'Visitor\TestController@index');

// ========== ROUTES ADMIN (AUTH) ==========
$router->get('/admin/login', 'Admin\AuthController@loginForm');
$router->post('/admin/login', 'Admin\AuthController@login');
$router->get('/admin/logout', 'Admin\AuthController@logout');

// ========== ROUTES ADMIN (PROTÉGÉES) ==========
$router->get('/admin', 'Admin\DashboardController@index');

// ========== ROUTES ADMIN RÉSERVATIONS ==========
$router->get('/admin/reservations', 'Admin\ReservationController@index');
$router->post('/admin/reservations/annuler', 'Admin\ReservationController@annuler');
$router->post('/admin/reservations/changer-statut', 'Admin\ReservationController@changerStatut');

// ========== ROUTES ADMIN MENU ==========
$router->get('/admin/menu', 'Admin\MenuController@index');
$router->get('/admin/menu/create', 'Admin\MenuController@create');
$router->post('/admin/menu', 'Admin\MenuController@store');
$router->get('/admin/menu/edit/{id}', 'Admin\MenuController@edit');
$router->post('/admin/menu/update/{id}', 'Admin\MenuController@update');
$router->post('/admin/menu/delete', 'Admin\MenuController@delete');

// ========== ROUTES ADMIN CONFIGURATION ==========
$router->get('/admin/config', 'Admin\ConfigController@index');
$router->post('/admin/config', 'Admin\ConfigController@update');