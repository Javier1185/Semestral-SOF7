<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../modelos/Bitacora.php';

Sesion::iniciar();

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = (int) $_POST['id'];
$nombre = trim($_POST['nombre']);

$sql = "
UPDATE roles
SET
    nombre = ?
WHERE id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $nombre,
    $id
]);

Bitacora::registrar(
    Sesion::obtenerId(),
    'actualizar',
    'roles',
    $id,
    'Se actualizó el rol: ' . $nombre
);

header('Location: roles_index.php');
exit;