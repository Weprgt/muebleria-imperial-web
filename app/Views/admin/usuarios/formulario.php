<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear empleado | Mueblería Imperial</title>

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
                <p class="mb-0">Administración de empleados</p>
            </div>
            <a href="<?= site_url('admin') ?>" class="btn btn-outline-light imperial-form-page__header-link">
                Volver al panel administrativo
            </a>
        </div>
    </header>

    <main class="container py-5 imperial-form-page__main">
        <div class="mb-4">
            <h2 class="imperial-form-page__title mb-1">Crear empleado</h2>
            <p class="text-secondary mb-0">
                Registra una cuenta para el equipo de Ventas o Taller.
            </p>
        </div>

        <?php $errores = session()->getFlashdata('errors') ?? []; ?>
        <?php if ($errores !== []): ?>
            <div class="alert alert-danger imperial-form__message imperial-form__message--error" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errores as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php $empleadoInput = session()->getFlashdata('empleadoInput') ?? []; ?>

        <section class="card employee-form-card imperial-form-panel shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form action="<?= site_url('admin/usuarios/guardar') ?>"
                      method="post"
                      class="imperial-form imperial-form--admin">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-6 imperial-form__group">
                            <label for="nombre" class="form-label imperial-form__label">Nombre</label>
                            <input type="text"
                                   class="form-control imperial-form__control"
                                   id="nombre"
                                   name="nombre"
                                   minlength="2"
                                   maxlength="100"
                                   autocomplete="given-name"
                                   value="<?= esc($empleadoInput['nombre'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="col-md-6 imperial-form__group">
                            <label for="apellido" class="form-label imperial-form__label">Apellido</label>
                            <input type="text"
                                   class="form-control imperial-form__control"
                                   id="apellido"
                                   name="apellido"
                                   minlength="2"
                                   maxlength="100"
                                   autocomplete="family-name"
                                   value="<?= esc($empleadoInput['apellido'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="col-12 imperial-form__group">
                            <label for="correo" class="form-label imperial-form__label">Correo electrónico</label>
                            <input type="email"
                                   class="form-control imperial-form__control"
                                   id="correo"
                                   name="correo"
                                   maxlength="150"
                                   autocomplete="email"
                                   value="<?= esc($empleadoInput['correo'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="col-md-6 imperial-form__group">
                            <label for="password" class="form-label imperial-form__label">Contraseña</label>
                            <input type="password"
                                   class="form-control imperial-form__control"
                                   id="password"
                                   name="password"
                                   minlength="8"
                                   maxlength="72"
                                   autocomplete="new-password"
                                   required>
                            <div class="form-text imperial-form__help">Debe tener al menos 8 caracteres.</div>
                        </div>

                        <div class="col-md-6 imperial-form__group">
                            <label for="confirmar_password" class="form-label imperial-form__label">
                                Confirmar contraseña
                            </label>
                            <input type="password"
                                   class="form-control imperial-form__control"
                                   id="confirmar_password"
                                   name="confirmar_password"
                                   minlength="8"
                                   maxlength="72"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <div class="col-12 imperial-form__group">
                            <label for="rol_id" class="form-label imperial-form__label">Rol</label>
                            <select class="form-select imperial-form__control"
                                    id="rol_id"
                                    name="rol_id"
                                    required>
                                <option value="">Selecciona un rol</option>
                                <option value="2"
                                    <?= ($empleadoInput['rol_id'] ?? '') === '2' ? 'selected' : '' ?>>
                                    Ventas
                                </option>
                                <option value="3"
                                    <?= ($empleadoInput['rol_id'] ?? '') === '3' ? 'selected' : '' ?>>
                                    Taller
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4 imperial-form__actions">
                        <button type="submit" class="btn imperial-form__button imperial-form__button--primary">
                            Crear empleado
                        </button>
                        <a href="<?= site_url('admin/usuarios') ?>"
                           class="btn btn-outline-secondary imperial-form__button imperial-form__button--secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
