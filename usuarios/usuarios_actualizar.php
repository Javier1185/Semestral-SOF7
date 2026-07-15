<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../modelos/Bitacora.php';

Sesion::iniciar();

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = (int) $_POST['id'];
$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$rolId = (int) $_POST['rol_id'];

$sql = "
UPDATE usuarios
SET
    nombre = ?,
    correo = ?,
    rol_id = ?,
    actualizado_por = ?,
    fecha_actualizacion = NOW()
WHERE id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $nombre,
    $correo,
    $rolId,
    Sesion::obtenerId(),
    $id
]);

Bitacora::registrar(
    Sesion::obtenerId(),
    'actualizar',
    'usuarios',
    $id,
    'Se actualizó el usuario: ' . $nombre
);

header('Location: usuarios_index.php');
exit;