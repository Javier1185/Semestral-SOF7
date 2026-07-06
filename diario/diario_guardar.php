<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$fecha = $_POST['fecha'];
$descripcion = $_POST['descripcion'];

$cuentas = $_POST['cuenta_id'];
$debitos = $_POST['debito'];
$creditos = $_POST['credito'];

$totalDebito = array_sum($debitos);
$totalCredito = array_sum($creditos);

if($totalDebito != $totalCredito){

die("
<h2>
Error:
El total Débito debe ser igual al total Crédito.
</h2>
");

}

/*
Temporal hasta que exista login.
*/
$usuarioId = 2;

$pdo->beginTransaction();

try{

$stmt = $pdo->prepare("
INSERT INTO diario
(
fecha,
descripcion,
usuario_id
)
VALUES
(
?,
?,
?
)
");

$stmt->execute([
$fecha,
$descripcion,
$usuarioId
]);

$diarioId = $pdo->lastInsertId();

foreach($cuentas as $i => $cuenta){

$stmtDetalle = $pdo->prepare("
INSERT INTO diario_detalle
(
diario_id,
cuenta_id,
debito,
credito
)
VALUES
(
?,
?,
?,
?
)
");

$stmtDetalle->execute([
$diarioId,
$cuenta,
$debitos[$i],
$creditos[$i]
]);

}

$pdo->commit();

header("Location: diario_index.php");
exit;

}catch(Exception $e){

$pdo->rollBack();

die($e->getMessage());

}
?>
