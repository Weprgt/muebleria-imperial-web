<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="Descubre muebles para cada espacio y conoce nuestro servicio de reparación. Tu hogar, nuestro arte.">

    <title>Mueblería Imperial | Tu hogar, nuestro arte</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet"
          href="<?= base_url('assets/css/estilos.css') ?>">
    <link rel="preload"
          href="<?= base_url('assets/img/hero-sala-imperial.webp') ?>"
          as="image"
          type="image/webp">
    <link rel="preload"
          href="<?= base_url('assets/img/hero-comedor-imperial.webp') ?>"
          as="image"
          type="image/webp"
          fetchpriority="low">
    <link rel="preload"
          href="<?= base_url('assets/img/hero-restauracion-imperial.webp') ?>"
          as="image"
          type="image/webp"
          fetchpriority="low">
</head>

<body>
    <?= view('frontend/partials/header') ?>

    <main>
        <section id="imperialHeroCarousel"
                 class="carousel slide imperial-hero"
                 aria-label="Destacados de Mueblería Imperial">
            <div class="carousel-indicators">
                <button type="button"
                        data-bs-target="#imperialHeroCarousel"
                        data-bs-slide-to="0"
                        class="active"
                        aria-current="true"
                        aria-label="Diapositiva 1: Bienvenida"></button>
                <button type="button"
                        data-bs-target="#imperialHeroCarousel"
                        data-bs-slide-to="1"
                        aria-label="Diapositiva 2: Ofertas"></button>
                <button type="button"
                        data-bs-target="#imperialHeroCarousel"
                        data-bs-slide-to="2"
                        aria-label="Diapositiva 3: Reparaciones"></button>
            </div>

            <div class="carousel-inner">
                <div class="carousel-item active imperial-hero__slide"
                     role="group"
                     aria-roledescription="diapositiva"
                     aria-label="1 de 3">
                    <div class="imperial-hero__image imperial-hero__image--welcome"
                         aria-hidden="true"></div>
                    <div class="container imperial-hero__content">
                        <p class="imperial-eyebrow">Muebles para vivirlos</p>
                        <h1 id="hero-title">
                            Tu hogar,<br>
                            <span>nuestro arte</span>
                        </h1>
                        <p class="imperial-hero__intro">
                            Piezas que hacen de cada espacio un lugar para disfrutar.
                            Encuentra el mueble ideal para tu hogar.
                        </p>
                        <a class="btn imperial-btn-primary"
                           href="<?= site_url('catalogo') ?>">
                            Explorar catálogo
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>

                <div class="carousel-item imperial-hero__slide"
                     role="group"
                     aria-roledescription="diapositiva"
                     aria-label="2 de 3">
                    <div class="imperial-hero__image imperial-hero__image--dining"
                         aria-hidden="true"></div>
                    <div class="container imperial-hero__content">
                        <p class="imperial-eyebrow">Inspiración para cada espacio</p>
                        <h2 id="hero-promotions-title">
                            Ofertas para renovar tu hogar
                        </h2>
                        <p class="imperial-hero__intro">
                            Descubre nuestros muebles y encuentra inspiración
                            para cada espacio.
                        </p>
                        <a class="btn imperial-btn-primary"
                           href="<?= site_url('catalogo') ?>">
                            Explorar catálogo
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>

                <div class="carousel-item imperial-hero__slide"
                     role="group"
                     aria-roledescription="diapositiva"
                     aria-label="3 de 3">
                    <div class="imperial-hero__image imperial-hero__image--repair"
                         aria-hidden="true"></div>
                    <div class="container imperial-hero__content">
                        <p class="imperial-eyebrow">Servicio de reparación</p>
                        <h2 id="hero-repair-title">
                            Tus muebles merecen una segunda oportunidad
                        </h2>
                        <p class="imperial-hero__intro">
                            Dales una nueva vida con nuestro servicio de reparación.
                        </p>
                        <a class="btn imperial-btn-primary"
                           href="<?= site_url('reparaciones/nueva') ?>">
                            Solicitar reparación
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev"
                    type="button"
                    data-bs-target="#imperialHeroCarousel"
                    data-bs-slide="prev"
                    aria-label="Diapositiva anterior">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next"
                    type="button"
                    data-bs-target="#imperialHeroCarousel"
                    data-bs-slide="next"
                    aria-label="Diapositiva siguiente">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </section>

        <section class="imperial-section imperial-categories"
                 aria-labelledby="categories-title">
            <div class="container">
                <div class="imperial-section-heading">
                    <p class="imperial-eyebrow">Encuentra tu estilo</p>
                    <h2 id="categories-title">Espacios para sentirte en casa</h2>
                    <p>Descubre nuestras colecciones para cada rincón.</p>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-4">
                        <a class="imperial-category-card"
                           href="<?= site_url('catalogo') . '?categoria=1' ?>">
                            <span class="imperial-category-card__image">
                                <img
                                    src="<?= base_url('uploads/muebles/Salas/01_Sala_Esquinera_Moderna_Q12990_stock8.jpg') ?>"
                                    alt="Sala contemporánea con sofá esquinero"
                                    loading="lazy"
                                >
                            </span>
                            <span class="imperial-category-card__label">
                                <span>Salas</span>
                                <span aria-hidden="true">&rarr;</span>
                            </span>
                        </a>
                    </div>
                    <div class="col-12 col-md-4">
                        <a class="imperial-category-card"
                           href="<?= site_url('catalogo') . '?categoria=3' ?>">
                            <span class="imperial-category-card__image">
                                <img
                                    src="<?= base_url('uploads/muebles/Dormitorios/07_Dormitorio_Moderno_Queen_Q18990_stock4.jpg') ?>"
                                    alt="Dormitorio moderno con cama y mesas de noche"
                                    loading="lazy"
                                >
                            </span>
                            <span class="imperial-category-card__label">
                                <span>Dormitorios</span>
                                <span aria-hidden="true">&rarr;</span>
                            </span>
                        </a>
                    </div>
                    <div class="col-12 col-md-4">
                        <a class="imperial-category-card"
                           href="<?= site_url('catalogo') . '?categoria=2' ?>">
                            <span class="imperial-category-card__image">
                                <img
                                    src="<?= base_url('uploads/muebles/Comedores/14_Comedor_Moderno_6_Plazas_Q12990_stock7.jpg') ?>"
                                    alt="Comedor moderno de seis plazas"
                                    loading="lazy"
                                >
                            </span>
                            <span class="imperial-category-card__label">
                                <span>Comedores</span>
                                <span aria-hidden="true">&rarr;</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="imperial-section imperial-featured"
                 aria-labelledby="featured-title">
            <div class="container">
                <div class="imperial-section-heading imperial-section-heading--split">
                    <div>
                        <p class="imperial-eyebrow">Selección Imperial</p>
                        <h2 id="featured-title">Muebles para tu próximo capítulo</h2>
                    </div>
                    <a class="imperial-text-link"
                       href="<?= site_url('catalogo') ?>">
                        Ver todo el catálogo
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="imperial-empty-state" role="status">
                    <h3>Consulta el catálogo para ver las piezas disponibles</h3>
                    <p>
                        Esta portada no recibe datos de productos. El catálogo
                        muestra la información y disponibilidad actuales.
                    </p>
                    <a class="imperial-text-link"
                       href="<?= site_url('catalogo') ?>">
                        Explorar catálogo
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        <section class="imperial-repair"
                 aria-labelledby="repair-title">
            <div class="container">
                <div class="row align-items-center g-0 imperial-repair__panel">
                    <div class="col-12 col-lg-6">
                        <img
                            class="imperial-repair__image"
                            src="<?= base_url('assets/img/restauracion-mobiliario.jpg') ?>"
                            alt="Manos expertas restauran un mueble de madera en un taller"
                            loading="lazy"
                        >
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="imperial-repair__content">
                            <p class="imperial-eyebrow">Dale una nueva vida</p>
                            <h2 id="repair-title">Los buenos muebles merecen otra historia</h2>
                            <p>
                                Cuidamos y reparamos tus muebles para que sigan
                                siendo parte de los momentos que más importan.
                            </p>
                            <a class="btn imperial-btn-primary"
                               href="<?= site_url('reparaciones/nueva') ?>">
                                Solicitar una reparación
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                            <p class="imperial-image-credit">
                                Fotografía de
                                <a href="https://www.pexels.com/photo/person-holding-black-leather-shoe-5095274/"
                                   target="_blank"
                                   rel="noopener noreferrer">cottonbro studio</a>
                                vía <a href="https://www.pexels.com/license/"
                                       target="_blank"
                                       rel="noopener noreferrer">Pexels</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?= view('frontend/partials/footer') ?>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/hero-carousel.js') ?>"></script>
</body>
</html>
