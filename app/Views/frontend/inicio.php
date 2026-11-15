<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mueblería Imperial | Tu hogar, nuestro arte</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">

    <link rel="stylesheet"
          href="<?= base_url('assets/css/estilos.css') ?>">
</head>

<body>

    <!-- Encabezado principal -->
    <header class="imperial-header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">

                <!-- Identidad de la tienda -->
                <a class="navbar-brand d-flex align-items-center"
                   href="<?= base_url('/') ?>">

                    <img
                        src="<?= base_url('assets/img/logo-imperial.png') ?>"
                        alt="Logotipo de Mueblería Imperial"
                        class="imperial-logo">

                    <span class="imperial-brand-name">
                        Mueblería Imperial
                    </span>
                </a>

                <!-- Menú para dispositivos móviles -->
                <button class="navbar-toggler"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#menuImperial"
                        aria-controls="menuImperial"
                        aria-expanded="false"
                        aria-label="Mostrar menú">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navegación -->
                <div class="collapse navbar-collapse"
                     id="menuImperial">

                    <ul class="navbar-nav ms-auto align-items-lg-center">

                        <li class="nav-item">
                            <a class="nav-link active"
                               aria-current="page"
                               href="<?= base_url('/') ?>">
                                Inicio
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= base_url('catalogo') ?>">
                                Catálogo
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= base_url('carrito') ?>">
                                Carrito
                            </a>
                        </li>

                        <li class="nav-item ms-lg-3">
                            <a class="btn imperial-btn-login"
                               href="<?= base_url('login') ?>">
                                Iniciar sesión
                            </a>
                        </li>

                    </ul>
                </div>

            </div>
        </nav>
    </header>

    <!-- Contenido temporal -->
    <main class="container py-5">
        <h1>Bienvenido a Mueblería Imperial</h1>
        <p>
            Tu hogar, nuestro arte.
            Descubre muebles para cada espacio de tu hogar.
        </p>
    </main>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
