<?php

session_start();

require_once __DIR__ . '/../../config/Conexion.php';
require_once __DIR__ . '/../../modelos/informes_firma/FirmaDigital.php';
require_once __DIR__ . '/../../modelos/informes_firma/InformeContable.php';
require_once __DIR__ . '/../../modelos/informes_firma/Validador.php';
require_once __DIR__ . '/../../modelos/Bitacora.php';

/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE DATOS RECIBIDOS
|--------------------------------------------------------------------------
| Se reciben el tipo de informe y las fechas por GET.
| Antes de consultar o verificar, se limpian y validan.
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
| CONEXIÓN A LA BASE DE DATOS
|--------------------------------------------------------------------------
| Se usa la conexión real del proyecto.
|--------------------------------------------------------------------------
*/

$conexion = Conexion::obtenerInstancia()->obtenerPDO();

/*
|--------------------------------------------------------------------------
| BUSCAR EL ÚLTIMO CIERRE DEFINITIVO
|--------------------------------------------------------------------------
| En este proyecto la firma definitiva se guarda en la tabla cierres.
| El Gerente Financiero deja el estado como CERRADO.
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        c.*,
        u.nombre AS nombre_usuario,
        u.correo AS correo_usuario
    FROM cierres c
    INNER JOIN usuarios u
        ON c.usuario_id = u.id
    WHERE c.tipo = :tipo
    AND c.periodo_inicio = :inicio
    AND c.periodo_fin = :fin
    AND c.estado = 'CERRADO'
    ORDER BY c.fecha_cierre DESC
    LIMIT 1
";

/*
|--------------------------------------------------------------------------
| PREPARAR Y EJECUTAR LA CONSULTA
|--------------------------------------------------------------------------
| Primero se prepara la consulta SQL y luego se envían los parámetros.
|--------------------------------------------------------------------------
*/

$stmt = $conexion->prepare($sql);

$stmt->execute([
    ':tipo'   => $tipo,
    ':inicio' => $inicio ?: null,
    ':fin'    => $fin
]);

$cierreGuardado = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cierreGuardado) {

    if (isset($_SESSION['usuario_id'])) {
        Bitacora::registrar(
            $_SESSION['usuario_id'],
            'VERIFICAR_INFORME',
            'cierres',
            null,
            'Se intentó verificar el informe ' . $tipo .
            ' del período ' . $inicio . ' al ' . $fin .
            ', pero todavía no tiene cierre definitivo.'
        );
    }

} else {

    /*
    |--------------------------------------------------------------------------
    | GENERAR HASH ACTUAL
    |--------------------------------------------------------------------------
    | Se vuelve a calcular el hash con los datos actuales del informe.
    | Si alguien modificó el diario después del cierre, este hash será distinto.
    |--------------------------------------------------------------------------
    */

    $informe = new InformeContable();
    $firmaDigital = new FirmaDigital();

    $contenidoActual = $informe->generarContenidoHash($tipo, $inicio, $fin);
    $hashActual = $firmaDigital->generarHash($contenidoActual);

    /*
    |--------------------------------------------------------------------------
    | HASH ORIGINAL
    |--------------------------------------------------------------------------
    | Este es el hash guardado cuando el Gerente Financiero cerró el informe.
    |--------------------------------------------------------------------------
    */

    $hashOriginal = $cierreGuardado['hash_datos'];

    /*
    |--------------------------------------------------------------------------
    | VERIFICAR FIRMA DIGITAL
    |--------------------------------------------------------------------------
    | Además de comparar hashes, se verifica que la firma digital corresponda
    | al hash original guardado.
    |--------------------------------------------------------------------------
    */

    $firmaEsValida = $firmaDigital->verificarFirma(
        $hashOriginal,
        $cierreGuardado['firma']
    );

    /*
    |--------------------------------------------------------------------------
    | RESULTADO FINAL
    |--------------------------------------------------------------------------
    | El informe es confiable solamente si:
    | 1. El hash actual es igual al hash original.
    | 2. La firma digital es válida.
    |--------------------------------------------------------------------------
    */

    $informeConfiable = ($hashActual === $hashOriginal && $firmaEsValida);

    /*
    |--------------------------------------------------------------------------
    | REGISTRO EN BITÁCORA
    |--------------------------------------------------------------------------
    */

    if (isset($_SESSION['usuario_id'])) {

        if ($informeConfiable) {
            Bitacora::registrar(
                $_SESSION['usuario_id'],
                'VERIFICAR_INFORME',
                'cierres',
                $cierreGuardado['id'],
                'El informe ' . $tipo .
                ' del período ' . $inicio . ' al ' . $fin .
                ' fue verificado como CONFIABLE.'
            );
        } else {
            Bitacora::registrar(
                $_SESSION['usuario_id'],
                'INFORME_NO_CONFIABLE',
                'cierres',
                $cierreGuardado['id'],
                'El informe ' . $tipo .
                ' del período ' . $inicio . ' al ' . $fin .
                ' fue verificado como NO CONFIABLE.'
            );
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Informe</title>
</head>
<body>

<h2>Verificación del Informe</h2>

<?php if (!$cierreGuardado): ?>

    <p style="color:orange;">
        Este informe todavía no tiene una firma definitiva del Gerente Financiero.
    </p>

<?php else: ?>

    <p><strong>Cerrado por:</strong> 
        <?= htmlspecialchars($cierreGuardado['nombre_usuario'] ?? $cierreGuardado['correo_usuario']) ?>
    </p>

    <p><strong>Fecha de cierre:</strong> 
        <?= htmlspecialchars($cierreGuardado['fecha_cierre']) ?>
    </p>

    <p><strong>Estado guardado:</strong> 
        <?= htmlspecialchars($cierreGuardado['estado']) ?>
    </p>

    <?php if ($informeConfiable): ?>

        <h3 style="color:green;">Informe confiable</h3>
        <p>
            El informe no ha sido modificado después de su cierre definitivo
            y la firma digital es válida.
        </p>

    <?php else: ?>

        <h3 style="color:red;">Informe NO confiable</h3>
        <p>
            El informe fue modificado después del cierre definitivo
            o la firma digital no es válida.
        </p>

    <?php endif; ?>

    <hr>

    <p><strong>Hash original:</strong></p>
    <textarea rows="3" cols="90" readonly><?= htmlspecialchars($hashOriginal) ?></textarea>

    <p><strong>Hash actual:</strong></p>
    <textarea rows="3" cols="90" readonly><?= htmlspecialchars($hashActual) ?></textarea>

    <p><strong>Firma digital válida:</strong> 
        <?= $firmaEsValida ? 'Sí' : 'No' ?>
    </p>

<?php endif; ?>

<br><br>

<a href="VerInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
    Volver al informe
</a>

</body>
</html>