<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Rutas públicas
$routes->get('/', 'Inicio::index');
$routes->get('catalogo', 'CatalogoController::index');

// Autenticación
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::autenticar');

// Registro público de clientes
$routes->get('registro', 'RegistroController::index');
$routes->post('registro', 'RegistroController::guardar');

// Rutas protegidas
$routes->get('dashboard', 'DashboardController::index', [
    'filter' => 'auth'
]);

// Cierre de sesión
$routes->post('logout', 'AuthController::logout', [
    'filter' => 'auth'
]);

// Páginas exclusivas por rol
$routes->get('admin', 'PanelRolController::administrador', [
    'filter' => 'role:1'
]);

$routes->get('ventas', 'PanelRolController::ventas', [
    'filter' => 'role:2'
]);

$routes->get('taller', 'PanelRolController::taller', [
    'filter' => 'role:3'
]);

$routes->get('cliente', 'PanelRolController::cliente', [
    'filter' => 'role:4'
]);

$routes->get('admin/muebles', 'AdminMueblesController::index', [
    'filter' => 'role:1']
);
$routes->get('admin/muebles/agregar', 'AdminMueblesController::agregar', [
    'filter' => 'role:1']);

$routes->post('admin/muebles/guardar', 'AdminMueblesController::guardar', [
    'filter' => 'role:1']);

$routes->get('admin/muebles/editar/(:num)', 'AdminMueblesController::editar/$1', [
    'filter' => 'role:1']);

$routes->post('admin/muebles/actualizar/(:num)', 
    'AdminMueblesController::actualizar/$1', [
    'filter' => 'role:1']);

$routes->post('admin/muebles/cambiar-estado/(:num)', 
    'AdminMueblesController::cambiarEstado/$1', [
    'filter' => 'role:1']);

// Administración de categorías
$routes->get('admin/categorias', 
    'AdminCategoriasController::index', [
    'filter' => 'role:1']);

$routes->get('admin/categorias/agregar', 
    'AdminCategoriasController::agregar', [
    'filter' => 'role:1']);

$routes->post('admin/categorias/guardar', 
    'AdminCategoriasController::guardar', [
    'filter' => 'role:1']);

$routes->get('admin/categorias/editar/(:num)', 
    'AdminCategoriasController::editar/$1', [
    'filter' => 'role:1']);

$routes->post('admin/categorias/actualizar/(:num)', 
    'AdminCategoriasController::actualizar/$1', [
    'filter' => 'role:1']);

$routes->post('admin/categorias/desactivar/(:num)', 
    'AdminCategoriasController::desactivar/$1', [
    'filter' => 'role:1']);

$routes->post('admin/categorias/reactivar/(:num)', 
    'AdminCategoriasController::reactivar/$1', [
    'filter' => 'role:1']);

$routes->get('carrito', 'CarritoController::index');
$routes->post('carrito/agregar', 'CarritoController::agregar');
$routes->post('carrito/eliminar', 'CarritoController::eliminar');
$routes->post('carrito/actualizar', 'CarritoController::actualizar');

// Confirmacion de pedidos: exclusivamente clientes autenticados
$routes->post('pedidos/confirmar', 'PedidoController::confirmar', [
    'filter' => 'role:4'
]);

// Registro de pagos: exclusivamente clientes autenticados
$routes->post('pagos/registrar', 'PagoPedidoController::registrar', [
    'filter' => 'role:4'
]);

// Consulta de pedidos del cliente autenticado
$routes->get('mis-pedidos', 'MisPedidosController::index', [
    'filter' => 'role:4'
]);

// Consulta de pagos pendientes: Administrador y Ventas
$routes->get('pagos/pendientes', 'VerificacionPagosController::index', [
    'filter' => 'role:1,2'
]);

// Aprobar anticipos: Administrador y Ventas
$routes->post('pagos/(:num)/aprobar', 'VerificacionPagosController::aprobar/$1', [
    'filter' => 'role:1,2'
]);

// Rechazar anticipos: Administrador y Ventas
$routes->post('pagos/(:num)/rechazar', 'VerificacionPagosController::rechazar/$1', [
    'filter' => 'role:1,2'
]);
