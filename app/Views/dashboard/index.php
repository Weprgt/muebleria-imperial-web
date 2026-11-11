<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Panel de control - Mueblería Imperial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f2f0eb;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }

        .navbar-imperial {
            background: #ffffff;
            border-bottom: 1px solid #e9e2d9;
        }

        .brand {
            color: #403226;
            font-weight: 700;
            text-decoration: none;
        }

        .brand:hover {
            color: #795b3b;
        }

        .page-title {
            color: #403226;
            font-weight: 700;
        }

        .dashboard-card {
            background: #ffffff;
            border: 1px solid #e9e2d9;
            border-radius: 16px;
            padding: 28px;
            height: 100%;
            transition: transform .2s, box-shadow .2s;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.07);
        }

        .dashboard-card h2 {
            color: #403226;
            font-size: 1.25rem;
        }

        .dashboard-card p {
            color: #786d62;
        }

        .btn-imperial {
            background: #795b3b;
            border-color: #795b3b;
            color: #ffffff;
        }

        .btn-imperial:hover {
            background: #60472e;
            border-color: #60472e;
            color: #ffffff;
        }

        .btn-outline-imperial {
            border: 1px solid #795b3b;
            color: #795b3b;
        }

        .btn-outline-imperial:hover {
            background: #795b3b;
            color: #ffffff;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-imperial py-3">
    <div class="container">

        <a class="brand fs-5" href="<?= site_url('dashboard') ?>">
            Mueblería Imperial
        </a>

        <form action="<?= site_url('logout') ?>" method="post" class="mb-0">
            <?= csrf_field() ?>

            <button type="submit" class="btn btn-outline-danger btn-sm">
                Cerrar sesión
            </button>
        </form>

    </div>
</nav>

<main class="container py-5">

    <div class="mb-4">
        <h1 class="page-title">Panel de administración</h1>

        <p class="text-secondary">
            Bienvenido,
            <strong><?= esc(session()->get('nombre')) ?></strong>.
            Administra tu tienda desde un solo lugar.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-6">
            <section class="dashboard-card">

                <div class="fs-1 mb-3">🛋️</div>

                <h2>Catálogo de muebles</h2>

                <p>
                    Explora los productos disponibles en nuestra tienda.
                </p>

                <a href="<?= site_url('catalogo') ?>"
                   class="btn btn-imperial">
                    Ver catálogo
                </a>

            </section>
        </div>

        <?php if ((int) session()->get('rol_id') === 1): ?>
            <div class="col-md-6">
                <section class="dashboard-card">

                    <div class="fs-1 mb-3">⚙️</div>

                    <h2>Administración</h2>

                    <p>
                        Accede a las herramientas de gestión de la tienda.
                    </p>

                    <a href="<?= site_url('admin') ?>"
                       class="btn btn-outline-imperial">
                        Ir a administración
                    </a>

                </section>
            </div>
        <?php endif; ?>

        <?php if (in_array((int) session()->get('rol_id'), [1, 2], true)): ?>

            <div class="col-md-6">
                <section class="dashboard-card">

                    <div class="fs-1 mb-3">💳</div>

                    <h2>Pagos pendientes</h2>

                    <p>
                        Revisa los anticipos reportados por los clientes
                        y aprueba o rechaza sus pagos.
                    </p>

                    
                    <a href="<?= site_url('pagos/pendientes') ?>"
                    class="btn btn-imperial">
                        Revisar pagos pendientes
                    </a>

                </section>
            </div>

        <?php endif; ?>

    </div>

</main>

<footer class="text-center text-secondary small py-4">
    &copy; <?= date('Y') ?> Mueblería Imperial
</footer>

</body>
</html>

