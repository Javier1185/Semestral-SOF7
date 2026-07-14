<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$codigo = $_POST['codigo'];
$nombre = $_POST['nombre'];
$clase = $_POST['clase'];

/*
Usuario temporal.
Cuando exista login reemplazar por:
$_SESSION['usuario_id']
*/
$usuarioId = 2;

$sql = "
INSERT INTO cuentas
(
    codigo,
    nombre,
    clase,
    usuario_id
)
VALUES
(
    ?,
    ?,
    ?,
    ?
)
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $codigo,
    $nombre,
    $clase,
    $usuarioId
]);

header('Location: cuentas_index.php');
exit;
?>
