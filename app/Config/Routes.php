<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Rutas públicas
$routes->get('/', 'Inicio::index');
$routes->get('catalogo', 'CatalogoController::index');

// Autenticación
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::autenticar');

// Rutas protegidas
$routes->get('dashboard', 'DashboardController::index', [
    'filter' => 'auth'
]);

// Cierre de sesión
$routes->post('logout', 'AuthController::logout', [
    'filter' => 'auth'
]);
