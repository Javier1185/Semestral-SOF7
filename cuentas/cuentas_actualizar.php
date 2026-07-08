<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_POST['id'];
$codigo = $_POST['codigo'];
$nombre = $_POST['nombre'];
$clase = $_POST['clase'];
$activo = $_POST['activo'];

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

header('Location: cuentas_index.php');
exit;
?>
