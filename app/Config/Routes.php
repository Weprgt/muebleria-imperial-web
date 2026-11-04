<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Inicio::index');
$routes->get('catalogo', 'CatalogoController::index');