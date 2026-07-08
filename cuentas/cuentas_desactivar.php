<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    UPDATE cuentas
    SET activo = 0
    WHERE id = ?
");

$stmt->execute([$id]);

header('Location: cuentas_index.php');
exit;
?>
