<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Mueblería Imperial</title>
</head>
<body>

    <h1>Panel de administración</h1>

    <p>
        Bienvenido,
        <strong><?= esc(session()->get('nombre')) ?></strong>
    </p>

    <p>Has iniciado sesión correctamente.</p>
    <form action="<?= site_url('logout') ?>" method="post">
        <?= csrf_field() ?>

        <button type="submit">Cerrar sesión</button>
    </form>

</body>
</html>
