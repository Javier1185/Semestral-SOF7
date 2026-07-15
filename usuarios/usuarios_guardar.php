<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../modelos/Bitacora.php';

Sesion::iniciar();

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
$rolId = (int) $_POST['rol_id'];

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
    $contrasena,
    $rolId
]);

$idUsuarioNuevo = $pdo->lastInsertId();

/*
Registrar en la bitácora
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