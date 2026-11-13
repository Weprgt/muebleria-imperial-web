<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Solicitudes de cancelación | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>

<body class="bg-dark text-light">

    <main class="container py-4">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">Solicitudes de cancelación</h1>
            <p class="text-secondary mb-0">
                Pedidos pendientes de revisión
            </p>
        </div>

        <a href="<?= site_url('dashboard') ?>"
           class="btn btn-outline-light">
            Volver al panel
        </a>
    </div>

    <?php if (empty($solicitudes)): ?>

        <div class="alert alert-info">
            No hay solicitudes de cancelación pendientes.
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-bordered align-middle">

                <thead>
                    <tr>
                        <th>Solicitud</th>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Motivo</th>
                        <th>Estado del pedido</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($solicitudes as $solicitud): ?>
                        <tr>
                            <td>#<?= esc($solicitud['id']) ?></td>

                            <td>#<?= esc($solicitud['pedido_id']) ?></td>

                            <td>
                                <?= esc(
                                    $solicitud['nombre'] . ' ' .
                                    $solicitud['apellido']
                                ) ?>

                                <div class="small text-secondary">
                                    <?= esc($solicitud['correo']) ?>
                                </div>
                            </td>

                            <td>
                                Q<?= number_format(
                                    (float) $solicitud['total'],
                                    2
                                ) ?>
                            </td>

                            <td>
                                <?= nl2br(esc($solicitud['motivo'])) ?>
                            </td>

                            <td>
                                <?= esc($solicitud['estado_pedido']) ?>
                            </td>
                            <td>
                                <?= esc($solicitud['created_at']) ?>
                            </td>

                            <td>
                                <form
                                    action="<?= site_url(
                                        'gestion-cancelaciones/' .
                                        $solicitud['id'] .
                                        '/aprobar'
                                    ) ?>"
                                    method="post"
                                    onsubmit="return confirm('¿Confirmas que deseas aprobar esta cancelación?');"
                                >
                                    <?= csrf_field() ?>

                                    <button type="submit" class="btn btn-success btn-sm">
                                        Aprobar
                                    </button>
                                </form>
                                <form
                                action="<?= site_url(
                                    'gestion-cancelaciones/' .
                                    $solicitud['id'] .
                                    '/rechazar'
                                ) ?>"
                                method="post"
                                class="mt-2"
                                onsubmit="return confirm('¿Confirmas que deseas rechazar esta solicitud? El pedido continuará en preparación.');"
                            >
                                <?= csrf_field() ?>

                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm"
                                >
                                    Rechazar
                                </button>
                            </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    <?php endif; ?>

</main>

<script src="<?= base_url(
    'assets/vendor/bootstrap/js/bootstrap.bundle.min.js'
) ?>"></script>

</body>
</html>
