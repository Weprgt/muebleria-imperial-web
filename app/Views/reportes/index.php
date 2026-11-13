<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Reportes - Mueblería Imperial</title>

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

        .brand, .page-title {
            color: #403226;
            font-weight: 700;
        }

        .brand {
            text-decoration: none;
        }

        .report-card {
            background: #ffffff;
            border: 1px solid #e9e2d9;
            border-radius: 16px;
            padding: 24px;
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
    </style>
</head>

<body>

<nav class="navbar navbar-imperial py-3">
    <div class="container">
        <a class="brand fs-5" href="<?= site_url('dashboard') ?>">
            Mueblería Imperial
        </a>

        <a href="<?= site_url('dashboard') ?>"
           class="btn btn-outline-secondary btn-sm">
            Volver al dashboard
        </a>
    </div>
</nav>

<main class="container py-5">

    <div class="mb-4">
        <h1 class="page-title">Reportes administrativos</h1>
        <p class="text-secondary">
            Consulta los resultados de ventas, inventario y reparaciones.
        </p>
    </div>

    <section class="report-card mb-4">
        <h2 class="h4 page-title mb-3">Pedidos por estado</h2>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th class="text-end">Pedidos</th>
                        <th class="text-end">Importe total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($pedidosPorEstado ?? []) as $fila): ?>
                        <tr>
                            <td><?= esc($fila['estado']) ?></td>
                            <td class="text-end">
                                <?= number_format((int) $fila['cantidad']) ?>
                            </td>
                            <td class="text-end">
                                Q<?= number_format((float) $fila['monto'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($pedidosPorEstado)): ?>
                        <tr>
                            <td colspan="3" class="text-center">
                                No hay pedidos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="small text-secondary mb-0">
            Los importes representan el valor de los pedidos por estado.
            Los pedidos cancelados no constituyen ingresos por ventas.
        </p>
    </section>

    <section class="report-card mb-4">
        <h2 class="h4 page-title mb-3">Inventario de muebles</h2>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Mueble</th>
                        <th class="text-end">Stock</th>
                        <th class="text-end">Reservado</th>
                        <th class="text-end">Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($reporteInventario ?? []) as $mueble): ?>
                        <?php
                            $stock = (int) $mueble['stock'];
                            $reservado = (int) $mueble['stock_reservado'];
                            $disponible = $stock - $reservado;
                        ?>
                        <tr>
                            <td><?= esc($mueble['nombre']) ?></td>
                            <td class="text-end"><?= $stock ?></td>
                            <td class="text-end"><?= $reservado ?></td>
                            <td class="text-end">
                                <?php if ($disponible <= 2): ?>
                                    <span class="badge bg-warning text-dark">
                                        <?= $disponible ?>
                                    </span>
                                <?php else: ?>
                                    <?= $disponible ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($reporteInventario)): ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                No hay muebles activos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="small text-secondary mb-0">
            Las unidades disponibles se calculan restando las reservas
            al stock registrado. Los muebles con dos unidades o menos
            se resaltan en amarillo.
        </p>
    </section>

    <section class="report-card mb-4">
        <h2 class="h4 page-title mb-3">Reparaciones por estado</h2>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Estado de reparación</th>
                        <th class="text-end">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($reparacionesPorEstado ?? []) as $fila): ?>
                        <tr>
                            <td><?= esc(str_replace('_', ' ', $fila['estado'])) ?></td>
                            <td class="text-end">
                                <?= number_format((int) $fila['cantidad']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($reparacionesPorEstado)): ?>
                        <tr>
                            <td colspan="2" class="text-center">
                                No hay reparaciones registradas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="small text-secondary mb-0">
            Cantidad de reparaciones agrupadas según su estado actual.
        </p>
    </section>

</main>

<footer class="text-center text-secondary small py-4">
    &copy; <?= date('Y') ?> Mueblería Imperial
</footer>

</body>
</html>
