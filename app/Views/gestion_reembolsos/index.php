
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de reembolsos - Mueblería Imperial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-dark text-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de reembolsos</h1>

        <a href="<?= site_url('dashboard') ?>"
           class="btn btn-outline-light">
            Volver al dashboard
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

    <p class="text-light">
        Los reembolsos registrados permanecen pendientes
        hasta confirmar la devolución efectiva del dinero.
    </p>

    <?php if (empty($pedidos)): ?>

        <div class="alert alert-info">
            No hay pedidos cancelados.
        </div>

    <?php else: ?>

        <?php foreach ($pedidos as $pedido): ?>

            <?php
                $pagadoCentavos = (int) round(
                    (float) $pedido['total_pagado'] * 100
                );

                $comprometidoCentavos = (int) round(
                    (float) $pedido['total_comprometido'] * 100
                );

                $disponibleCentavos = max(
                    0,
                    $pagadoCentavos - $comprometidoCentavos
                );

                $disponible = number_format(
                    $disponibleCentavos / 100,
                    2,
                    '.',
                    ''
                );
            ?>

            <div class="card bg-secondary text-white mb-4">
                <div class="card-body">

                    <h2 class="h5">
                        Pedido #<?= esc($pedido['id']) ?>
                    </h2>

                    <div class="table-responsive">
                        <table class="table table-dark table-bordered">
                            <tbody>
                                <tr>
                                    <th>Total del pedido</th>
                                    <td>
                                        Q<?= number_format(
                                            (float) $pedido['total'], 2
                                        ) ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Pagos verificados</th>
                                    <td>
                                        Q<?= number_format(
                                            $pagadoCentavos / 100, 2
                                        ) ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Reembolsos pendientes o procesados</th>
                                    <td>
                                        Q<?= number_format(
                                            $comprometidoCentavos / 100, 2
                                        ) ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Disponible para reembolsar</th>
                                    <td class="fw-bold">
                                        Q<?= number_format(
                                            $disponibleCentavos / 100, 2
                                        ) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($disponibleCentavos > 0): ?>

                        <form method="post"
                              action="<?= site_url(
                                  'gestion-reembolsos/'
                                  . $pedido['id']
                                  . '/registrar'
                              ) ?>">

                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label">
                                    Monto del reembolso (Q)
                                </label>

                                <input
                                    type="number"
                                    name="monto"
                                    class="form-control"
                                    min="0.01"
                                    max="<?= esc($disponible, 'attr') ?>"
                                    step="0.01"
                                    value="<?= esc($disponible, 'attr') ?>"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Método de reembolso
                                </label>

                                <select
                                    name="metodo_reembolso"
                                    class="form-select"
                                    required
                                >
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
                                    Referencia (opcional)
                                </label>

                                <input
                                    type="text"
                                    name="referencia"
                                    class="form-control"
                                    maxlength="150"
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Motivo del reembolso
                                </label>

                                <textarea
                                    name="motivo"
                                    class="form-control"
                                    rows="3"
                                    minlength="10"
                                    maxlength="2000"
                                    required
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-warning"
                                onclick="return confirm(
                                    '¿Registrar este reembolso como pendiente?'
                                )"
                            >
                                Registrar reembolso pendiente
                            </button>

                        </form>

                    <?php else: ?>

                        <div class="alert alert-info mb-0">
                            Este pedido no tiene saldo disponible
                            para registrar nuevos reembolsos.
                        </div>

                    <?php endif; ?>

                </div>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>


    <hr class="border-light my-5">

    <h2 class="h4 mb-3">Historial de reembolsos</h2>

    <?php if (empty($reembolsos)): ?>

        <div class="alert alert-info">
            Todavía no existen reembolsos registrados.
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pedido</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Referencia</th>
                        <th>Estado</th>
                        <th>Registrado por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($reembolsos as $reembolso): ?>
                        <tr>
                            <td><?= esc($reembolso['id']) ?></td>

                            <td>
                                #<?= esc($reembolso['pedido_id']) ?>
                            </td>

                            <td>
                                Q<?= number_format(
                                    (float) $reembolso['monto'],
                                    2
                                ) ?>
                            </td>

                            <td>
                                <?= esc($reembolso['metodo_reembolso']) ?>
                            </td>

                            <td>
                                <?= esc($reembolso['referencia'] ?? '—') ?>
                            </td>

                            <td>
                                <?php
                                    $estado = $reembolso['estado'];

                                    $color = match ($estado) {
                                        'PENDIENTE' => 'warning',
                                        'PROCESADO' => 'success',
                                        'RECHAZADO' => 'danger',
                                        default => 'secondary',
                                    };
                                ?>

                                <span class="badge bg-<?= $color ?>">
                                    <?= esc($estado) ?>
                                </span>
                            </td>

                            <td>
                                <?= esc($reembolso['registrado_nombre']) ?>
                            </td>

                            <td>
                                <?php if ($estado === 'PENDIENTE'): ?>

                                    <div class="d-flex flex-wrap gap-2">

                                        <form
                                            method="post"
                                            action="<?= site_url(
                                                'gestion-reembolsos/'
                                                . $reembolso['id']
                                                . '/procesar'
                                            ) ?>"
                                            onsubmit="return confirm(
                                                '¿Confirmas que el dinero YA fue devuelto?'
                                            )"
                                        >
                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="btn btn-success btn-sm"
                                            >
                                                Procesar
                                            </button>
                                        </form>

                                        <form
                                            method="post"
                                            action="<?= site_url(
                                                'gestion-reembolsos/'
                                                . $reembolso['id']
                                                . '/rechazar'
                                            ) ?>"
                                            onsubmit="return confirm(
                                                '¿Rechazar esta solicitud de reembolso?'
                                            )"
                                        >
                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm"
                                            >
                                                Rechazar
                                            </button>
                                        </form>

                                    </div>

                                <?php else: ?>

                                    <span class="text-secondary">
                                        Sin acciones
                                    </span>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

    </div>

</body>
</html>
