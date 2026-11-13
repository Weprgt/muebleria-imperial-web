
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Nueva reparación - Mueblería Imperial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Solicitar reparación</h1>

        <a href="<?= site_url('cliente') ?>"
           class="btn btn-outline-secondary">
            Volver
        </a>
    </div>

    <div class="alert alert-info">
        La evaluación inicial es gratuita.
        Recibirás un presupuesto antes de autorizar
        cualquier reparación.
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form
        action="<?= site_url('reparaciones/guardar') ?>"
        method="post"
        enctype="multipart/form-data"
        class="card shadow-sm"
    >
        <?= csrf_field() ?>

        <div class="card-body">

            <h2 class="h5 mb-3">Datos del mueble</h2>

            <div class="mb-3">
                <label for="nombre_mueble"
                       class="form-label">
                    Nombre del mueble
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="nombre_mueble"
                    name="nombre_mueble"
                    maxlength="150"
                    value="<?= esc(old('nombre_mueble')) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="descripcion_problema"
                       class="form-label">
                    Describe el problema
                </label>

                <textarea
                    class="form-control"
                    id="descripcion_problema"
                    name="descripcion_problema"
                    rows="4"
                    required
                ><?= esc(old('descripcion_problema')) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="fotografia"
                       class="form-label">
                    Fotografía del mueble
                </label>

                <input
                    type="file"
                    class="form-control"
                    id="fotografia"
                    name="fotografia"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <div class="form-text">
                    Formatos permitidos: JPG, PNG o WEBP.
                    La fotografía es opcional.
                </div>
            </div>

            <div class="alert alert-secondary">
                Podrás solicitar reparaciones de muebles
                comprados en nuestra tienda o de muebles externos.
            </div>

            <button type="submit"
                    class="btn btn-primary">
                Enviar solicitud
            </button>

        </div>
    </form>

</div>

</body>
</html>
