<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gestión de reparaciones | Mueblería Imperial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de reparaciones</h1>
            <p class="text-secondary mb-0">
                Solicitudes recibidas de los clientes.
            </p>
        </div>

        <a href="<?= site_url('dashboard') ?>"
           class="btn btn-outline-secondary">
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

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if (empty($reparaciones)): ?>

                <div class="alert alert-info mb-0">
                    No hay solicitudes de reparación registradas.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Solicitud</th>
                                <th>Cliente</th>
                                <th>Mueble(s)</th>
                                <th>Fecha</th>
                                <th>Canal</th>
                                <th>Estado</th>
                                <th>Presupuesto</th>
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
                                        <?= esc(
                                            trim(
                                                $reparacion['nombre_cliente']
                                                . ' '
                                                . $reparacion['apellido_cliente']
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= esc($reparacion['muebles'] ?? 'Sin muebles registrados') ?>
                                    </td>

                                    <td>
                                        <?= esc($reparacion['created_at']) ?>
                                    </td>

                                    <td>
                                        <?= esc($reparacion['canal']) ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
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
                                            Pendiente
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($reparacion['estado'] === 'SOLICITADA'): ?>

                                            <form
                                                action="<?= site_url('taller/reparaciones/' . $reparacion['id'] . '/iniciar-evaluacion') ?>"
                                                method="post"
                                            >
                                                <?= csrf_field() ?>

                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    Iniciar evaluación
                                                </button>
                                            </form>

                                        <?php elseif ($reparacion['estado'] === 'EN_EVALUACION'): ?>

                                            <a
                                                href="<?= site_url('taller/reparaciones/' . (int) $reparacion['id'] . '/presupuestar') ?>"
                                                class="btn btn-sm btn-success"
                                            >
                                                Registrar presupuesto
                                            </a>


                                        <?php elseif ($reparacion['estado'] === 'ACEPTADA'): ?>

                                            <form
                                                action="<?= site_url(
                                                    'taller/reparaciones/'
                                                    . (int) $reparacion['id']
                                                    . '/iniciar-reparacion'
                                                ) ?>"
                                                method="post"
                                                onsubmit="return confirm('¿Confirmas que deseas iniciar esta reparación?');"
                                            >
                                                <?= csrf_field() ?>

                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    🔧 Iniciar reparación
                                                </button>
                                            </form>

                                        <?php elseif ($reparacion['estado'] === 'EN_REPARACION'): ?>

                                            <form
                                                action="<?= site_url(
                                                    'taller/reparaciones/'
                                                    . (int) $reparacion['id']
                                                    . '/finalizar-reparacion'
                                                ) ?>"
                                                method="post"
                                                onsubmit="return confirm('¿Confirmas que el trabajo de reparación está terminado?');"
                                            >
                                                <?= csrf_field() ?>

                                                <button type="submit" class="btn btn-success btn-sm">
                                                    Finalizar reparación
                                                </button>
                                            </form>

                                    <?php elseif ($reparacion['estado'] === 'FINALIZADA'): ?>

                                        <form
                                            action="<?= site_url(
                                                'taller/reparaciones/'
                                                . (int) $reparacion['id']
                                                . '/entregar'
                                            ) ?>"
                                            method="post"
                                            onsubmit="return confirm('¿Confirmas que esta reparación fue entregada al cliente? Esta operación registrará la entrega definitiva.');"
                                        >
                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="btn btn-primary btn-sm"
                                            >
                                                Entregar reparación
                                            </button>
                                        </form>

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

        </div>
    </div>

</main>

</body>
</html>
