<?php

/*
|--------------------------------------------------------------------------
| FIRMAR INFORME
|--------------------------------------------------------------------------
| Este archivo permite registrar la firma de un informe contable.
|
| - Todos los usuarios pueden firmar el informe como una revisión.
| - Si el usuario tiene el rol "Gerente Financiero", la firma será
|   considerada como el cierre definitivo del informe.
|
| Además:
| - Se genera un hash SHA-256 con la información del informe.
| - Se firma digitalmente dicho hash utilizando OpenSSL.
| - Se guarda el cierre en la tabla "cierres".
| - Se registra la acción en la bitácora del sistema.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../config/Sesion.php';
require_once __DIR__ . '/../../config/Conexion.php';

require_once __DIR__ . '/../../modelos/informes_firma/FirmaDigital.php';
require_once __DIR__ . '/../../modelos/informes_firma/InformeContable.php';
require_once __DIR__ . '/../../modelos/informes_firma/Validador.php';
require_once __DIR__ . '/../../modelos/Bitacora.php';

/*
|--------------------------------------------------------------------------
| INICIAR SESIÓN
|--------------------------------------------------------------------------
| Se utiliza la clase Sesion del proyecto para validar que exista un
| usuario autenticado.
|--------------------------------------------------------------------------
*/

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    die("Debe iniciar sesión para firmar el informe.");
}

/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE DATOS
|--------------------------------------------------------------------------
| Se reciben los parámetros enviados desde VerInforme.php.
| Luego se limpian y validan para evitar datos inválidos.
|--------------------------------------------------------------------------
*/

$tipo   = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin    = Validador::limpiarTexto($_GET['fin'] ?? '');

if (!Validador::validarTipoInforme($tipo)) {
    die("Tipo de informe inválido.");
}

if (!Validador::validarFecha($fin)) {
    die("Fecha final inválida.");
}

if ($tipo === 'estado_resultados' && !Validador::validarFecha($inicio)) {
    die("Fecha de inicio inválida.");
}

/*
|--------------------------------------------------------------------------
| DATOS DEL USUARIO LOGUEADO
|--------------------------------------------------------------------------
| La información proviene de la clase Sesion.
|--------------------------------------------------------------------------
*/

$usuarioId     = (int) $_SESSION['usuario_id'];
$nombreUsuario = $_SESSION['nombre'];
$rolUsuario    = $_SESSION['rol_nombre'];

/*
|--------------------------------------------------------------------------
| DETERMINAR SI LA FIRMA ES DEFINITIVA
|--------------------------------------------------------------------------
| Todos los usuarios pueden firmar el informe.
| Solamente el Gerente Financiero realiza el cierre definitivo.
|--------------------------------------------------------------------------
*/

$esDefinitiva = ($rolUsuario === 'Gerente Financiero');

$estado = $esDefinitiva
    ? 'CERRADO'
    : 'REVISADO';

/*
|--------------------------------------------------------------------------
| GENERACIÓN DEL HASH Y FIRMA DIGITAL
|--------------------------------------------------------------------------
| Se obtiene toda la información del informe.
| Posteriormente se genera un hash SHA-256.
| Finalmente dicho hash es firmado mediante OpenSSL.
|--------------------------------------------------------------------------
*/

$informe = new InformeContable();
$firmaDigital = new FirmaDigital();

$contenido = $informe->generarContenidoHash($tipo, $inicio, $fin);

$hash = $firmaDigital->generarHash($contenido);

$firma = $firmaDigital->firmarHash($hash);

/*
|--------------------------------------------------------------------------
| CONEXIÓN A LA BASE DE DATOS
|--------------------------------------------------------------------------
| Se utiliza el Singleton definido por el proyecto.
|--------------------------------------------------------------------------
*/

$conexion = Conexion::obtenerInstancia()->obtenerPDO();

/*
|--------------------------------------------------------------------------
| REGISTRAR EL CIERRE DEL INFORME
|--------------------------------------------------------------------------
| La tabla "cierres" almacena:
|
| - Tipo de informe.
| - Período.
| - Usuario que realizó la firma.
| - Hash del informe.
| - Firma digital.
| - Estado (REVISADO o CERRADO).
|--------------------------------------------------------------------------
*/

$sql = "
INSERT INTO cierres
(
    tipo,
    periodo_inicio,
    periodo_fin,
    usuario_id,
    hash_datos,
    firma,
    estado,
    fecha_cierre
)
VALUES
(
    :tipo,
    :periodo_inicio,
    :periodo_fin,
    :usuario_id,
    :hash_datos,
    :firma,
    :estado,
    NOW()
)
";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    ':tipo'            => $tipo,
    ':periodo_inicio'  => $inicio ?: null,
    ':periodo_fin'     => $fin,
    ':usuario_id'      => $usuarioId,
    ':hash_datos'      => $hash,
    ':firma'           => $firma,
    ':estado'          => $estado
]);

/*
|--------------------------------------------------------------------------
| OBTENER EL ID DEL CIERRE
|--------------------------------------------------------------------------
| Se utiliza para relacionarlo con la bitácora.
|--------------------------------------------------------------------------
*/

$cierreId = $conexion->lastInsertId();

/*
|--------------------------------------------------------------------------
| REGISTRO EN BITÁCORA
|--------------------------------------------------------------------------
| Se registra quién realizó la acción.
|--------------------------------------------------------------------------
*/

if ($esDefinitiva) {

    Bitacora::registrar(
        $usuarioId,
        'CERRAR_INFORME',
        'cierres',
        $cierreId,
        'El Gerente Financiero cerró definitivamente el informe "' .
        $tipo .
        '" correspondiente al período ' .
        $inicio .
        ' al ' .
        $fin
    );

} else {

    Bitacora::registrar(
        $usuarioId,
        'FIRMAR_INFORME',
        'cierres',
        $cierreId,
        'El usuario firmó como revisión el informe "' .
        $tipo .
        '" correspondiente al período ' .
        $inicio .
        ' al ' .
        $fin
    );

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe Firmado</title>
</head>

<body>

<h2>Informe firmado correctamente</h2>

<p>
    <strong>Usuario:</strong>
    <?= htmlspecialchars($nombreUsuario) ?>
</p>

<p>
    <strong>Rol:</strong>
    <?= htmlspecialchars($rolUsuario) ?>
</p>

<p>
    <strong>Estado del informe:</strong>
    <?= htmlspecialchars($estado) ?>
</p>

<?php if ($esDefinitiva): ?>

    <p style="color:green;">
        ✔ El informe fue cerrado definitivamente por el Gerente Financiero.
    </p>

<?php else: ?>

    <p style="color:blue;">
        ✔ El informe fue firmado como revisión.
    </p>

<?php endif; ?>

<hr>

<h3>Hash generado</h3>

<textarea
    rows="4"
    cols="90"
    readonly><?= htmlspecialchars($hash) ?></textarea>

<br><br>

<a href="VerInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
    Volver al informe
</a>

</body>

</html>