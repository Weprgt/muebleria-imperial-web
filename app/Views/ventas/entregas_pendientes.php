<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Entregas pendientes - Mueblería Imperial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">

    <h1 class="mb-3">Entregas pendientes</h1>

    <p class="text-muted">
        Pedidos confirmados que todavía no han sido entregados.
    </p>

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

    <?php if (empty($pedidos)): ?>

        <div class="alert alert-info">
            No existen pedidos confirmados pendientes de entrega.
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Pedido</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha del pedido</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= (int) $pedido['id'] ?></td>

                            <td>
                                Q<?= number_format((float) $pedido['total'], 2) ?>
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    <?= esc($pedido['estado']) ?>
                                </span>
                            </td>

                            <td>
                                <?= esc($pedido['created_at']) ?>
                            </td>
                            <td>
                                <?php if ($pedido['estado'] === 'CONFIRMADO'): ?>

                                    <form
                                        action="<?= site_url('pedidos/' . (int) $pedido['id'] . '/iniciar-preparacion') ?>"
                                        method="post"
                                        onsubmit="return confirm('¿Deseas iniciar la preparación de este pedido?');"
                                    >
                                        <?= csrf_field() ?>

                                        <button type="submit" class="btn btn-primary btn-sm">
                                            Iniciar preparación
                                        </button>
                                    </form>

                                <?php elseif ($pedido['estado'] === 'EN_PREPARACION'): ?>

                                    <form
                                        action="<?= site_url('entregas/' . (int) $pedido['id'] . '/registrar') ?>"
                                        method="post"
                                        onsubmit="return confirm('¿Confirmas que este pedido fue entregado al cliente?');"
                                    >
                                        <?= csrf_field() ?>

                                        <button type="submit" class="btn btn-success btn-sm">
                                            Registrar entrega
                                        </button>
                                    </form>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

    <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary mt-3">
        Volver al panel
    </a>

</div>

</body>
</html>