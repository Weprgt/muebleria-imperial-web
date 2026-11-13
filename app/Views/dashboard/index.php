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

    </div>

</main>

<footer class="text-center text-secondary small py-4">
    &copy; <?= date('Y') ?> Mueblería Imperial
</footer>

</body>
</html>

