
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel <?= esc($rol) ?> | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>

<body class="bg-light">

    <header class="bg-dark text-white py-4">
        <div class="container">
            <h1 class="h3 mb-1">Mueblería Imperial</h1>
            <p class="mb-0">Panel de <?= esc($rol) ?></p>
        </div>
    </header>

    <main class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <h2 class="h4">
                    Bienvenido, <?= esc($nombre) ?>
                </h2>

                <p>
                    Has ingresado al panel de
                    <strong><?= esc($rol) ?></strong>.
                </p>

                <div class="alert alert-success">
                    Tu rol tiene autorización para acceder a esta página.
                </div>

                <form action="<?= site_url('logout') ?>" method="post">
                    <?= csrf_field() ?>

                    <button type="submit" class="btn btn-danger">
                        Cerrar sesión
                    </button>
                </form>

            </div>
        </div>
    </main>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
