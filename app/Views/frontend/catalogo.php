<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Catálogo | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet"
          href="<?= base_url('assets/css/estilos.css') ?>">
</head>

<body class="imperial-catalog-page">
    <?= view('frontend/partials/header') ?>

    <main class="container py-5 imperial-catalog-main">
        
        <?php if (session()->getFlashdata('success')): ?>

            <div class="alert alert-success alert-dismissible fade show"
                role="alert">

                <?= esc(session()->getFlashdata('success')) ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>

            </div>

        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>

            <div class="alert alert-danger alert-dismissible fade show"
                role="alert">

                <?= esc(session()->getFlashdata('error')) ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>

            </div>

        <?php endif; ?>


        <h2 class="imperial-catalog-title">Nuestros muebles</h2>
        
        <form action="<?= site_url('catalogo') ?>" method="get" class="row g-3 mb-4 imperial-catalog-search">

            <div class="col-12 col-md-9">
                <label for="buscar" class="form-label">
                    Buscar muebles
                </label>

                <input
                    type="search"
                    id="buscar"
                    name="buscar"
                    class="form-control"
                    placeholder="Ejemplo: sofá, mesa, cama..."
                    value="<?= esc($buscar ?? '', 'attr') ?>"
                >
            </div>

            <div class="col-12 col-md-3 d-flex align-items-end">
                <button type="submit"
                        class="btn imperial-btn-primary imperial-catalog-search-button">
                    Buscar
                </button>
            </div>

        </form>


        <div class="row g-4">

            <?php if (empty($muebles)): ?>

                <div class="col-12">
                    
                    <div class="alert imperial-catalog-empty">

                        <?php if (!empty($buscar)): ?>

                            No encontramos muebles que coincidan con
                            <strong><?= esc($buscar) ?></strong>.

                        <?php else: ?>

                            Actualmente no hay muebles disponibles.

                        <?php endif; ?>

                    </div>

                </div>

            <?php else: ?>

                <?php foreach ($muebles as $mueble): ?>

                    <div class="col-12 col-md-6 col-lg-4">

                        <article class="imperial-product-card h-100">

                            <?php if (!empty($mueble['imagen_principal'])): ?>

                                <img
                                    src="<?= esc(base_url($mueble['imagen_principal']), 'attr') ?>"
                                    class="imperial-product-card__image"
                                    alt="<?= esc($mueble['nombre'], 'attr') ?>"
                                >

                            <?php else: ?>

                                <div class="imperial-product-card__placeholder"
                                     role="img"
                                     aria-label="Imagen no disponible para <?= esc($mueble['nombre'], 'attr') ?>">

                                    <span>
                                        Imagen no disponible
                                    </span>

                                </div>

                            <?php endif; ?>

                            <div class="imperial-product-card__body">

                                <span class="imperial-product-card__category">
                                    <?= esc($mueble['categoria']) ?>
                                </span>

                                <h3>
                                    <?= esc($mueble['nombre']) ?>
                                </h3>

                                <p class="imperial-product-card__description">
                                    <?= esc($mueble['descripcion'] ?? '') ?>
                                </p>

                                <p class="imperial-product-card__price mt-auto">
                                    Q <?= number_format(
                                        (float) $mueble['precio'],
                                        2
                                    ) ?>
                                </p>

                                
                                <?php if ($mueble['stock'] > 0): ?>

                                    <span class="imperial-product-card__availability">
                                        Disponible
                                    </span>

                                    <form action="<?= site_url('carrito/agregar') ?>"
                                        method="post">

                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="mueble_id"
                                            value="<?= esc($mueble['id'], 'attr') ?>"
                                        >

                                        <button type="submit"
                                                class="btn imperial-btn-outline w-100">
                                            Agregar al carrito
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="imperial-product-card__availability is-unavailable">
                                        Agotado
                                    </span>

                                    <button class="btn imperial-btn-disabled w-100 mt-3" disabled>
                                        No disponible
                                    </button>

                                <?php endif; ?>


                            </div>
                            </article>

                        </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

            <?php if ($pager->getPageCount() > 1): ?>
            <nav class="mt-5 d-flex justify-content-center"
                 aria-label="Paginación del catálogo">

                <?= $pager->only(['buscar', 'categoria'])->links() ?>

            </nav>
        <?php endif; ?>

    </main>

    <?= view('frontend/partials/footer') ?>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
