<?php
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