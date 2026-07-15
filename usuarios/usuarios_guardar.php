<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../modelos/Bitacora.php';
require_once '../seguridad/Validaciones.php';

Sesion::iniciar();

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

/*
|--------------------------------------------------------------------------
| Sanitizar datos
|--------------------------------------------------------------------------
*/
$nombre = Validaciones::sanitizarTexto($_POST['nombre'] ?? '');
$correo = Validaciones::sanitizarTexto($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$rolId = (int) ($_POST['rol_id'] ?? 0);

/*
|--------------------------------------------------------------------------
| Validaciones
|--------------------------------------------------------------------------
*/

if (!Validaciones::noVacio($nombre)) {
    die('Debe ingresar el nombre del usuario.');
}

if (!Validaciones::validarNombre($nombre)) {
    die('El nombre solo puede contener letras y espacios.');
}

if (!Validaciones::validarCorreo($correo)) {
    die('El correo electrónico no es válido.');
}

if (strlen($contrasena) < 8) {
    die('La contraseña debe tener al menos 8 caracteres.');
}

if ($rolId <= 0) {
    die('Debe seleccionar un rol.');
}

/*
|--------------------------------------------------------------------------
| Verificar correo duplicado
|--------------------------------------------------------------------------
*/

$sql = "SELECT COUNT(*) FROM usuarios WHERE correo = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$correo]);

if ($stmt->fetchColumn() > 0) {
    die('Ya existe un usuario registrado con ese correo.');
}

/*
|--------------------------------------------------------------------------
| Guardar usuario
|--------------------------------------------------------------------------
*/

$contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

$sql = "
INSERT INTO usuarios
(
    nombre,
    correo,
    contrasena,
    rol_id,
    estado_actividad
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    1
)
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $nombre,
    $correo,
    $contrasenaHash,
    $rolId
]);

$idUsuarioNuevo = $pdo->lastInsertId();

/*
|--------------------------------------------------------------------------
| Registrar en Bitácora
|--------------------------------------------------------------------------
*/

Bitacora::registrar(
    Sesion::obtenerId(),
    'crear',
    'usuarios',
    $idUsuarioNuevo,
    'Se creó el usuario: ' . $nombre
);

header('Location: usuarios_index.php');
exit;