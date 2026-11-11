<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pedidos | Mueblería Imperial</title>
    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>

<body class="bg-light">

<header class="bg-dark text-white py-4">
    <div class="container">
        <h1 class="h3 mb-1">Mueblería Imperial</h1>
        <p class="mb-0">Mis pedidos</p>
    </div>
</header>

<main class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">
            Bienvenido, <?= esc($nombre) ?>
        </h2>
        <a href="<?= site_url('cliente') ?>"
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

    <?php if (empty($pedidos)): ?>

        <div class="alert alert-info">
            Todavía no tienes pedidos registrados.
        </div>

        <a href="<?= site_url('catalogo') ?>"
           class="btn btn-primary">
            Explorar catálogo
        </a>

    <?php else: ?>

        <?php foreach ($pedidos as $pedido): ?>

            <?php
                $total = (float) $pedido['total'];
                $anticipo = (float) $pedido['anticipo_requerido'];
                $verificado = (float) $pedido['total_verificado'];

                $anticipoPendiente = max(
                    0,
                    $anticipo - $verificado
                );
            ?>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">
                        Pedido #<?= esc($pedido['id']) ?>
                    </h3>
                </div>

                <div class="card-body">

                    <p>
                        <strong>Estado:</strong>
                        <?= esc($pedido['estado']) ?>
                    </p>

                    <p>
                        <strong>Total:</strong>
                        Q<?= number_format($total, 2) ?>
                    </p>

                    <p>
                        <strong>Anticipo requerido (25 %):</strong>
                        Q<?= number_format($anticipo, 2) ?>
                    </p>

                    <p>
                        <strong>Pagos verificados:</strong>
                        Q<?= number_format($verificado, 2) ?>
                    </p>

                    <p>
                        <strong>Anticipo pendiente:</strong>
                        Q<?= number_format($anticipoPendiente, 2) ?>
                    </p>

                    <hr>

                    <h4 class="h6">Historial de pagos</h4>

                    <?php if (empty($pedido['pagos'])): ?>

                        <p class="text-muted">
                            No hay pagos registrados.
                        </p>

                    <?php else: ?>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Monto</th>
                                        <th>Método</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedido['pagos'] as $pago): ?>
                                        <tr>
                                            <td><?= esc($pago['id']) ?></td>
                                            <td>
                                                Q<?= number_format(
                                                    (float) $pago['monto'],
                                                    2
                                                ) ?>
                                            </td>
                                            <td><?= esc($pago['metodo_pago']) ?></td>
                                            <td><?= esc($pago['estado']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php endif; ?>

                    <?php if ($pedido['estado'] === 'PENDIENTE_ANTICIPO'): ?>

                        <hr>

                        <h4 class="h6">Registrar pago</h4>

                        <p class="text-muted small">
                            El pago quedará pendiente de verificación.
                            Registrar un pago no confirma su recepción.
                        </p>

                        <form action="<?= site_url('pagos/registrar') ?>"
                              method="post">

                            <?= csrf_field() ?>

                            <input type="hidden"
                                   name="pedido_id"
                                   value="<?= esc($pedido['id']) ?>">

                            <div class="mb-3">
                                <label class="form-label">Monto (Q)</label>
                                <input type="number"
                                       name="monto"
                                       class="form-control"
                                       min="0.01"
                                       max="<?= esc(number_format(
                                           max(0, $total - $verificado),
                                           2,
                                           '.',
                                           ''
                                       )) ?>"
                                       step="0.01"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Método de pago
                                </label>

                                <select name="metodo_pago"
                                        class="form-select"
                                        required>
                                    <option value="">
                                        Seleccionar
                                    </option>
                                    <option value="TRANSFERENCIA">
                                        Transferencia
                                    </option>
                                    <option value="DEPOSITO">
                                        Depósito
                                    </option>
                                    <option value="EFECTIVO">
                                        Efectivo
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Referencia del pago
                                </label>

                                <input type="text"
                                       name="referencia"
                                       class="form-control"
                                       maxlength="150">

                                <div class="form-text">
                                    Obligatoria para transferencias y depósitos.
                                </div>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary">
                                Registrar pago
                            </button>

                        </form>

                    <?php endif; ?>

                </div>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</main>

<script src="<?= base_url(
    'assets/vendor/bootstrap/js/bootstrap.bundle.min.js'
) ?>"></script>

</body>
</html>
