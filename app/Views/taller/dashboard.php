<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de Taller | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>

<body class="bg-light">

    <header class="bg-dark text-white py-4">
        <div class="container">
            <h1 class="h3 mb-1">Mueblería Imperial</h1>
            <p class="mb-0">Panel de Taller</p>
        </div>
    </header>

    <main class="container py-5">

        <h2 class="h4 mb-2">
            Bienvenido, <?= esc($nombre) ?>
        </h2>

        <p class="text-muted mb-4">
            Resumen de las reparaciones registradas.
        </p>

        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="h6 text-muted">
                            Pendientes de atención
                        </h3>
                        <p class="display-5 fw-bold mb-0">
                            <?= esc($pendientes) ?>
                        </p>
                        <small class="text-muted">
                            Solicitadas o en evaluación
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="h6 text-muted">
                            En reparación
                        </h3>
                        <p class="display-5 fw-bold mb-0">
                            <?= esc($enReparacion) ?>
                        </p>
                        <small class="text-muted">
                            Trabajos en proceso
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="h6 text-muted">
                            Listas para entregar
                        </h3>
                        <p class="display-5 fw-bold mb-0">
                            <?= esc($finalizadas) ?>
                        </p>
                        <small class="text-muted">
                            Reparaciones finalizadas
                        </small>
                    </div>
                </div>
            </div>

        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h3 class="h5">Gestión de reparaciones</h3>

                <p class="text-muted">
                    Consulta solicitudes, prepara presupuestos,
                    inicia reparaciones y registra entregas.
                </p>

                <a href="<?= site_url('taller/reparaciones') ?>"
                   class="btn btn-primary">
                    Gestionar reparaciones
                </a>
            </div>
        </div>

        <form action="<?= site_url('logout') ?>" method="post">
            <?= csrf_field() ?>

            <button type="submit" class="btn btn-outline-danger">
                Cerrar sesión
            </button>
        </form>

    </main>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
