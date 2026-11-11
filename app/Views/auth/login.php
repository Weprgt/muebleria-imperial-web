<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - Mueblería Imperial</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: #f2f0eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .brand {
            color: #403226;
            font-weight: 700;
        }

        .subtitle {
            color: #786d62;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.07);
        }

        .btn-imperial {
            background: #795b3b;
            border-color: #795b3b;
            color: white;
        }

        .btn-imperial:hover {
            background: #60472e;
            border-color: #60472e;
            color: white;
        }

        .btn-outline-imperial {
            border: 1px solid #795b3b;
            color: #795b3b;
        }

        .btn-outline-imperial:hover {
            background: #795b3b;
            color: white;
        }
    </style>
</head>

<body>
    <main class="login-container">

        <header class="text-center mb-4">
            <h1 class="brand">Mueblería Imperial</h1>
            <p class="subtitle mb-0">
                CALIDAD Y ELEGANCIA PARA TU HOGAR
            </p>
        </header>

        <section class="card login-card">
            <div class="card-body p-4 p-md-5">

                <h2 class="h4 mb-2">Bienvenido de nuevo</h2>
                <p class="text-secondary mb-4">
                    Ingresa tus credenciales para continuar.
                </p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <div><?= esc($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('login') ?>" method="post">

                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="correo" class="form-label">
                            Correo electrónico
                        </label>
                        <input
                            type="email"
                            class="form-control"
                            id="correo"
                            name="correo"
                            value="<?= esc(old('correo')) ?>"
                            autocomplete="username"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            Contraseña
                        </label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <button type="submit"
                            class="btn btn-imperial w-100 py-2">
                        Iniciar sesión
                    </button>
                </form>

                <a href="<?= site_url('catalogo') ?>"
                   class="btn btn-outline-imperial w-100 py-2 mt-3">
                    Ver catálogo
                </a>

            </div>
        </section>

        <footer class="text-center text-secondary small mt-4">
            &copy; <?= date('Y') ?> Mueblería Imperial
        </footer>

    </main>
</body>
</html>

