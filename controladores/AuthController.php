<?php

require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../config/Sesion.php';
require_once __DIR__ . '/../seguridad/Validaciones.php'; 

Sesion::iniciar();

// Si ya inició sesión, no tiene sentido que vea el login otra vez.
if (Sesion::estaLogueado()) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = Validaciones::sanitizarTexto($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (!Validaciones::noVacio($correo) || !Validaciones::noVacio($contrasena)) {
        $error = 'Completa correo y contraseña.';
    } elseif (!Validaciones::validarCorreo($correo)) {
        $error = 'El correo no tiene un formato válido.';
    } else {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();

        $sql = "SELECT u.id, u.nombre, u.correo, u.contrasena, u.rol_id, u.estado_actividad,
                       r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.rol_id
                WHERE u.correo = :correo";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            $error = 'Correo o contraseña incorrectos.';
        } elseif ((int) $usuario['estado_actividad'] === 0) {
            $error = 'Este usuario está desactivado. Contacta al administrador.';
        } elseif (!password_verify($contrasena, $usuario['contrasena'])) {
            $error = 'Correo o contraseña incorrectos.';
        } else {
            // Login correcto: guardamos los datos en sesión y redirigimos.
            Sesion::guardarUsuario($usuario);
            header('Location: ../index.php');
            exit;
        }
    }
}

require_once __DIR__ . '/../vistas/auth/login.php';