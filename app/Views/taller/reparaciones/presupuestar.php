<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Presupuesto de reparación - Mueblería Imperial</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="mb-4">
        <h1>Diagnóstico y presupuesto</h1>
        <p class="text-muted">
            Reparación #<?= esc($reparacion['id']) ?>
        </p>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Información de la reparación
        </div>

        <div class="card-body">
            <p>
                <strong>Cliente:</strong>
                <?= esc($reparacion['nombre_cliente'] . ' ' . $reparacion['apellido_cliente']) ?>
            </p>

            <p>
                <strong>Estado:</strong>
                <?= esc($reparacion['estado']) ?>
            </p>

            <p>
                <strong>Canal:</strong>
                <?= esc($reparacion['canal']) ?>
            </p>

            <p>
                <strong>Evaluación:</strong> Gratuita
            </p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Muebles de la solicitud
        </div>

        <div class="card-body">

            <?php foreach ($detalles as $detalle): ?>
                <div class="mb-3">
                    <h5><?= esc($detalle['nombre_mueble']) ?></h5>

                    <p><?= esc($detalle['descripcion_problema']) ?></p>

                    <p>
                        <strong>Cantidad:</strong>
                        <?= esc($detalle['cantidad']) ?>
                    </p>
                </div>
                <hr>
            <?php endforeach; ?>

        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Registrar diagnóstico y presupuesto
        </div>

        <div class="card-body">

            <form method="post"
                  action="<?= site_url('taller/reparaciones/' . $reparacion['id'] . '/guardar-presupuesto') ?>">

                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="diagnostico" class="form-label">
                        Diagnóstico técnico
                    </label>

                    <textarea
                        id="diagnostico"
                        name="diagnostico"
                        class="form-control"
                        rows="4"
                        required
                    ></textarea>
                </div>

                <div class="mb-3">
                    <label for="presupuesto_total" class="form-label">
                        Presupuesto total (Q)
                    </label>

                    <input
                        type="number"
                        id="presupuesto_total"
                        name="presupuesto_total"
                        class="form-control"
                        min="0.01"
                        step="0.01"
                        required
                    >
                </div>

                <div class="alert alert-info">
                    <strong>Anticipo requerido (50 %):</strong>
                    <span id="anticipo">Q 0.00</span>
                </div>

                <button type="submit" class="btn btn-primary">
                    Guardar diagnóstico y presupuesto
                </button>

                <a href="<?= site_url('taller/reparaciones') ?>"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>
    </div>

</div>

<script>
    const presupuesto = document.getElementById('presupuesto_total');
    const anticipo = document.getElementById('anticipo');

    presupuesto.addEventListener('input', function () {
        const total = Number(this.value);

        const monto = Number.isFinite(total) && total > 0
            ? Math.round((total * 0.5 + Number.EPSILON) * 100) / 100
            : 0;

        anticipo.textContent = 'Q ' + monto.toFixed(2);
    });
</script>

</body>
</html>
