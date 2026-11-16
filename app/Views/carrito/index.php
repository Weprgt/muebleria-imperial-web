<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Mi carrito | Mueblería Imperial</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet"
          href="<?= base_url('assets/css/estilos.css') ?>">
</head>

<body class="imperial-cart-page">
    <?= view('frontend/partials/header') ?>

    <main class="container imperial-cart-main">

        <section class="imperial-cart-heading"
                 aria-labelledby="cart-page-title">
            <div>
                <p class="imperial-eyebrow">Tu selección</p>
                <h1 id="cart-page-title">Mi carrito de compras</h1>
                <p class="imperial-cart-heading__intro">
                    Revisa las piezas que elegiste para tu hogar.
                </p>
            </div>

            <div class="imperial-cart-heading__actions">
                <?php if (
                    session('logueado') === true &&
                    (int) session('rol_id') === 4
                ): ?>
                    <a href="<?= site_url('mis-pedidos') ?>"
                       class="btn imperial-cart-secondary">
                        Mis pedidos
                    </a>
                <?php endif; ?>

                <a href="<?= site_url('catalogo') ?>"
                   class="btn imperial-cart-continue">
                    Seguir comprando
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </section>

        <?php if (session()->getFlashdata('success')): ?>

            <div class="imperial-cart-alert imperial-cart-alert--success alert alert-success alert-dismissible fade show"
                role="alert">

                <?= esc(session()->getFlashdata('success')) ?>
                <?php if (
                    session('logueado') === true &&
                    (int) session('rol_id') === 4
                ): ?>
                    <div class="mt-3">
                        <a href="<?= site_url('mis-pedidos') ?>"
                        class="btn imperial-cart-secondary btn-sm">
                            Ver mis pedidos
                        </a>
                    </div>
                <?php endif; ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>

            </div>

        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>

            <div class="imperial-cart-alert imperial-cart-alert--error alert alert-danger alert-dismissible fade show"
                role="alert">

                <?= esc(session()->getFlashdata('error')) ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>

            </div>

        <?php endif; ?>


        <?php if (empty($productos)): ?>
            <section class="imperial-cart-empty"
                     aria-labelledby="cart-empty-title"
                     role="status">
                <div class="imperial-cart-empty__icon" aria-hidden="true">
                    <svg viewBox="0 0 64 64" fill="none">
                        <path d="M7 10h7l6.2 29h27.2l6.1-20H17.2"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round" />
                        <circle cx="25" cy="49" r="3"
                                stroke="currentColor"
                                stroke-width="2" />
                        <circle cx="45" cy="49" r="3"
                                stroke="currentColor"
                                stroke-width="2" />
                        <path d="M29 27h12M35 21v12"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round" />
                    </svg>
                </div>
                <p class="imperial-eyebrow">Un espacio por llenar</p>
                <h2 id="cart-empty-title">Tu carrito está vacío</h2>
                <p class="imperial-cart-empty__message">
                    Descubre nuestros muebles y encuentra la pieza ideal para tu hogar.
                </p>
                <a href="<?= site_url('catalogo') ?>"
                   class="btn imperial-btn-primary">
                    Explorar catálogo
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </section>

        <?php else: ?>

            <div class="imperial-cart-layout">
                <section class="imperial-cart-products"
                         aria-labelledby="cart-products-title">
                    <div class="imperial-cart-section-heading">
                        <p class="imperial-eyebrow">Tus piezas</p>
                        <h2 id="cart-products-title">Productos seleccionados</h2>
                    </div>

                    <div class="imperial-cart-items">
                        <?php foreach ($productos as $producto): ?>
                            <article class="imperial-cart-item">
                                <div class="imperial-cart-item__product">
                                    <div class="imperial-cart-item__image">
                                        <?php if (!empty($producto['imagen_principal'])): ?>
                                            <img
                                                src="<?= esc(base_url($producto['imagen_principal']), 'attr') ?>"
                                                alt="<?= esc($producto['nombre'], 'attr') ?>"
                                                width="112"
                                                height="96"
                                            >
                                        <?php else: ?>
                                            <span>Imagen no disponible</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="imperial-cart-item__details">
                                        <h3><?= esc($producto['nombre']) ?></h3>
                                        <p>
                                            Precio unitario
                                            <strong>
                                                Q <?= number_format((float) $producto['precio'], 2) ?>
                                            </strong>
                                        </p>
                                    </div>
                                </div>

                                <div class="imperial-cart-item__controls">
                                    <div class="imperial-cart-quantity">
                                        <label for="cantidad-<?= esc($producto['id'], 'attr') ?>">
                                            Cantidad
                                        </label>
                                        <form action="<?= site_url('carrito/actualizar') ?>"
                                              method="post"
                                              class="imperial-cart-quantity-form">
                                            <?= csrf_field() ?>

                                            <input type="hidden"
                                                   name="mueble_id"
                                                   value="<?= esc($producto['id'], 'attr') ?>">

                                            <input type="number"
                                                   id="cantidad-<?= esc($producto['id'], 'attr') ?>"
                                                   name="cantidad"
                                                   class="form-control imperial-cart-quantity-input"
                                                   min="1"
                                                   max="<?= (int) $producto['stock'] ?>"
                                                   value="<?= (int) $producto['cantidad'] ?>"
                                                   required>

                                            <button type="submit"
                                                    class="btn imperial-cart-update">
                                                Actualizar
                                            </button>
                                        </form>
                                    </div>

                                    <div class="imperial-cart-item__subtotal">
                                        <span>Subtotal</span>
                                        <strong>
                                            Q <?= number_format((float) $producto['subtotal'], 2) ?>
                                        </strong>
                                    </div>

                                    <form action="<?= site_url('carrito/eliminar') ?>"
                                          method="post"
                                          onsubmit="return confirm('¿Deseas eliminar este producto del carrito?');">
                                        <?= csrf_field() ?>

                                        <input type="hidden"
                                               name="mueble_id"
                                               value="<?= esc($producto['id'], 'attr') ?>">

                                        <button type="submit"
                                                class="btn imperial-cart-remove">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <aside class="imperial-cart-summary"
                       aria-labelledby="cart-summary-title">
                    <p class="imperial-eyebrow">Resumen</p>
                    <h2 id="cart-summary-title">Tu pedido</h2>

                    <div class="imperial-cart-summary__total">
                        <span>Total</span>
                        <strong>
                            Q <?= number_format((float) $total, 2) ?>
                        </strong>
                    </div>

                    <?php if (
                        session('logueado') === true &&
                        (int) session('rol_id') === 4
                    ): ?>
                        <form
                            action="<?= site_url('pedidos/confirmar') ?>"
                            method="post"
                            class="imperial-cart-summary__action"
                            onsubmit="return confirm('¿Deseas confirmar este pedido?');"
                        >
                            <?= csrf_field() ?>

                            <button type="submit" class="btn imperial-btn-primary w-100">
                                Confirmar pedido
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="imperial-cart-summary__action">
                            <p>
                                Inicia sesión como cliente para confirmar tu pedido.
                            </p>

                            <a href="<?= site_url('login') ?>"
                               class="btn imperial-btn-primary w-100">
                                Iniciar sesión
                            </a>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>

        <?php endif; ?>

    </main>

    <?= view('frontend/partials/footer') ?>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
