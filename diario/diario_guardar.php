<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../seguridad/Validaciones.php';
require_once '../modelos/Bitacora.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
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
    title: 'Sesión expirada',
    text: 'Debe iniciar sesión nuevamente.'
}).then(() => {
    window.location.href = '../auth/AuthController.php';
});
</script>

</body>
</html>
<?php
exit;
}

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

/*
|--------------------------------------------------------------------------
| CAPTURA DE DATOS
|--------------------------------------------------------------------------
*/

$fecha = Validaciones::sanitizarTexto($_POST['fecha'] ?? '');
$descripcion = Validaciones::sanitizarTexto($_POST['descripcion'] ?? '');

$cuentas = $_POST['cuenta_id'] ?? [];
$debitos = $_POST['debito'] ?? [];
$creditos = $_POST['credito'] ?? [];

/*
|--------------------------------------------------------------------------
| VALIDACIONES
|--------------------------------------------------------------------------
*/

if (!Validaciones::validarFecha($fecha)) {
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon:'warning',
    title:'Fecha inválida',
    text:'Seleccione una fecha válida.'
}).then(()=>{
    history.back();
});
</script>

</body>
</html>
<?php
exit;
}

if (!Validaciones::noVacio($descripcion)) {
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon:'warning',
    title:'Descripción requerida',
    text:'Debe escribir una descripción.'
}).then(()=>{
    history.back();
});
</script>

</body>
</html>
<?php
exit;
}

if (strlen($descripcion) < 5) {
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon:'warning',
    title:'Descripción inválida',
    text:'La descripción debe contener al menos 5 caracteres.'
}).then(()=>{
    history.back();
});
</script>

</body>
</html>
<?php
exit;
}

if (empty($cuentas)) {
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon:'warning',
    title:'Sin cuentas',
    text:'Debe agregar al menos una cuenta.'
}).then(()=>{
    history.back();
});
</script>

</body>
</html>
<?php
exit;
}

$totalDebito = 0;
$totalCredito = 0;

foreach ($cuentas as $i => $cuenta) {

    if (empty($cuenta)) {
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon:'warning',
    title:'Cuenta requerida',
    text:'Seleccione una cuenta.'
}).then(()=>{
    history.back();
});
</script>
<?php
exit;
    }

    if (!Validaciones::esNumerico($debitos[$i]) || !Validaciones::esNumerico($creditos[$i])) {
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon:'error',
    title:'Monto inválido',
    text:'Débito y Crédito deben ser numéricos.'
}).then(()=>{
    history.back();
});
</script>
<?php
exit;
    }

    $debito = (float)$debitos[$i];
    $credito = (float)$creditos[$i];

    if ($debito < 0 || $credito < 0) {
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon:'error',
    title:'Monto inválido',
    text:'No se permiten valores negativos.'
}).then(()=>{
    history.back();
});
</script>
<?php
exit;
    }

    $totalDebito += $debito;
    $totalCredito += $credito;
}

if (round($totalDebito,2) != round($totalCredito,2)) {
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    icon:'error',
    title:'Error',
    text:'El total Débito debe ser igual al total Crédito.'
}).then(()=>{
    history.back();
});
</script>

</body>
</html>
<?php
exit;
}

/*
|--------------------------------------------------------------------------
| USUARIO AUTENTICADO
|--------------------------------------------------------------------------
*/

$usuarioId = Sesion::obtenerId();

/*
|--------------------------------------------------------------------------
| GUARDAR
|--------------------------------------------------------------------------
*/

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
        htmlspecialchars($descripcion),
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
            (float)$debitos[$i],
            (float)$creditos[$i]
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
    icon:'success',
    title:'Correcto',
    text:'Asiento registrado correctamente.'
}).then(()=>{
    window.location.href='diario_index.php';
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
    icon:'error',
    title:'Error del sistema',
    text: <?= json_encode($e->getMessage()) ?>
}).then(()=>{
    history.back();
});
</script>

</body>
</html>

<?php
}