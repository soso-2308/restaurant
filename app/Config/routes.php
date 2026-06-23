<?php
/**
 * Routes de l'application
 * @var \App\Core\Router $router
 */

// ====================
// ROUTES VISITEUR (WEB)
// ====================
$router->get('/', 'Web\HomeController@index');
$router->get('/menu', 'Web\MenuController@index');
$router->get('/reservation', 'Web\ReservationController@index');
$router->get('/contact', 'Web\ContactController@index');

// ====================
// ROUTES API (AJAX / REST)
// ====================
$router->post('/api/disponibilites', 'API\ReservationApiController@getDisponibilites');
$router->post('/api/reservation/confirmer', 'API\ReservationApiController@confirmer');

// ====================
// ROUTES ADMIN (AUTH)
// ====================
$router->get('/admin/login', 'Web\Admin\AuthController@loginForm');
$router->post('/admin/login', 'Web\Admin\AuthController@login');
$router->get('/admin/logout', 'Web\Admin\AuthController@logout');

// ====================
// ROUTES ADMIN (PROTÉGÉES)
// ====================

// Dashboard
$router->get('/admin', 'Web\Admin\DashboardController@index');

// Réservations
$router->get('/admin/reservations', 'Web\Admin\ReservationController@index');
$router->post('/admin/reservations/annuler', 'Web\Admin\ReservationController@annuler');
$router->post('/admin/reservations/changer-statut', 'Web\Admin\ReservationController@changerStatut');
$router->get('/admin/reservations/export/pdf', 'Web\Admin\ReservationController@exportPdf');
$router->get('/admin/reservations/export/excel', 'Web\Admin\ReservationController@exportExcel');

// Menu (plats)
$router->get('/admin/menu', 'Web\Admin\MenuController@index');
$router->get('/admin/menu/create', 'Web\Admin\MenuController@create');
$router->post('/admin/menu', 'Web\Admin\MenuController@store');
$router->get('/admin/menu/edit/{id}', 'Web\Admin\MenuController@edit');
$router->post('/admin/menu/update/{id}', 'Web\Admin\MenuController@update');
$router->post('/admin/menu/delete', 'Web\Admin\MenuController@delete');

// Catégories (GESTION COMPLÈTE)
$router->get('/admin/categories', 'Web\Admin\CategorieController@index');
$router->get('/admin/categories/create', 'Web\Admin\CategorieController@create');
$router->post('/admin/categories', 'Web\Admin\CategorieController@store');
$router->get('/admin/categories/edit/{id}', 'Web\Admin\CategorieController@edit');
$router->post('/admin/categories/update/{id}', 'Web\Admin\CategorieController@update');
$router->post('/admin/categories/delete', 'Web\Admin\CategorieController@delete');
$router->get('/admin/categories/export/pdf', 'Web\Admin\CategorieController@exportPdf');
$router->get('/admin/categories/export/excel', 'Web\Admin\CategorieController@exportExcel');

// Configuration
$router->get('/admin/config', 'Web\Admin\ConfigController@index');
$router->post('/admin/config', 'Web\Admin\ConfigController@update');

$router->get('/admin/reservations/export/pdf', 'Web\Admin\ReservationController@exportPdf');
$router->get('/admin/reservations/export/excel', 'Web\Admin\ReservationController@exportExcel');

// Clients
$router->get('/admin/clients', 'Web\Admin\ClientController@index');
$router->get('/admin/clients/show/{id}', 'Web\Admin\ClientController@show');
$router->get('/admin/clients/export/pdf', 'Web\Admin\ClientController@exportPdf');
$router->get('/admin/clients/export/excel', 'Web\Admin\ClientController@exportExcel');