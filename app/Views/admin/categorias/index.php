<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administrar categorías | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>

<body class="bg-light">

<header class="bg-dark text-white py-4">
    <div class="container">
        <h1 class="h3 mb-1">Mueblería Imperial</h1>
        <p class="mb-0">Administración de categorías</p>
    </div>
</header>

<main class="container py-5">

    <h2 class="mb-4">Categorías de muebles</h2>

    <?php $mensaje = session()->getFlashdata('success'); ?>

    <?php if ($mensaje): ?>
        <div class="alert alert-success">
            <?= esc($mensaje) ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">

            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            No hay categorías registradas.
                        </td>
                    </tr>
                <?php else: ?>

                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= esc($categoria['id']) ?></td>

                            <td><?= esc($categoria['nombre']) ?></td>

                            <td><?= esc($categoria['descripcion'] ?? '') ?></td>

                            <td>
                                <?php if ((int) $categoria['activo'] === 1): ?>
                                    <span class="badge text-bg-success">
                                        Activa
                                    </span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">
                                        Inactiva
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="<?= site_url('admin/categorias/editar/' . $categoria['id']) ?>"
                                class="btn btn-sm btn-primary">
                                    Editar
                                </a>

                                <?php if ($categoria['activo'] == 1): ?>

                                    <form
                                        action="<?= site_url('admin/categorias/desactivar/' . $categoria['id']) ?>"
                                        method="post"
                                        class="d-inline"
                                        onsubmit="return confirm('¿Seguro que deseas desactivar esta categoría?');"
                                    >
                                        <?= csrf_field() ?>

                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Desactivar
                                        </button>
                                    </form>

                                <?php else: ?>

                                    <form
                                        action="<?= site_url('admin/categorias/reactivar/' . $categoria['id']) ?>"
                                        method="post"
                                        class="d-inline"
                                        onsubmit="return confirm('¿Seguro que deseas reactivar esta categoría?');"
                                    >
                                        <?= csrf_field() ?>

                                        <button type="submit" class="btn btn-sm btn-success">
                                            Reactivar
                                        </button>
                                    </form>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>

        </table>
    </div>

</main>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>