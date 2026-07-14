<?php

require_once '../config/Conexion.php';
require_once '../modelos/Bitacora.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$fecha = $_POST['fecha'];
$descripcion = $_POST['descripcion'];

$cuentas = $_POST['cuenta_id'];
$debitos = $_POST['debito'];
$creditos = $_POST['credito'];

$totalDebito = array_sum($debitos);
$totalCredito = array_sum($creditos);

if ($totalDebito != $totalCredito) {
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'El total Débito debe ser igual al total Crédito'
}).then(() => {
    window.history.back();
});
</script>

</body>
</html>
<?php
exit;
}

/*
Usuario temporal.
Cuando exista login reemplazar por:
$usuarioId = $_SESSION['usuario_id'];
*/
$usuarioId = 2;

$pdo->beginTransaction();

try {

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

    foreach ($cuentas as $i => $cuenta) {

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

    Bitacora::registrar(
        $usuarioId,
        'crear',
        'diario',
        $diarioId,
        "Asiento contable creado: {$descripcion}"
    );

    $pdo->commit();
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'success',
    title: 'Guardado',
    text: 'Asiento registrado correctamente'
}).then(() => {
    window.location.href = 'diario_index.php';
});
</script>

</body>
</html>
<?php

} catch (Exception $e) {

    $pdo->rollBack();
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon: 'error',
    title: 'Error del sistema',
    text: <?= json_encode($e->getMessage()) ?>
}).then(() => {
    window.history.back();
});
</script>

</body>
</html>
<?php
}
?>