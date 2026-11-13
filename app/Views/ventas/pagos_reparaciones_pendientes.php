<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pagos de reparaciones | Mueblería Imperial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-dark text-light">

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Pagos pendientes de reparaciones</h1>

        <a href="<?= site_url('dashboard') ?>"
           class="btn btn-outline-light">
            Volver
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

    <?php if (empty($pagos)): ?>

        <div class="alert alert-info">
            No hay pagos de reparaciones pendientes de verificación.
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-bordered align-middle">

                <thead>
                    <tr>
                        <th>Pago</th>
                        <th>Reparación</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Referencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td>#<?= esc($pago['id']) ?></td>

                            <td>
                                #<?= esc($pago['reparacion_id']) ?>
                                <div class="small text-secondary">
                                    <?= esc($pago['estado_reparacion']) ?>
                                </div>
                            </td>

                            <td>
                                <?= esc(
                                    $pago['nombre_cliente'] . ' ' .
                                    $pago['apellido_cliente']
                                ) ?>
                            </td>

                            <td><?= esc($pago['created_at']) ?></td>

                            <td>
                                Q <?= number_format(
                                    (float) $pago['monto'],
                                    2
                                ) ?>
                            </td>

                            <td><?= esc($pago['metodo_pago']) ?></td>

                            <td>
                                <?= esc($pago['referencia'] ?? 'Sin referencia') ?>
                            </td>

                            <td>
                                <span class="badge bg-warning text-dark">
                                    <?= esc($pago['estado']) ?>
                                </span>
                            </td>

                            <td>
                                <form
                                    action="<?= site_url(
                                        'reparaciones/pagos/' . $pago['id'] . '/aprobar'
                                    ) ?>"
                                    method="post"
                                    onsubmit="return confirm('¿Confirmas que verificaste este anticipo?');"
                                >
                                    <?= csrf_field() ?>

                                    <button type="submit" class="btn btn-success btn-sm">
                                        Aprobar
                                    </button>
                                </form>
                                <form
                                    action="<?= site_url(
                                        'reparaciones/pagos/' . $pago['id'] . '/rechazar'
                                    ) ?>"
                                    method="post"
                                    class="mt-2"
                                    onsubmit="return confirm('¿Confirmas que deseas rechazar este anticipo?');"
                                >
                                    <?= csrf_field() ?>

                                    <button type="submit" class="btn btn-danger btn-sm">
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

</body>
</html>
