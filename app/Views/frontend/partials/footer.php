<footer class="imperial-footer">
    <div class="container">
        <div class="row gy-4 align-items-start">
            <div class="col-12 col-lg-6">
                <a class="imperial-footer__brand"
                   href="<?= site_url('/') ?>">
                    <img
                        src="<?= base_url('assets/img/logo-imperial.png') ?>"
                        alt=""
                        width="44"
                        height="44"
                        loading="lazy"
                    >
                    <span>Mueblería Imperial</span>
                </a>
                <p class="imperial-footer__motto">Tu hogar, nuestro arte.</p>
                <p class="imperial-footer__description">
                    Muebles y cuidado para los espacios que hacen hogar.
                </p>
            </div>

            <div class="col-12 col-lg-6">
                <h2 class="imperial-footer__heading">Explora</h2>
                <nav aria-label="Enlaces del pie de página">
                    <ul class="imperial-footer__links">
                        <li><a href="<?= site_url('/') ?>">Inicio</a></li>
                        <li><a href="<?= site_url('catalogo') ?>">Catálogo</a></li>
                        <li><a href="<?= site_url('carrito') ?>">Carrito</a></li>
                        <li>
                            <a href="<?= site_url('reparaciones/nueva') ?>">
                                Reparación de muebles
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="imperial-footer__bottom">
            <p class="mb-0">
                &copy; <?= date('Y') ?> Mueblería Imperial
            </p>
            <p class="mb-0">Tu hogar, nuestro arte.</p>
        </div>
    </div>
</footer>
