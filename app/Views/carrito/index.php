<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Mi carrito | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
</head>

<body class="bg-light">

    <header class="bg-dark text-white py-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Mueblería Imperial</h1>
                <p class="mb-0">Mi carrito de compras</p>
            </div>

            <a href="<?= site_url('catalogo') ?>"
               class="btn btn-outline-light">
                Seguir comprando
            </a>
        </div>
    </header>

    <main class="container py-5">

        <?php if (session()->getFlashdata('success')): ?>

            <div class="alert alert-success alert-dismissible fade show"
                role="alert">

                <?= esc(session()->getFlashdata('success')) ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>

            </div>

        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>

            <div class="alert alert-danger alert-dismissible fade show"
                role="alert">

                <?= esc(session()->getFlashdata('error')) ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>

            </div>

        <?php endif; ?>


        <h2 class="mb-4">Productos seleccionados</h2>

        <?php if (empty($productos)): ?>

            <div class="alert alert-info">
                Tu carrito está vacío.
            </div>

            <a href="<?= site_url('catalogo') ?>"
               class="btn btn-primary">
                Ver catálogo
            </a>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle bg-white">

                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($productos as $producto): ?>

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">

                                        <?php if (!empty($producto['imagen_principal'])): ?>

                                            <img
                                                src="<?= esc(base_url($producto['imagen_principal']), 'attr') ?>"
                                                alt="<?= esc($producto['nombre'], 'attr') ?>"
                                                width="90"
                                                height="70"
                                                style="object-fit: cover;"
                                            >

                                        <?php endif; ?>

                                        <strong>
                                            <?= esc($producto['nombre']) ?>
                                        </strong>

                                    </div>
                                </td>

                                <td>
                                    Q <?= number_format((float) $producto['precio'], 2) ?>
                                </td>

                                <!-- CANTIDAD -->
                                <td>
                                    <form action="<?= site_url('carrito/actualizar') ?>"
                                        method="post"
                                        class="d-flex align-items-center gap-2">

                                        <?= csrf_field() ?>

                                        <input type="hidden"
                                            name="mueble_id"
                                            value="<?= esc($producto['id'], 'attr') ?>">

                                        <input type="number"
                                            name="cantidad"
                                            class="form-control form-control-sm"
                                            min="1"
                                            max="<?= (int) $producto['stock'] ?>"
                                            value="<?= (int) $producto['cantidad'] ?>"
                                            style="width: 80px;"
                                            required>

                                        <button type="submit" class="btn btn-primary btn-sm">
                                            Actualizar
                                        </button>
                                    </form>
                                </td>

                                <!-- SUBTOTAL -->
                                <td class="fw-bold">
                                    Q <?= number_format((float) $producto['subtotal'], 2) ?>
                                </td>

                                <!-- ACCIONES -->
                                <td>
                                    <form action="<?= site_url('carrito/eliminar') ?>"
                                        method="post"
                                        onsubmit="return confirm('¿Deseas eliminar este producto del carrito?');">

                                        <?= csrf_field() ?>

                                        <input type="hidden"
                                            name="mueble_id"
                                            value="<?= esc($producto['id'], 'attr') ?>">

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body text-end">

                    <h3 class="h4">
                        Total:
                        <span class="text-success">
                            Q <?= number_format((float) $total, 2) ?>
                        </span>
                    </h3>

                    
                    <?php if (
                        session('logueado') === true &&
                        (int) session('rol_id') === 4
                    ): ?>

                        <form
                            action="<?= site_url('pedidos/confirmar') ?>"
                            method="post"
                            class="mt-3"
                            onsubmit="return confirm('¿Deseas confirmar este pedido?');"
                        >
                            <?= csrf_field() ?>

                            <button type="submit" class="btn btn-success">
                                Confirmar pedido
                            </button>
                        </form>

                    <?php else: ?>

                        <div class="mt-3">
                            <p class="text-muted mb-2">
                                Inicia sesión como cliente para confirmar tu pedido.
                            </p>

                            <a href="<?= site_url('login') ?>"
                            class="btn btn-primary">
                                Iniciar sesión
                            </a>
                        </div>

                    <?php endif; ?>


                </div>
            </div>

        <?php endif; ?>

    </main>

    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">
            &copy; <?= date('Y') ?> Mueblería Imperial
        </p>
    </footer>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
