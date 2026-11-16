<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Agregar mueble | Mueblería Imperial</title>

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
            <p class="mb-0">Administración de muebles</p>
        </div>
    </div>
</header>

<main class="container py-5 imperial-form-page__main">

    <h2 class="mb-4 imperial-form-page__title">
        <?= isset($mueble) ? 'Editar mueble' : 'Agregar nuevo mueble' ?>
    </h2>
    
        <?php $errores = session()->getFlashdata('errors'); ?>

        <?php if (! empty($errores)): ?>
            <div class="alert alert-danger imperial-form__message imperial-form__message--error" role="alert">
                <h5 class="alert-heading">
                    No se pudo guardar el mueble
                </h5>

                <ul class="mb-0">
                    <?php foreach ($errores as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    <form action="<?= isset($mueble)
        ? site_url('admin/muebles/actualizar/' . $mueble['id'])
        : site_url('admin/muebles/guardar') ?>"
        method="post"
        enctype="multipart/form-data"
        class="imperial-form imperial-form--card">

        <?= csrf_field() ?>

        <div class="mb-3 imperial-form__group">
            <label for="categoria_id" class="form-label imperial-form__label">
                Categoría
            </label>

            <select name="categoria_id"
                    id="categoria_id"
                    class="form-select imperial-form__control"
                    required>

                <option value="">Seleccione una categoría</option>

                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= esc($categoria['id']) ?>"
                        <?= (string) old('categoria_id', $mueble['categoria_id'] ?? '') === (string) $categoria['id'] ? 'selected' : '' ?>>
                        <?= esc($categoria['nombre']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="mb-3 imperial-form__group">
            <label for="nombre" class="form-label imperial-form__label">
                Nombre del mueble
            </label>

            <input type="text"
                   name="nombre"
                   id="nombre"
                   value="<?= esc(old('nombre', $mueble['nombre'] ?? '')) ?>"
                   class="form-control imperial-form__control"
                   maxlength="150"
                   required>
        </div>

        <div class="mb-3 imperial-form__group">
            <label for="descripcion" class="form-label imperial-form__label">
                Descripción
            </label>

            <textarea name="descripcion"
                      id="descripcion"
                      class="form-control imperial-form__control"
                      rows="4"><?= esc(old('descripcion', $mueble['descripcion'] ?? '')) ?>
            </textarea>
        </div>

        <div class="mb-3 imperial-form__group">
            <label for="precio" class="form-label imperial-form__label">
                Precio (Q)
            </label>

            <input type="number"
                   name="precio"
                   id="precio"
                   value="<?= esc(old('precio', $mueble['precio'] ?? '')) ?>"
                   class="form-control imperial-form__control"
                   min="0.01"
                   max="99999999.99"
                   step="0.01"
                   required>
        </div>

        <div class="mb-3 imperial-form__group">
            <label for="stock" class="form-label imperial-form__label">
                Existencias
            </label>

            <input type="number"
                   name="stock"
                   id="stock"
                   value="<?= esc(old('stock', $mueble['stock'] ?? '')) ?>"
                   class="form-control imperial-form__control"
                   min="0"
                   step="1"
                   required>
        </div>

        <div class="mb-4 imperial-form__group">
            <label for="imagen_principal" class="form-label imperial-form__label">
                Fotografía del mueble (opcional)
            </label>

            <input type="file"
                name="imagen_principal"
                id="imagen_principal"
                class="form-control imperial-form__control"
                accept=".jpg,.jpeg,.png,.webp">

            <div class="form-text imperial-form__help">
                Formatos permitidos: JPG, PNG y WebP.
                Tamaño máximo: 2 MB.
            </div>
        </div>

        <div class="d-flex gap-2 imperial-form__actions">
            <button type="submit" class="btn btn-primary imperial-form__button imperial-form__button--primary">
                <?= isset($mueble) ? 'Actualizar mueble' : 'Guardar mueble' ?>
            </button>

            <a href="<?= site_url('admin/muebles') ?>"
               class="btn btn-secondary imperial-form__button imperial-form__button--secondary">
                Cancelar
            </a>
        </div>

    </form>

</main>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
