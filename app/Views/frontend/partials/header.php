<?php
$logeado = session('logueado') === true;
?>

<header class="imperial-header">
    <nav class="navbar navbar-expand-lg"
         aria-label="Navegación principal">
        <div class="container">
            <a class="navbar-brand imperial-brand"
               href="<?= site_url('/') ?>">
                <img
                    src="<?= base_url('assets/img/logo-imperial.png') ?>"
                    alt=""
                    class="imperial-logo"
                >
                <span>Mueblería Imperial</span>
            </a>

            <button class="navbar-toggler imperial-navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#menuImperial"
                    aria-controls="menuImperial"
                    aria-expanded="false"
                    aria-label="Mostrar menú de navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuImperial">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link active"
                           aria-current="page"
                           href="<?= site_url('/') ?>">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="<?= site_url('catalogo') ?>">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="<?= site_url('carrito') ?>">Carrito</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn imperial-btn-login"
                           href="<?= site_url($logeado ? 'dashboard' : 'login') ?>">
                            <?= $logeado ? 'Mi cuenta' : 'Iniciar sesión' ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
