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

    <style>
        body {
            background: #f2f0eb;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }

        .navbar-imperial {
            background: #ffffff;
            border-bottom: 1px solid #e9e2d9;
        }

        .brand {
            color: #403226;
            font-weight: 700;
            text-decoration: none;
        }

        .brand:hover {
            color: #795b3b;
        }

        .page-title {
            color: #403226;
            font-weight: 700;
        }

        .dashboard-card {
            background: #ffffff;
            border: 1px solid #e9e2d9;
            border-radius: 16px;
            padding: 28px;
            height: 100%;
            transition: transform .2s, box-shadow .2s;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.07);
        }

        .dashboard-card h2 {
            color: #403226;
            font-size: 1.25rem;
        }

        .dashboard-card p {
            color: #786d62;
        }

        .btn-imperial {
            background: #795b3b;
            border-color: #795b3b;
            color: #ffffff;
        }

        .btn-imperial:hover {
            background: #60472e;
            border-color: #60472e;
            color: #ffffff;
        }

        .btn-outline-imperial {
            border: 1px solid #795b3b;
            color: #795b3b;
        }

        .btn-outline-imperial:hover {
            background: #795b3b;
            color: #ffffff;
        }

        .dashboard-audit-card {
            height: auto;
            padding: 24px;
        }

        .dashboard-audit-heading {
            margin-bottom: 12px;
        }

        .dashboard-audit-heading h2 {
            margin-bottom: 6px;
        }

        .dashboard-audit-heading p {
            margin-bottom: 0;
        }

        .dashboard-audit-list {
            margin-top: 16px;
        }

        .dashboard-audit-item {
            padding: 14px 0 14px 14px;
            border-bottom: 1px solid #eee8e0;
            border-left: 3px solid #b68a45;
        }

        .dashboard-audit-item:last-child {
            border-bottom: 0;
        }

        .dashboard-audit-item--success {
            border-left-color: #3c7851;
        }

        .dashboard-audit-item--danger {
            border-left-color: #a6443b;
        }

        .dashboard-audit-item__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
        }

        .dashboard-audit-module {
            color: #493024;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .dashboard-audit-date {
            color: #786d62;
            font-size: 0.8rem;
        }

        .dashboard-audit-action {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            background: #f3efe9;
            color: #695442;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .dashboard-audit-action--success {
            background: #eaf3eb;
            color: #28633c;
        }

        .dashboard-audit-action--danger {
            background: #f9ecea;
            color: #8a312c;
        }

        .dashboard-audit-details {
            display: flex;
            flex-wrap: wrap;
            gap: 4px 18px;
            margin-top: 9px;
            color: #665b50;
            font-size: 0.86rem;
            overflow-wrap: anywhere;
        }

        .dashboard-audit-notice {
            margin: 16px 0 0;
            padding: 14px 16px;
            border: 1px solid #e9e2d9;
            border-radius: 8px;
            background: #f8f6f2;
            color: #665b50;
        }

        .dashboard-audit-notice--unavailable {
            border-color: #ead2cc;
            background: #fbf0ed;
            color: #743d34;
        }

        .dashboard-audit-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid #eee8e0;
        }

        .dashboard-audit-pagination .btn {
            min-width: 104px;
        }

        .dashboard-audit-pagination .disabled {
            opacity: 0.5;
        }

        .dashboard-audit-pagination__label {
            color: #493024;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
        }

        @media (max-width: 575.98px) {
            .dashboard-audit-card {
                padding: 18px;
            }

            .dashboard-audit-item__header {
                align-items: flex-start;
                flex-direction: column;
                gap: 4px;
            }

            .dashboard-audit-pagination {
                flex-wrap: wrap;
            }

            .dashboard-audit-pagination__label {
                order: -1;
                width: 100%;
            }
        }
    </style>
</head>

<body>

<nav class="navbar navbar-imperial py-3">
    <div class="container">

        <a class="brand fs-5" href="<?= site_url('dashboard') ?>">
            Mueblería Imperial
        </a>

        <form action="<?= site_url('logout') ?>" method="post" class="mb-0">
            <?= csrf_field() ?>

            <button type="submit" class="btn btn-outline-danger btn-sm">
                Cerrar sesión
            </button>
        </form>

    </div>
</nav>

<main class="container py-5">

    <?php
    $rolId = (int) session()->get('rol_id');

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

    <div class="mb-4">
        <h1 class="page-title">
            <?= esc($panelActual['titulo']) ?>
        </h1>

        <p class="text-secondary">
            Bienvenido,
            <strong><?= esc(session()->get('nombre')) ?></strong>.
            <?= esc($panelActual['descripcion']) ?>
        </p>
    </div>

    <?php if (in_array($rolId, [1, 2], true)): ?>

        <section class="mb-5">
            <h2 class="h4 page-title mb-3">
                Resumen del negocio
            </h2>

            <div class="row g-3">

                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h2>Pedidos registrados</h2>
                        <p class="fs-2 fw-bold mb-0">
                            <?= number_format($totalPedidos ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h2>Pedidos entregados</h2>
                        <p class="fs-2 fw-bold mb-0">
                            <?= number_format($pedidosEntregados ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h2>Ventas entregadas</h2>
                        <p class="fs-2 fw-bold mb-0">
                            Q<?= number_format($ventasEntregadas ?? 0, 2) ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h2>Pagos pendientes</h2>
                        <p class="fs-2 fw-bold mb-0">
                            <?= number_format($pagosPendientes ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h2>Productos activos</h2>
                        <p class="fs-2 fw-bold mb-0">
                            <?= number_format($productosActivos ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h2>Unidades disponibles</h2>
                        <p class="fs-2 fw-bold mb-0">
                            <?= number_format($unidadesDisponibles ?? 0) ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-card">
                        <h2>Reparaciones activas</h2>
                        <p class="fs-2 fw-bold mb-0">
                            <?= number_format($reparacionesActivas ?? 0) ?>
                        </p>
                    </div>
                </div>

            </div>
        </section>





    <?php endif; ?>

    <div class="row g-4">

    <?php if ($rolId === 1): ?>
        <div class="col-md-6">
            <section class="dashboard-card">

                <div class="fs-1 mb-3">🪑</div>

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

    <?php if (in_array($rolId, [1, 2], true)): ?>
        <div class="col-md-6">
            <section class="dashboard-card">

                <div class="fs-1 mb-3">📊</div>

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

        <div class="col-md-6">
            <section class="dashboard-card">

                <div class="fs-1 mb-3">🛋️</div>

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

        <?php if ((int) session()->get('rol_id') === 4): ?>
            <div class="col-md-6">
                <section class="dashboard-card">

                    <div class="fs-1 mb-3">📦</div>

                    <h2>Mis pedidos</h2>

                    <p>
                        Consulta tus compras, revisa el estado
                        de tus pedidos y registra tus pagos.
                    </p>

                    <a href="<?= site_url('mis-pedidos') ?>"
                    class="btn btn-imperial">
                        Ver mis pedidos
                    </a>

                </section>
            </div>
        <?php endif; ?>

        <?php if ((int) session()->get('rol_id') === 4): ?>

            <div class="col-md-6">
                <section class="dashboard-card">

                    <div class="fs-1 mb-3">
                        <i class="bi bi-tools"></i>
                    </div>

                    <h2>Solicitar reparación</h2>

                    <p>
                        Solicita la evaluación gratuita de tus muebles,
                        aunque no los hayas comprado en nuestra tienda.
                    </p>

                    <a href="<?= site_url('reparaciones/nueva') ?>"
                    class="btn btn-imperial">
                        Nueva reparación
                    </a>

                </section>
            </div>

            <div class="col-md-6">
                <section class="dashboard-card">

                    <div class="fs-1 mb-3">
                        <i class="bi bi-clipboard-check"></i>
                    </div>

                    <h2>Mis reparaciones</h2>

                    <p>
                        Consulta tus solicitudes, presupuestos
                        y el estado de tus reparaciones.
                    </p>

                    <a href="<?= site_url('mis-reparaciones') ?>"
                    class="btn btn-outline-imperial">
                        Ver mis reparaciones
                    </a>

                </section>
            </div>

        <?php endif; ?>

        <?php if (in_array($rolId, [1, 2], true)): ?>
            <div class="col-md-6">
                <section class="dashboard-card">

                    <div class="fs-1 mb-3">📋</div>

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

                <div class="fs-1 mb-3">🚚</div>

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

                    <div class="fs-1 mb-3">💳</div>

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

                    <div class="fs-1 mb-3">🧾</div>

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

                    <div class="fs-1 mb-3">🔧</div>

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

</main>

<footer class="text-center text-secondary small py-4">
    &copy; <?= date('Y') ?> Mueblería Imperial
</footer>

</body>
</html>

