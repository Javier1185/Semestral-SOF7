<?php

require_once '../config/Conexion.php';
require_once '../modelos/Bitacora.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_POST['id'];
$fecha = $_POST['fecha'];
$descripcion = $_POST['descripcion'];

$cuentas = $_POST['cuenta_id'];
$debitos = $_POST['debito'];
$creditos = $_POST['credito'];

$totalDebito = array_sum($debitos);
$totalCredito = array_sum($creditos);

if ($totalDebito != $totalCredito) {

    die("
    <h2>
    Error: El asiento no está cuadrado.
    </h2>
    ");

}

/*
Usuario temporal.
Cuando exista login reemplazar por:
$usuarioId = $_SESSION['usuario_id'];
*/
$usuarioId = 2;

$pdo->beginTransaction();

try {

    /* Actualizar cabecera */

    $stmt = $pdo->prepare("
    UPDATE diario
    SET
        fecha = ?,
        descripcion = ?
    WHERE id = ?
    ");

    $stmt->execute([
        $fecha,
        $descripcion,
        $id
    ]);

    /* Eliminar detalle anterior */

    $stmt = $pdo->prepare("
    DELETE FROM diario_detalle
    WHERE diario_id = ?
    ");

    $stmt->execute([$id]);

    /* Insertar nuevo detalle */

    foreach ($cuentas as $i => $cuentaId) {

        $stmt = $pdo->prepare("
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

        $stmt->execute([
            $id,
            $cuentaId,
            $debitos[$i],
            $creditos[$i]
        ]);
    }

    // Registrar en bitácora
    Bitacora::registrar(
        $usuarioId,
        'actualizar',
        'diario',
        $id,
        "Asiento contable actualizado: {$descripcion}"
    );

    $pdo->commit();

    header("Location: diario_ver.php?id=" . $id);
    exit;

} catch (Exception $e) {

    $pdo->rollBack();

    die($e->getMessage());
}
?>