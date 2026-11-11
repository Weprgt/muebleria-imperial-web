<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pagos pendientes - Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Pagos pendientes</h1>
            <p class="text-muted mb-0">
                Mueblería Imperial - Verificación de anticipos
            </p>
        </div>

        <a href="<?= site_url('dashboard') ?>"
           class="btn btn-outline-secondary">
            Volver al panel
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if (empty($pagos)): ?>

                <div class="alert alert-info mb-0">
                    No existen pagos pendientes de verificación.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>Pago</th>
                                <th>Pedido</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Referencia</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($pagos as $pago): ?>
                                <tr>
                                    <td>#<?= esc($pago['id']) ?></td>

                                    <td>#<?= esc($pago['pedido_id']) ?></td>

                                    <td>
                                        Q <?= number_format(
                                            (float) $pago['monto'],
                                            2
                                        ) ?>
                                    </td>

                                    <td><?= esc($pago['metodo_pago']) ?></td>

                                    <td>
                                        <?= esc($pago['referencia'] ?? '—') ?>
                                    </td>

                                    <td><?= esc($pago['created_at']) ?></td>

                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            <?= esc($pago['estado']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">

                                            <form method="post"
                                                  action="<?= site_url('pagos/' . $pago['id'] . '/aprobar') ?>">
                                                <?= csrf_field() ?>

                                                <button type="submit"
                                                        class="btn btn-success btn-sm"
                                                        onclick="return confirm('¿Confirmas que el dinero fue recibido y deseas aprobar este pago?')">
                                                    Aprobar
                                                </button>
                                            </form>

                                            <form method="post"
                                                  action="<?= site_url('pagos/' . $pago['id'] . '/rechazar') ?>">
                                                <?= csrf_field() ?>

                                                <button type="submit"
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('¿Estás seguro de rechazar este pago?')">
                                                    Rechazar
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <p class="text-muted small mt-3">
        Los pagos mostrados todavía no han sido verificados.
        Antes de aprobar un pago debe comprobarse que el dinero
        realmente fue recibido.
    </p>

</div>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
