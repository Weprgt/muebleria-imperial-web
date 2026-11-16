<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Formulario de categorías | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet"
          href="<?= base_url('assets/css/estilos.css') ?>">
</head>

<body class="imperial-form-page imperial-form-page--admin">

<header class="imperial-form-page__header py-4">
    <div class="container imperial-form-page__header-inner">
        <img
            class="imperial-form-page__logo"
            src="<?= base_url('assets/img/logo-imperial.png') ?>"
            alt=""
            aria-hidden="true"
            width="56"
            height="56"
        >
        <div>
            <h1 class="h3 mb-1">Mueblería Imperial</h1>
            <p class="mb-0">Administración de categorías</p>
        </div>
    </div>
</header>

<main class="container py-5 imperial-form-page__main">

    <h2 class="mb-4 imperial-form-page__title">
        <?= isset($categoria) ? 'Editar categoría' : 'Agregar categoría' ?>
    </h2>

    <?php $errores = session()->getFlashdata('errors'); ?>

    <?php if (! empty($errores)): ?>
        <div class="alert alert-danger imperial-form__message imperial-form__message--error" role="alert">
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
          method="post"
          class="imperial-form imperial-form--card">

        <?= csrf_field() ?>

        <div class="mb-3 imperial-form__group">
            <label for="nombre" class="form-label imperial-form__label">
                Nombre de la categoría
            </label>

            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control imperial-form__control"
                   maxlength="100"
                   value="<?= esc(old('nombre', $categoria['nombre'] ?? '')) ?>"
                   required>
        </div>

        <div class="mb-4 imperial-form__group">
            <label for="descripcion" class="form-label imperial-form__label">
                Descripción
            </label>

            <textarea name="descripcion"
                      id="descripcion"
                      class="form-control imperial-form__control"
                      rows="4"><?= esc(old('descripcion', $categoria['descripcion'] ?? '')) ?></textarea>
        </div>

        <div class="d-flex gap-2 imperial-form__actions">
            <button type="submit" class="btn btn-primary imperial-form__button imperial-form__button--primary">
                <?= isset($categoria) ? 'Actualizar categoría' : 'Guardar categoría' ?>
            </button>

            <a href="<?= site_url('admin/categorias') ?>"
               class="btn btn-secondary imperial-form__button imperial-form__button--secondary">
                Cancelar
            </a>
        </div>

    </form>

</main>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>