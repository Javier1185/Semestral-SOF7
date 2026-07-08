<?php
<<<<<<< HEAD
?>

<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Contable</title>

```
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
```

</head>

<body>

<div class="container mt-5">

```
<div class="text-center mb-5">
    <h1>Sistema Contable</h1>
    <p class="text-muted">
        Módulo de Catálogo de Cuentas y Diario General
    </p>
</div>

<div class="row">

    <div class="col-md-6 mb-4">

        <div class="card shadow">

            <div class="card-body text-center">

                <h3>Catálogo de Cuentas</h3>

                <p>
                    Administración de cuentas contables.
                </p>

                <a
                    href="cuentas/cuentas_index.php"
                    class="btn btn-primary">
                    Ingresar
                </a>

            </div>

        </div>

    </div>

    <div class="col-md-6 mb-4">

        <div class="card shadow">

            <div class="card-body text-center">

                <h3>Diario General</h3>

                <p>
                    Registro de transacciones contables.
                </p>

                <a
                    href="diario/diario_index.php"
                    class="btn btn-success">
                    Ingresar
                </a>

            </div>

        </div>

    </div>

</div>
```

</div>

</body>
</html>
=======
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Sesion.php';

Sesion::iniciar();

// Si no ha iniciado sesión, lo mandamos a la página pública.
if (!Sesion::estaLogueado()) {
    header('Location: ' . BASE_URL . '/vistas/publico/landing.php');
    exit;
}

$usuario = Sesion::usuarioActual();

require_once __DIR__ . '/vistas/layout/header.php';
require_once __DIR__ . '/vistas/layout/sidebar.php';
?>

    <h1>Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?></h1>
    <p>Tu rol actual es: <strong><?= htmlspecialchars($usuario['rol_nombre']) ?></strong>.</p>
    <p>Usa el menú de la izquierda para ir al módulo que necesites.</p>

<?php
require_once __DIR__ . '/vistas/layout/footer.php';
>>>>>>> origin/master
