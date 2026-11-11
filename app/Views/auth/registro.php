<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta | Mueblería Imperial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h2 class="text-center mb-4">Crear cuenta</h2>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <div><?= esc($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('registro') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre"
                                   class="form-control" maxlength="100"
                                   value="<?= esc(old('nombre')) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" name="apellido" id="apellido"
                                   class="form-control" maxlength="100"
                                   value="<?= esc(old('apellido')) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" name="correo" id="correo"
                                   class="form-control" maxlength="150"
                                   value="<?= esc(old('correo')) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" id="password"
                                   class="form-control" minlength="8"
                                   maxlength="72" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirmar_password" class="form-label">
                                Confirmar contraseña
                            </label>
                            <input type="password" name="confirmar_password"
                                   id="confirmar_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono (opcional)</label>
                            <input type="tel" name="telefono" id="telefono"
                                   class="form-control" maxlength="20"
                                   value="<?= esc(old('telefono')) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">
                                Dirección de entrega (opcional)
                            </label>
                            <textarea name="direccion" id="direccion"
                                      class="form-control" rows="3"
                                      maxlength="1000"><?= esc(old('direccion')) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Registrarme
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        ¿Ya tienes cuenta?
                        <a href="<?= site_url('login') ?>">Iniciar sesión</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
