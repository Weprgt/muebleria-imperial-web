<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Catálogo | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>

<body class="bg-light">

    <header class="bg-dark text-white py-4">
        <div class="container">
            <h1 class="h3 mb-1">Mueblería Imperial</h1>
            <p class="mb-0">Catálogo de muebles</p>
        </div>
    </header>

    <main class="container py-5">
        
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


        <h2 class="mb-4">Nuestros muebles</h2>
        
        <form action="<?= site_url('catalogo') ?>" method="get" class="row g-3 mb-4">

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
                <button type="submit" class="btn btn-primary w-100">
                    Buscar
                </button>
            </div>

        </form>


        <div class="row g-4">

            <?php if (empty($muebles)): ?>

                <div class="col-12">
                    
                    <div class="alert alert-info">

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

                        <div class="card h-100 shadow-sm">

                            <?php if (!empty($mueble['imagen_principal'])): ?>

                                <img
                                    src="<?= esc(base_url($mueble['imagen_principal']), 'attr') ?>"
                                    class="card-img-top"
                                    alt="<?= esc($mueble['nombre'], 'attr') ?>"
                                    style="height: 220px; object-fit: cover;"
                                >

                            <?php else: ?>

                                <div class="bg-secondary-subtle
                                            d-flex align-items-center
                                            justify-content-center"
                                     style="height: 220px;">

                                    <span class="text-secondary">
                                        Imagen no disponible
                                    </span>

                                </div>

                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">

                                <span class="text-muted small mb-2">
                                    <?= esc($mueble['categoria']) ?>
                                </span>

                                <h3 class="card-title h5">
                                    <?= esc($mueble['nombre']) ?>
                                </h3>

                                <p class="card-text text-secondary">
                                    <?= esc($mueble['descripcion'] ?? '') ?>
                                </p>

                                <p class="fs-4 fw-bold text-success mt-auto">
                                    Q <?= number_format(
                                        (float) $mueble['precio'],
                                        2
                                    ) ?>
                                </p>

                                
                                <?php if ($mueble['stock'] > 0): ?>

                                    <span class="badge text-bg-success mb-3">
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

                                        <button type="submit" class="btn btn-primary w-100">
                                            Agregar al carrito
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="badge text-bg-danger">
                                        Agotado
                                    </span>

                                    <button class="btn btn-secondary w-100 mt-3" disabled>
                                        No disponible
                                    </button>

                                <?php endif; ?>


                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </main>

    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">
            &copy; <?= date('Y') ?> Mueblería Imperial
        </p>
    </footer>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
