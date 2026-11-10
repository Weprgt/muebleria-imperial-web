<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Agregar mueble | Mueblería Imperial</title>

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

    <h2 class="mb-4">
        <?= isset($mueble) ? 'Editar mueble' : 'Agregar nuevo mueble' ?>
    </h2>
    
        <?php $errores = session()->getFlashdata('errors'); ?>

        <?php if (! empty($errores)): ?>
            <div class="alert alert-danger" role="alert">
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
        method="post">

        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">
                Categoría
            </label>

            <select name="categoria_id"
                    id="categoria_id"
                    class="form-select"
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

        <div class="mb-3">
            <label for="nombre" class="form-label">
                Nombre del mueble
            </label>

            <input type="text"
                   name="nombre"
                   id="nombre"
                   value="<?= esc(old('nombre', $mueble['nombre'] ?? '')) ?>"
                   class="form-control"
                   maxlength="150"
                   required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">
                Descripción
            </label>

            <textarea name="descripcion"
                      id="descripcion"
                      class="form-control"
                      rows="4"><?= esc(old('descripcion', $mueble['descripcion'] ?? '')) ?>
            </textarea>
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">
                Precio (Q)
            </label>

            <input type="number"
                   name="precio"
                   id="precio"
                   value="<?= esc(old('precio', $mueble['precio'] ?? '')) ?>"
                   class="form-control"
                   min="0.01"
                   max="99999999.99"
                   step="0.01"
                   required>
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">
                Existencias
            </label>

            <input type="number"
                   name="stock"
                   id="stock"
                   value="<?= esc(old('stock', $mueble['stock'] ?? '')) ?>"
                   class="form-control"
                   min="0"
                   step="1"
                   required>
        </div>

        <div class="mb-4">
            <label for="imagen_principal" class="form-label">
                Ruta de imagen (opcional)
            </label>

            <input type="text"
                   name="imagen_principal"
                   id="imagen_principal"
                   value="<?= esc(old('imagen_principal', $mueble['imagen_principal'] ?? '')) ?>"
                   class="form-control"
                   placeholder="uploads/muebles/sofa.jpg">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <?= isset($mueble) ? 'Actualizar mueble' : 'Guardar mueble' ?>
            </button>

            <a href="<?= site_url('admin/muebles') ?>"
               class="btn btn-secondary">
                Cancelar
            </a>
        </div>

    </form>

</main>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
