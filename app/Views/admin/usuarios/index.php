<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar empleados | Mueblería Imperial</title>

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
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="imperial-form-page__title mb-1">Empleados</h2>
                <p class="text-secondary mb-0">
                    Administra las cuentas de Ventas y Taller.
                </p>
            </div>

            <a href="<?= site_url('admin/usuarios/crear') ?>"
               class="btn imperial-form__button imperial-form__button--primary">
                Crear empleado
            </a>
        </div>

        <?php foreach (['success', 'error'] as $tipoMensaje): ?>
            <?php $mensaje = session()->getFlashdata($tipoMensaje); ?>
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje === 'success' ? 'success' : 'danger' ?> imperial-form__message <?= $tipoMensaje === 'success' ? 'imperial-form__message--success' : 'imperial-form__message--error' ?>"
                     role="alert">
                    <?= esc($mensaje) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <section class="card employee-card imperial-form-panel shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive imperial-form-table-wrap">
                    <table class="table table-hover align-middle mb-0 imperial-form-table">
                        <thead class="imperial-form-table__head">
                            <tr>
                                <th scope="col" class="px-4 py-3">Nombre completo</th>
                                <th scope="col" class="py-3">Correo electrónico</th>
                                <th scope="col" class="py-3">Rol</th>
                                <th scope="col" class="py-3">Estado</th>
                                <th scope="col" class="py-3">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-5">
                                        No hay empleados registrados.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <?php
                                    $id = (int) $usuario['id'];
                                    $rolNombre = (int) $usuario['rol_id'] === 2
                                        ? 'Ventas'
                                        : 'Taller';
                                    $activo = (int) $usuario['activo'] === 1;
                                    ?>
                                    <tr>
                                        <td class="px-4">
                                            <?= esc(trim($usuario['nombre'] . ' ' . $usuario['apellido'])) ?>
                                        </td>
                                        <td><?= esc($usuario['correo']) ?></td>
                                        <td><?= esc($rolNombre) ?></td>
                                        <td>
                                            <?php if ($activo): ?>
                                                <span class="badge imperial-form-status imperial-form-status--active">Activo</span>
                                            <?php else: ?>
                                                <span class="badge imperial-form-status imperial-form-status--inactive">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form action="<?= site_url('admin/usuarios/' . $id . '/cambiar-estado') ?>"
                                                  method="post"
                                                  class="imperial-form imperial-form--inline"
                                                  onsubmit="return confirm('¿Confirmas el cambio de estado de esta cuenta?');">
                                                <?= csrf_field() ?>
                                                <button type="submit"
                                                        class="btn btn-sm imperial-form__button <?= $activo ? 'btn-outline-danger imperial-form__button--danger' : 'btn-outline-success imperial-form__button--success' ?>">
                                                    <?= $activo ? 'Desactivar' : 'Activar' ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
