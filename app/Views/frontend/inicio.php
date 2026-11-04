
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mueblería Imperial</title>

    <!-- Bootstrap local -->
    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">

    <!-- CSS personalizado -->
    <link rel="stylesheet"
          href="<?= base_url('assets/css/estilos.css') ?>">
</head>

<body>

    <main class="container py-5">
        <h1>Bienvenido a Mueblería Imperial</h1>

        <p class="lead">
            Proyecto Fullstack desarrollado con CodeIgniter 4.
        </p>

        <button class="btn btn-primary" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mensaje">
            Probar Bootstrap
        </button>

        <div class="collapse mt-3" id="mensaje">
            <div class="card card-body">
                ¡Bootstrap CSS y JavaScript funcionan correctamente!
            </div>
        </div>
    </main>

    <!-- JavaScript de Bootstrap local -->
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
