<?php

require_once __DIR__ . '/../config/Sesion.php';
require_once __DIR__ . '/../modelos/Bitacora.php';

Sesion::iniciar();

if (Sesion::estaLogueado()) {
    $usuario = Sesion::usuarioActual();

    // Registrar el logout ANTES de destruir la sesión:
    // una vez que cerramos sesión ya no tenemos el usuario_id disponible.
    Bitacora::registrar($usuario['id'], 'logout', 'usuarios', $usuario['id'], 'Cierre de sesión');
}

Sesion::cerrarSesion();
header('Location: ../vistas/publico/landing.php');
exit;