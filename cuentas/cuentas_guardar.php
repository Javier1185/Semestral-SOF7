<?php

require_once '../config/Conexion.php';
require_once '../modelos/Bitacora.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$codigo = $_POST['codigo'];
$nombre = $_POST['nombre'];
$clase = $_POST['clase'];

/*
Usuario temporal.
Cuando exista login reemplazar por:
$usuarioId = $_SESSION['usuario_id'];
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

// Obtener el ID de la cuenta recién creada
$idCuenta = $pdo->lastInsertId();

// Registrar en bitácora
Bitacora::registrar(
    $usuarioId,
    'crear',
    'cuentas',
    $idCuenta,
    "Cuenta creada: {$codigo} - {$nombre}"
);

header('Location: cuentas_index.php');
exit;
?>