<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mis reparaciones | Mueblería Imperial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-dark text-light">

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Mis reparaciones</h1>

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

    <div class="mb-4">
        <a href="<?= site_url('reparaciones/nueva') ?>"
           class="btn btn-primary">
            Solicitar nueva reparación
        </a>
    </div>

    <?php if (empty($reparaciones)): ?>

        <div class="alert alert-info">
            Todavía no tienes solicitudes de reparación.
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-dark table-striped table-bordered align-middle">

                <thead>
                    <tr>
                        <th>Solicitud</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Presupuesto</th>
                        <th>Diagnóstico</th>
                        <th>Anticipo (50 %)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($reparaciones as $reparacion): ?>
                        <tr>
                            <td>
                                #<?= esc($reparacion['id']) ?>
                            </td>

                            <td>
                                <?= esc($reparacion['created_at']) ?>
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= esc($reparacion['estado']) ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($reparacion['presupuesto_total'] !== null): ?>
                                    Q <?= number_format(
                                        (float) $reparacion['presupuesto_total'],
                                        2
                                    ) ?>
                                <?php else: ?>
                                    Pendiente de evaluación
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($reparacion['diagnostico'])): ?>
                                    <?= nl2br(esc($reparacion['diagnostico'])) ?>
                                <?php else: ?>
                                    <span class="text-secondary">
                                        Pendiente de diagnóstico
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($reparacion['anticipo_requerido'] !== null): ?>
                                    Q <?= number_format(
                                        (float) $reparacion['anticipo_requerido'],
                                        2
                                    ) ?>
                                <?php else: ?>
                                    <span class="text-secondary">
                                        Pendiente
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($reparacion['estado'] === 'PRESUPUESTADA'): ?>

                                    <form
                                        action="<?= site_url('reparaciones/' . (int) $reparacion['id'] . '/aceptar-presupuesto') ?>"
                                        method="post"
                                    >
                                        <?= csrf_field() ?>

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('¿Confirmas que deseas aceptar este presupuesto?')"
                                        >
                                            Aceptar presupuesto
                                        </button>
                                    </form>

                                    <?php elseif ($reparacion['estado'] === 'ACEPTADA'): ?>

                                        <div class="mb-2 text-success">
                                            Presupuesto aceptado
                                        </div>

                                        <?php if ((int) $reparacion['pagos_activos'] > 0): ?>

                                            <?php if ($reparacion['estado_ultimo_pago'] === 'PENDIENTE'): ?>
                                                <span class="badge bg-warning text-dark">
                                                    Anticipo pendiente de verificación
                                                </span>
                                            <?php elseif ($reparacion['estado_ultimo_pago'] === 'VERIFICADO'): ?>
                                                <span class="badge bg-success">
                                                    Anticipo verificado
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">
                                                    Existe un pago activo
                                                </span>
                                            <?php endif; ?>

                                        <?php else: ?>

                                            <?php if ($reparacion['estado_ultimo_pago'] === 'RECHAZADO'): ?>
                                                <div class="alert alert-warning py-2 small">
                                                    El anticipo anterior fue rechazado.
                                                    Puedes registrar uno nuevo.
                                                </div>
                                            <?php endif; ?>

                                            <form
                                                action="<?= site_url('reparaciones/' . (int) $reparacion['id'] . '/registrar-anticipo') ?>"
                                                method="post"
                                                class="d-flex flex-column gap-2"
                                                onsubmit="return confirm('¿Confirmas el registro de este anticipo?')"
                                            >
                                                <?= csrf_field() ?>

                                                <div>
                                                    <label class="form-label small">
                                                        Anticipo a registrar
                                                    </label>

                                                    <input
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        value="Q <?= number_format(
                                                            (float) $reparacion['anticipo_requerido'],
                                                            2
                                                        ) ?>"
                                                        readonly
                                                    >
                                                </div>

                                                <div>
                                                    <label class="form-label small">
                                                        Método de pago
                                                    </label>

                                                    <select
                                                        name="metodo_pago"
                                                        class="form-select form-select-sm"
                                                        required
                                                        onchange="
                                                            const referencia = this.form.querySelector('[name=referencia]');
                                                            referencia.required = this.value === 'TRANSFERENCIA' || this.value === 'DEPOSITO';
                                                        "
                                                    >
                                                        <option value="">Seleccionar</option>
                                                        <option value="TRANSFERENCIA">Transferencia</option>
                                                        <option value="DEPOSITO">Depósito</option>
                                                        <option value="EFECTIVO">Efectivo</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="form-label small">
                                                        Referencia del pago
                                                    </label>

                                                    <input
                                                        type="text"
                                                        name="referencia"
                                                        class="form-control form-control-sm"
                                                        maxlength="150"
                                                        placeholder="Número de referencia"
                                                    >
                                                </div>

                                                <button
                                                    type="submit"
                                                    class="btn btn-primary btn-sm"
                                                >
                                                    Registrar anticipo
                                                </button>
                                            </form>

                                        <?php endif; ?>

                                        <?php elseif ($reparacion['estado'] === 'FINALIZADA'): ?>

                                            <div class="mb-2">
                                                <span class="badge bg-success">
                                                    Reparación finalizada
                                                </span>
                                            </div>

                                            <div class="small mb-2">
                                                Pagado: Q <?= number_format(
                                                    (float) $reparacion['total_verificado'], 2
                                                ) ?>
                                                <div class="fw-bold">
                                                    Saldo: Q <?= number_format(
                                                        (float) $reparacion['saldo_pendiente'], 2
                                                    ) ?>
                                                </div>
                                            </div>

                                            <?php if ((int) $reparacion['pagos_pendientes'] > 0): ?>

                                                <span class="badge bg-warning text-dark">
                                                    Pago pendiente de verificación
                                                </span>

                                            <?php elseif ((float) $reparacion['saldo_pendiente'] <= 0): ?>

                                                <span class="badge bg-success">
                                                    Reparación pagada completamente
                                                </span>

                                            <?php else: ?>

                                                <?php if ($reparacion['estado_ultimo_pago'] === 'RECHAZADO'): ?>
                                                    <div class="alert alert-warning py-2 small">
                                                        El último pago fue rechazado.
                                                        Puedes registrar uno nuevo.
                                                    </div>
                                                <?php endif; ?>

                                                <form
                                                    action="<?= site_url(
                                                        'reparaciones/'
                                                        . (int) $reparacion['id']
                                                        . '/pagar-saldo'
                                                    ) ?>"
                                                    method="post"
                                                    class="d-flex flex-column gap-2"
                                                    onsubmit="return confirm('¿Confirmas el registro del saldo restante?')"
                                                >
                                                    <?= csrf_field() ?>

                                                    <input
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        value="Q <?= number_format(
                                                            (float) $reparacion['saldo_pendiente'], 2
                                                        ) ?>"
                                                        readonly
                                                    >

                                                    <select
                                                        name="metodo_pago"
                                                        class="form-select form-select-sm"
                                                        required
                                                        onchange="
                                                            const referencia = this.form.querySelector('[name=referencia]');
                                                            referencia.required = this.value === 'TRANSFERENCIA' || this.value === 'DEPOSITO';
                                                        "
                                                    >
                                                        <option value="">Método de pago</option>
                                                        <option value="TRANSFERENCIA">Transferencia</option>
                                                        <option value="DEPOSITO">Depósito</option>
                                                        <option value="EFECTIVO">Efectivo</option>
                                                    </select>

                                                    <input
                                                        type="text"
                                                        name="referencia"
                                                        class="form-control form-control-sm"
                                                        maxlength="150"
                                                        placeholder="Referencia del pago"
                                                    >

                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        Pagar saldo restante
                                                    </button>
                                                </form>

                                            <?php endif; ?>

                                <?php else: ?>

                                    <span class="text-secondary">
                                        Sin acciones disponibles
                                    </span>

                                <?php endif; ?>
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
