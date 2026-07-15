<?php

require_once '../config/Conexion.php';
require_once '../modelos/Bitacora.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_POST['id'];
$codigo = $_POST['codigo'];
$nombre = $_POST['nombre'];
$clase = $_POST['clase'];
$activo = $_POST['activo'];

/*
Usuario temporal.
Cuando exista login reemplazar por:
$usuarioId = $_SESSION['usuario_id'];
*/
$usuarioId = 2;

$sql = "
UPDATE cuentas
SET
    codigo = ?,
    nombre = ?,
    clase = ?,
    activo = ?
WHERE id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $codigo,
    $nombre,
    $clase,
    $activo,
    $id
]);

// Registrar en bitácora
Bitacora::registrar(
    $usuarioId,
    'actualizar',
    'cuentas',
    $id,
    "Cuenta actualizada: {$codigo} - {$nombre}"
);

header('Location: cuentas_index.php');
exit;
?>