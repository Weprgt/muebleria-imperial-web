<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Formulario de categorías | Mueblería Imperial</title>

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

    <h2 class="mb-4">
        <?= isset($categoria) ? 'Editar categoría' : 'Agregar categoría' ?>
    </h2>

    <?php $errores = session()->getFlashdata('errors'); ?>

    <?php if (! empty($errores)): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= isset($categoria)
        ? site_url('admin/categorias/actualizar/' . $categoria['id'])
        : site_url('admin/categorias/guardar') ?>"
          method="post">

        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="nombre" class="form-label">
                Nombre de la categoría
            </label>

            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control"
                   maxlength="100"
                   value="<?= esc(old('nombre', $categoria['nombre'] ?? '')) ?>"
                   required>
        </div>

        <div class="mb-4">
            <label for="descripcion" class="form-label">
                Descripción
            </label>

            <textarea name="descripcion"
                      id="descripcion"
                      class="form-control"
                      rows="4"><?= esc(old('descripcion', $categoria['descripcion'] ?? '')) ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <?= isset($categoria) ? 'Actualizar categoría' : 'Guardar categoría' ?>
            </button>

            <a href="<?= site_url('admin/categorias') ?>"
               class="btn btn-secondary">
                Cancelar
            </a>
        </div>

    </form>

</main>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>