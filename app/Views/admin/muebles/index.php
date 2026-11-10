<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Administrar muebles | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>

<body class="bg-light">

<header class="bg-dark text-white py-4">
    <div class="container">
        <h1 class="h3 mb-1">Mueblería Imperial</h1>
        <p class="mb-0">Administración de muebles</p>
    </div>
</header>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

    <h2 class="mb-0">Inventario de muebles</h2>

        <a href="<?= site_url('admin/muebles/agregar') ?>"
            class="btn btn-success">
            + Nuevo mueble
        </a>

    </div>

        <?php $mensaje = session()->getFlashdata('success'); ?>

        <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show"
                role="alert">

                <?= esc($mensaje) ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>

            </div>
        <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">

            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($muebles)): ?>

                    <tr>
                        <td colspan="7" class="text-center">
                            No hay muebles registrados.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($muebles as $mueble): ?>

                        <tr>
                            <td><?= esc($mueble['id']) ?></td>

                            <td><?= esc($mueble['nombre']) ?></td>

                            <td><?= esc($mueble['categoria'] ?? 'Sin categoría') ?></td>

                            <td>
                                Q <?= number_format((float) $mueble['precio'], 2) ?>
                            </td>

                            <td><?= esc($mueble['stock']) ?></td>

                            <td>
                                <?php if ((int) $mueble['activo'] === 1): ?>
                                    <span class="badge text-bg-success">
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <!-- Botón para editar el mueble -->
                                <a href="<?= site_url('admin/muebles/editar/' . $mueble['id']) ?>"
                                class="btn btn-sm btn-primary me-2">
                                    Editar
                                </a>

                                <!-- Botón para activar o desactivar -->
                                <form action="<?= site_url('admin/muebles/cambiar-estado/' . $mueble['id']) ?>"
                                    method="post"
                                    class="d-inline">

                                    <?= csrf_field() ?>

                                    <button type="submit"
                                            class="btn btn-sm <?= (int) $mueble['activo'] === 1 ? 'btn-danger' : 'btn-success' ?>"
                                            onclick="return confirm('¿Confirmas que deseas cambiar el estado de este mueble?')">

                                        <?= (int) $mueble['activo'] === 1 ? 'Desactivar' : 'Activar' ?>

                                    </button>
                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>
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
