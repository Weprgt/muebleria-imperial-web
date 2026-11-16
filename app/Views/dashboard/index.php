<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Panel de control - Mueblería Imperial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="<?= base_url('assets/css/estilos.css') ?>"
        rel="stylesheet"
    >

    <?php
    $rolId = (int) session()->get('rol_id');
    $nombreUsuario = trim((string) session()->get('nombre'));

    $paneles = [
        1 => [
            'titulo' => 'Panel de Administración',
            'descripcion' => 'Administra tu tienda desde un solo lugar.',
        ],
        2 => [
            'titulo' => 'Panel de Ventas',
            'descripcion' => 'Gestiona pedidos, pagos y entregas.',
        ],
        3 => [
            'titulo' => 'Panel de Taller',
            'descripcion' => 'Gestiona las solicitudes y reparaciones de muebles.',
        ],
        4 => [
            'titulo' => 'Panel del Cliente',
            'descripcion' => 'Consulta tus compras y solicita reparaciones.',
        ],
    ];

    $panelActual = $paneles[$rolId] ?? [
        'titulo' => 'Panel de control',
        'descripcion' => 'Bienvenido a Mueblería Imperial.',
    ];
    ?>
</head>

<body class="imperial-dashboard<?= $rolId === 4 ? ' imperial-dashboard-client' : ' imperial-dashboard-management' ?>">

<div class="dashboard-shell">
    <nav class="navbar dashboard-navbar py-3">
        <div class="container">

            <a class="dashboard-brand" href="<?= site_url('dashboard') ?>">
                <img
                    class="dashboard-brand-logo"
                    src="<?= base_url('assets/img/logo-imperial.png') ?>"
                    alt=""
                    aria-hidden="true"
                    width="56"
                    height="56"
                >
                <span>Mueblería Imperial</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <span class="dashboard-role">
                    <?= esc($panelActual['titulo'] ?? 'Panel de control') ?>
                </span>

                <form action="<?= site_url('logout') ?>" method="post" class="mb-0">
                    <?= csrf_field() ?>

                    <button type="submit"
                            class="btn btn-outline-imperial dashboard-logout btn-sm">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
</nav>

<main class="container dashboard-main">

    <?php if ($rolId === 4): ?>
        <section class="customer-dashboard__hero"
                 aria-labelledby="customer-dashboard-title">
            <div class="customer-dashboard__content">
                <p class="dashboard-eyebrow">Muebles y cuidado para tu hogar</p>
                <h1 id="customer-dashboard-title">
                    Bienvenido a tu espacio Imperial
                </h1>
                <p class="customer-dashboard__intro">
                    Hola,
                    <strong><?= $nombreUsuario !== '' ? esc($nombreUsuario) : 'Cliente' ?></strong>.
                    Explora muebles para tus espacios y encuentra servicios para cuidarlos.
                </p>

                <div class="customer-dashboard__actions">
                    <a href="<?= site_url('catalogo') ?>"
                       class="btn btn-imperial customer-dashboard__button">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M4 10.5 12 4l8 6.5"></path>
                            <path d="M6.5 9.5V20h11V9.5M9.5 20v-6h5v6"></path>
                        </svg>
                        Explorar catálogo
                    </a>
                    <a href="<?= site_url('carrito') ?>"
                       class="btn btn-outline-imperial customer-dashboard__button">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h8.4a2 2 0 0 0 1.9-1.4L21 8H6"></path>
                            <circle cx="10" cy="20" r="1"></circle>
                            <circle cx="18" cy="20" r="1"></circle>
                        </svg>
                        Mi carrito
                    </a>
                </div>
            </div>

            <div class="customer-dashboard__image-frame">
                <img
                    class="customer-dashboard__image"
                    src="<?= base_url('assets/img/sala-imperial-dashboard.png') ?>"
                    alt=""
                    loading="lazy"
                    decoding="async"
                >
                <span class="customer-dashboard__image-caption">
                    Diseñado para disfrutar cada espacio
                </span>
            </div>
        </section>
    <?php else: ?>
    <section class="dashboard-welcome" aria-labelledby="dashboard-title">
        <p class="dashboard-eyebrow">Mueblería Imperial</p>
        <h1 id="dashboard-title">
            <?= esc($panelActual['titulo']) ?>
        </h1>

        <p>
            Bienvenido,
            <strong><?= $nombreUsuario !== '' ? esc($nombreUsuario) : 'Usuario' ?></strong>.
            <?= esc($panelActual['descripcion']) ?>
        </p>
    </section>
    <?php endif; ?>

    <?php if (in_array($rolId, [1, 2], true)): ?>

        <section class="dashboard-section" aria-labelledby="business-summary-heading">
            <h2 id="business-summary-heading" class="dashboard-section-heading">
                Resumen del negocio
            </h2>

            <div class="row g-3">

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Pedidos registrados</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($totalPedidos ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Pedidos entregados</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($pedidosEntregados ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Ventas entregadas</span>
                        <p class="dashboard-stat-card__value">
                            Q<?= number_format($ventasEntregadas ?? 0, 2) ?>
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Pagos pendientes</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($pagosPendientes ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Productos activos</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($productosActivos ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Unidades disponibles</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($unidadesDisponibles ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Reparaciones activas</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($reparacionesActivas ?? 0) ?>
                        </p>
                    </div>
                </div>

            </div>
        </section>





    <?php endif; ?>

    <?php if ($rolId === 3): ?>
        <section class="dashboard-section" aria-labelledby="workshop-summary-heading">
            <h2 id="workshop-summary-heading" class="dashboard-section-heading">
                Estado de las reparaciones
            </h2>

            <div class="row g-3">
                <div class="col-12 col-sm-4">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Pendientes de atención</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($tallerPendientes) ?>
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">En reparación</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($tallerEnReparacion) ?>
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="dashboard-stat-card">
                        <span class="dashboard-stat-card__label">Finalizadas</span>
                        <p class="dashboard-stat-card__value">
                            <?= number_format($tallerFinalizadas) ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($rolId !== 4): ?>
    <div class="row g-4">

    <?php if ($rolId === 1): ?>
        <div class="col-md-6">
            <section class="dashboard-card">

                <span class="dashboard-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M5 11V8a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v3"></path>
                        <path d="M3 19v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6M5 15v4m14-4v4M3 11h18"></path>
                    </svg>
                </span>

                <h2>Administración de muebles</h2>

                <p>
                    Registra nuevos muebles, modifica precios,
                    administra existencias y activa o desactiva
                    productos del catálogo.
                </p>

                <a href="<?= site_url('admin/muebles') ?>"
                class="btn btn-imperial">
                    Administrar muebles
                </a>

            </section>
        </div>
    <?php endif; ?>

    <?php if ($rolId === 1): ?>
        <div class="col-md-6">
            <section class="dashboard-card">
                <span class="dashboard-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <rect x="4" y="4" width="7" height="7" rx="1"></rect>
                        <rect x="13" y="4" width="7" height="7" rx="1"></rect>
                        <rect x="4" y="13" width="7" height="7" rx="1"></rect>
                        <rect x="13" y="13" width="7" height="7" rx="1"></rect>
                    </svg>
                </span>
                <h2>Administración de categorías</h2>
                <p>Administra las categorías disponibles en el catálogo.</p>
                <a href="<?= site_url('admin/categorias') ?>"
                   class="btn btn-imperial">
                    Administrar categorías
                </a>
            </section>
        </div>

        <div class="col-md-6">
            <section class="dashboard-card">
                <span class="dashboard-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <circle cx="9" cy="8" r="3"></circle>
                        <path d="M3.5 20v-1a5.5 5.5 0 0 1 11 0v1zM16 5.5a3 3 0 0 1 0 5.8M17 14a4.5 4.5 0 0 1 3.5 4.4v1"></path>
                    </svg>
                </span>
                <h2>Administración de empleados</h2>
                <p>Consulta y gestiona las cuentas de Ventas y Taller.</p>
                <a href="<?= site_url('admin/usuarios') ?>"
                   class="btn btn-imperial">
                    Administrar empleados
                </a>
            </section>
        </div>
    <?php endif; ?>

    <?php if (in_array($rolId, [1, 2], true)): ?>
        <div class="col-md-6">
            <section class="dashboard-card">

                <span class="dashboard-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M4 20V4m0 16h17"></path>
                        <rect x="7" y="12" width="3" height="5" rx=".5"></rect>
                        <rect x="13" y="8" width="3" height="9" rx=".5"></rect>
                        <rect x="19" y="5" width="2" height="12" rx=".5"></rect>
                    </svg>
                </span>

                <h2>Reportes administrativos</h2>

                <p>
                    Consulta los reportes de pedidos,
                    inventario y reparaciones de la tienda.
                </p>

                <a href="<?= site_url('reportes') ?>"
                class="btn btn-imperial">
                    Ver reportes
                </a>

            </section>
        </div>
    <?php endif; ?>

    <?php if (in_array($rolId, [1, 2], true)): ?>
        <div class="col-md-6">
            <section class="dashboard-card">
                <span class="dashboard-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M4 10V5l-3 3 3 3"></path>
                        <path d="M4 8h9a7 7 0 1 1-6.2 10.2"></path>
                    </svg>
                </span>
                <h2>Gestión de reembolsos</h2>
                <p>Consulta y gestiona los reembolsos de pedidos cancelados.</p>
                <a href="<?= site_url('gestion-reembolsos') ?>"
                   class="btn btn-imperial">
                    Revisar reembolsos
                </a>
            </section>
        </div>
    <?php endif; ?>

        <div class="col-md-6">
            <section class="dashboard-card">

                <span class="dashboard-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M5 11V8a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v3"></path>
                        <path d="M3 19v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6M5 15v4m14-4v4M3 11h18"></path>
                    </svg>
                </span>

                <h2>Catálogo de muebles</h2>

                <p>
                    Explora los productos disponibles en nuestra tienda.
                </p>

                <a href="<?= site_url('catalogo') ?>"
                   class="btn btn-imperial">
                    Ver catálogo
                </a>

            </section>
        </div>

        <?php if (in_array($rolId, [1, 2], true)): ?>
            <div class="col-md-6">
                <section class="dashboard-card">

                    <span class="dashboard-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false">
                            <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                            <path d="M9 4.5h6M8 11h8M8 15h8M8 18h5"></path>
                        </svg>
                    </span>

                    <h2>Gestión de cancelaciones</h2>

                    <p>
                        Revisa las solicitudes de cancelación
                        de pedidos en preparación y decide
                        si deben aprobarse o rechazarse.
                    </p>

                    <a href="<?= site_url('gestion-cancelaciones') ?>"
                       class="btn btn-imperial">
                        Revisar cancelaciones
                    </a>

                </section>
            </div>
        <?php endif; ?>

    <?php if (in_array((int) session()->get('rol_id'), [1, 2], true)): ?>
        <div class="col-md-6">
            <section class="dashboard-card">

                <span class="dashboard-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M3 6h11v11H3zM14 10h4l3 3v4h-7z"></path>
                        <circle cx="7.5" cy="18.5" r="1.5"></circle>
                        <circle cx="18" cy="18.5" r="1.5"></circle>
                    </svg>
                </span>

                <h2>Entregas pendientes</h2>

                <p>
                    Consulta los pedidos confirmados y gestiona
                    las entregas de los clientes.
                </p>

                <a href="<?= site_url('entregas/pendientes') ?>"
                class="btn btn-imperial">
                    Gestionar entregas
                </a>

            </section>
        </div>

            <div class="col-md-6">
                <section class="dashboard-card">

                    <span class="dashboard-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="M3 10h18M7 15h4"></path>
                        </svg>
                    </span>

                    <h2>Pagos pendientes</h2>

                    <p>
                        Revisa los anticipos reportados por los clientes
                        y aprueba o rechaza sus pagos.
                    </p>


                    <a href="<?= site_url('pagos/pendientes') ?>"
                    class="btn btn-imperial">
                        Revisar pagos pendientes
                    </a>

                </section>
            </div>

        <?php endif; ?>

        <?php if (in_array((int) session()->get('rol_id'), [1, 2], true)): ?>

            <div class="col-md-6">
                <section class="dashboard-card">

                    <span class="dashboard-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false">
                            <path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"></path>
                            <path d="M9 8h6M9 12h6M9 16h3"></path>
                        </svg>
                    </span>

                    <h2>Pagos de reparaciones</h2>

                    <p>
                        Consulta los anticipos de reparaciones
                        reportados por los clientes y verifica
                        sus pagos pendientes.
                    </p>

                    <a href="<?= site_url('reparaciones/pagos/pendientes') ?>"
                    class="btn btn-imperial">
                        Revisar pagos de reparaciones
                    </a>

                </section>
            </div>

        <?php endif; ?>

        <?php if (in_array((int) session()->get('rol_id'), [1, 3], true)): ?>

            <div class="col-md-6">
                <section class="dashboard-card">

                    <span class="dashboard-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false">
                            <path d="M14.5 6.5a5 5 0 0 0-6.8 6.8L3 18l3 3 4.7-4.7a5 5 0 0 0 6.8-6.8l-3 3-3-3z"></path>
                        </svg>
                    </span>

                    <h2>Gestión de reparaciones</h2>

                    <p>
                        Consulta las solicitudes de los clientes,
                        realiza evaluaciones y gestiona
                        los presupuestos de reparación.
                    </p>

                    <a href="<?= site_url('taller/reparaciones') ?>"
                    class="btn btn-imperial">
                        Gestionar reparaciones
                    </a>

                </section>



            </div>

        <?php endif; ?>
        <?php if ((int) session()->get('rol_id') === 1): ?>
            <div class="col-12">
                <section class="dashboard-card dashboard-audit-card"
                         aria-labelledby="audit-heading">
                    <div class="dashboard-audit-heading">
                        <h2 id="audit-heading">Actividad reciente del sistema</h2>
                        <p>Últimos eventos registrados en MongoDB.</p>
                    </div>

                    <?php if (empty($auditoriaDisponible)): ?>
                        <p class="dashboard-audit-notice dashboard-audit-notice--unavailable"
                           role="status">
                            La actividad de auditoría no está disponible en este momento.
                        </p>
                    <?php elseif (empty($actividadReciente)): ?>
                        <p class="dashboard-audit-notice" role="status">
                            No hay eventos de auditoría disponibles.
                        </p>
                    <?php else: ?>
                        <div class="dashboard-audit-list">
                            <?php foreach ($actividadReciente as $evento): ?>
                                <?php
                                $detalles = $evento['datos'] ?? [];
                                $pedidoId = (int) ($detalles['pedido_id'] ?? 0);
                                $nombreCliente = $clientesAuditoria[$pedidoId] ?? null;
                                $mueblesPedido = $mueblesAuditoria[$pedidoId] ?? [];
                                $accion = (string) ($evento['accion'] ?? 'Evento');
                                $accionLegible = ucfirst(
                                    mb_strtolower(
                                        str_replace('_', ' ', $accion),
                                        'UTF-8'
                                    )
                                );
                                $accionNormalizada = mb_strtoupper($accion, 'UTF-8');
                                $accionTono = 'neutral';

                                if (
                                    str_contains($accionNormalizada, 'APROBAD')
                                    || str_contains($accionNormalizada, 'VERIFICAD')
                                    || str_contains($accionNormalizada, 'ENTREGAD')
                                ) {
                                    $accionTono = 'success';
                                } elseif (str_contains($accionNormalizada, 'RECHAZAD')) {
                                    $accionTono = 'danger';
                                }

                                $estadoActual = $detalles['estado_nuevo']
                                    ?? $detalles['estado']
                                    ?? null;
                                $estadoPedido = $detalles['estado_pedido'] ?? null;
                                $estadoAnterior = $detalles['estado_anterior'] ?? null;
                                ?>

                                <article class="dashboard-audit-item dashboard-audit-item--<?= esc($accionTono, 'attr') ?>">
                                    <div class="dashboard-audit-item__header">
                                        <span class="dashboard-audit-module">
                                            <?= esc($evento['modulo'] ?? 'Sistema') ?>
                                        </span>

                                        <?php if (isset($evento['fecha'])): ?>
                                            <span class="dashboard-audit-date">
                                                <?= esc(
                                                    $evento['fecha']
                                                        ->toDateTime()
                                                        ->setTimezone(
                                                            new \DateTimeZone('America/Guatemala')
                                                        )
                                                        ->format('d/m/Y H:i:s')
                                                ) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <span class="dashboard-audit-action dashboard-audit-action--<?= esc($accionTono, 'attr') ?>">
                                        <?= esc($accionLegible) ?>
                                    </span>

                                    <div class="dashboard-audit-details">
                                        <?php if ($nombreCliente !== null): ?>
                                            <span>
                                                <strong>Cliente:</strong>
                                                <?= esc($nombreCliente) ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($mueblesPedido)): ?>
                                            <span>
                                                <strong>Muebles:</strong>
                                                <?= esc(implode(', ', $mueblesPedido)) ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (isset($detalles['pedido_id'])): ?>
                                            <span>
                                                <strong>Pedido:</strong>
                                                #<?= esc($detalles['pedido_id']) ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (isset($detalles['solicitud_id'])): ?>
                                            <span>
                                                <strong>Solicitud:</strong>
                                                #<?= esc($detalles['solicitud_id']) ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($estadoActual !== null): ?>
                                            <span>
                                                <strong>Estado:</strong>
                                                <?= esc(
                                                    ucfirst(
                                                        mb_strtolower(
                                                            str_replace('_', ' ', (string) $estadoActual),
                                                            'UTF-8'
                                                        )
                                                    )
                                                ) ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($estadoPedido !== null): ?>
                                            <span>
                                                <strong>Estado del pedido:</strong>
                                                <?= esc(
                                                    ucfirst(
                                                        mb_strtolower(
                                                            str_replace('_', ' ', (string) $estadoPedido),
                                                            'UTF-8'
                                                        )
                                                    )
                                                ) ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($estadoAnterior !== null): ?>
                                            <span>
                                                <strong>Estado anterior:</strong>
                                                <?= esc(
                                                    ucfirst(
                                                        mb_strtolower(
                                                            str_replace('_', ' ', (string) $estadoAnterior),
                                                            'UTF-8'
                                                        )
                                                    )
                                                ) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($auditoriaTotalPaginas > 0): ?>
                            <nav class="dashboard-audit-pagination"
                                 aria-label="Paginación de actividad reciente">
                                <?php if ($auditoriaPagina > 1): ?>
                                    <a class="btn btn-sm btn-outline-imperial"
                                       href="<?= esc(site_url('dashboard') . '?auditoria_pagina=' . ($auditoriaPagina - 1), 'attr') ?>">
                                        Anterior
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-sm btn-outline-imperial disabled"
                                          aria-disabled="true">
                                        Anterior
                                    </span>
                                <?php endif; ?>

                                <span class="dashboard-audit-pagination__label">
                                    Página <?= esc($auditoriaPagina) ?>
                                    de <?= esc($auditoriaTotalPaginas) ?>
                                </span>

                                <?php if ($auditoriaPagina < $auditoriaTotalPaginas): ?>
                                    <a class="btn btn-sm btn-outline-imperial"
                                       href="<?= esc(site_url('dashboard') . '?auditoria_pagina=' . ($auditoriaPagina + 1), 'attr') ?>">
                                        Siguiente
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-sm btn-outline-imperial disabled"
                                          aria-disabled="true">
                                        Siguiente
                                    </span>
                                <?php endif; ?>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </section>
            </div>

        <?php endif; ?>

    </div>
    <?php else: ?>
        <section class="customer-dashboard__account"
                 aria-labelledby="customer-account-heading">
            <div class="customer-dashboard__account-heading">
                <p class="dashboard-eyebrow">Todo en un solo lugar</p>
                <h2 id="customer-account-heading">Tu cuenta</h2>
                <p>Da seguimiento a tus compras y servicios para el hogar.</p>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <section class="dashboard-card customer-dashboard__account-card">
                        <span class="dashboard-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false">
                                <path d="M7 3h7l4 4v14H7z"></path>
                                <path d="M14 3v5h5M10 13h5M10 17h5"></path>
                            </svg>
                        </span>
                        <h3>Mis pedidos</h3>
                        <p>
                            Consulta el estado de tus compras y el seguimiento de tus pagos.
                        </p>
                        <a href="<?= site_url('mis-pedidos') ?>"
                           class="btn btn-outline-imperial">
                            Ver mis pedidos
                        </a>
                    </section>
                </div>

                <div class="col-12 col-md-4">
                    <section class="dashboard-card customer-dashboard__account-card">
                        <span class="dashboard-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false">
                                <path d="M14.5 6.5a5 5 0 0 0-6.8 6.8L3 18l3 3 4.7-4.7a5 5 0 0 0 6.8-6.8l-3 3-3-3z"></path>
                            </svg>
                        </span>
                        <h3>Solicitar reparación</h3>
                        <p>
                            Solicita la evaluación de tus muebles para conocer las opciones de reparación.
                        </p>
                        <a href="<?= site_url('reparaciones/nueva') ?>"
                           class="btn btn-outline-imperial">
                            Nueva solicitud
                        </a>
                    </section>
                </div>

                <div class="col-12 col-md-4">
                    <section class="dashboard-card customer-dashboard__account-card">
                        <span class="dashboard-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false">
                                <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                <path d="M9 4.5h6M8 11h8M8 15h5"></path>
                                <path d="m14 18 2 2 4-5"></path>
                            </svg>
                        </span>
                        <h3>Mis reparaciones</h3>
                        <p>
                            Revisa tus solicitudes, presupuestos y el avance de cada reparación.
                        </p>
                        <a href="<?= site_url('mis-reparaciones') ?>"
                           class="btn btn-outline-imperial">
                            Ver mis reparaciones
                        </a>
                    </section>
                </div>
            </div>
        </section>
    <?php endif; ?>

</main>

<footer class="dashboard-footer">
    &copy; <?= date('Y') ?> Mueblería Imperial
</footer>
</div>

</body>
</html>

