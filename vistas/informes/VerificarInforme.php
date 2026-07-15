<?php
/**
 * VerificarInforme.php
 *
 * Verifica la integridad de un informe mediante:
 * - El hash almacenado al momento del cierre.
 * - El hash calculado con los datos actuales.
 * - La firma digital guardada en la base de datos.
 */

require_once __DIR__ . '/../../config/Sesion.php';
require_once __DIR__ . '/../../config/Conexion.php';
require_once __DIR__ . '/../../modelos/informes_firma/FirmaDigital.php';
require_once __DIR__ . '/../../modelos/informes_firma/InformeContable.php';
require_once __DIR__ . '/../../modelos/informes_firma/Validador.php';
require_once __DIR__ . '/../../modelos/Bitacora.php';

/*
|--------------------------------------------------------------------------
| INICIAR Y VALIDAR LA SESIÓN
|--------------------------------------------------------------------------
*/

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ../../index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| RECIBIR Y LIMPIAR LOS DATOS
|--------------------------------------------------------------------------
*/

$tipo = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin = Validador::limpiarTexto($_GET['fin'] ?? '');

/*
|--------------------------------------------------------------------------
| VALIDAR LOS PARÁMETROS
|--------------------------------------------------------------------------
*/

if (!Validador::validarTipoInforme($tipo)) {
    die('Tipo de informe inválido.');
}

if (!Validador::validarFecha($fin)) {
    die('Fecha final inválida.');
}

if (
    $tipo === 'estado_resultados'
    && !Validador::validarFecha($inicio)
) {
    die('Fecha inicial inválida.');
}

/*
|--------------------------------------------------------------------------
| NORMALIZAR EL PERÍODO
|--------------------------------------------------------------------------
| El Estado de Resultados utiliza una fecha inicial y una fecha final.
|
| El Balance General representa una situación contable en una sola fecha.
| Cuando se firmó, se guardó la fecha final también como periodo_inicio.
| Aquí debemos utilizar exactamente el mismo criterio para encontrarlo.
|--------------------------------------------------------------------------
*/

$periodoInicio = $tipo === 'balance_general'
    ? $fin
    : $inicio;

/*
|--------------------------------------------------------------------------
| VALIDAR EL ORDEN DEL RANGO DE FECHAS
|--------------------------------------------------------------------------
*/

if (
    $tipo === 'estado_resultados'
    && $periodoInicio > $fin
) {
    die('La fecha inicial no puede ser posterior a la fecha final.');
}

/*
|--------------------------------------------------------------------------
| CONEXIÓN A LA BASE DE DATOS
|--------------------------------------------------------------------------
*/

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

/*
|--------------------------------------------------------------------------
| BUSCAR EL ÚLTIMO CIERRE DEFINITIVO
|--------------------------------------------------------------------------
| Solo se considera cierre definitivo un registro cuyo estado sea CERRADO.
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

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':tipo' => $tipo,
    ':inicio' => $periodoInicio,
    ':fin' => $fin
]);

$cierre = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| VALORES INICIALES
|--------------------------------------------------------------------------
*/

$hashActual = '';
$hashOriginal = '';
$firmaEsValida = false;
$informeConfiable = false;

/*
|--------------------------------------------------------------------------
| SI NO EXISTE CIERRE DEFINITIVO
|--------------------------------------------------------------------------
*/

if (!$cierre) {
    Bitacora::registrar(
        Sesion::obtenerId(),
        'VERIFICAR_INFORME',
        'cierres',
        null,
        'Se intentó verificar el informe '
        . $tipo
        . ' del período '
        . $periodoInicio
        . ' al '
        . $fin
        . ', pero no tiene cierre definitivo.'
    );
} else {
    /*
    |--------------------------------------------------------------------------
    | GENERAR EL HASH ACTUAL
    |--------------------------------------------------------------------------
    | Se utiliza el mismo período normalizado empleado durante la firma.
    |--------------------------------------------------------------------------
    */

    $modelo = new InformeContable();
    $firmaDigital = new FirmaDigital();

    $contenido = $modelo->generarContenidoHash(
        $tipo,
        $periodoInicio,
        $fin
    );

    $hashActual = $firmaDigital->generarHash($contenido);
    $hashOriginal = $cierre['hash_datos'];

    /*
    |--------------------------------------------------------------------------
    | VERIFICAR LA FIRMA DIGITAL
    |--------------------------------------------------------------------------
    */

    $firmaEsValida = $firmaDigital->verificarFirma(
        $hashOriginal,
        $cierre['firma']
    );

    /*
    |--------------------------------------------------------------------------
    | DETERMINAR SI EL INFORME ES CONFIABLE
    |--------------------------------------------------------------------------
    | Deben cumplirse las dos condiciones:
    |
    | 1. El hash actual debe coincidir con el hash original.
    | 2. La firma digital debe ser válida.
    |--------------------------------------------------------------------------
    */

    $informeConfiable = (
        $hashActual === $hashOriginal
        && $firmaEsValida
    );

    /*
    |--------------------------------------------------------------------------
    | REGISTRO EN BITÁCORA
    |--------------------------------------------------------------------------
    */

    Bitacora::registrar(
        Sesion::obtenerId(),
        $informeConfiable
            ? 'VERIFICAR_INFORME'
            : 'INFORME_NO_CONFIABLE',
        'cierres',
        (int) $cierre['id'],
        $informeConfiable
            ? 'El informe fue verificado correctamente como confiable.'
            : 'El informe fue modificado después del cierre o su firma no es válida.'
    );
}

/*
|--------------------------------------------------------------------------
| CARGAR EL DISEÑO GENERAL
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<link
    rel="stylesheet"
    href="<?= BASE_URL ?>/assets/css/informe/estilo.css"
>

<div class="contenido">

    <section class="informes-panel">

        <div class="titulo-modulo">
            <h1>Verificación del informe</h1>

            <p>
                Comprobación del cierre, hash y firma digital.
            </p>
        </div>

        <p>
            <strong>Tipo de informe:</strong>
            <?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            <strong>Período:</strong>
            <?= htmlspecialchars($periodoInicio, ENT_QUOTES, 'UTF-8') ?>
            al
            <?= htmlspecialchars($fin, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <?php if (!$cierre): ?>

            <div class="alerta alerta-advertencia">
                Este informe todavía no tiene un cierre definitivo.
                Debe ser firmado por el usuario autorizado para cerrar
                el informe.
            </div>

        <?php else: ?>

            <p>
                <strong>Cerrado por:</strong>
                <?= htmlspecialchars(
                    $cierre['nombre_usuario'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <p>
                <strong>Correo:</strong>
                <?= htmlspecialchars(
                    $cierre['correo_usuario'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <p>
                <strong>Fecha de cierre:</strong>
                <?= htmlspecialchars(
                    $cierre['fecha_cierre'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <p>
                <strong>Estado:</strong>
                <?= htmlspecialchars(
                    $cierre['estado'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <?php if ($informeConfiable): ?>

                <div class="resultado-correcto">
                    <h3>Informe confiable</h3>

                    <p>
                        El informe mantiene la misma información registrada
                        al momento del cierre y su firma digital es válida.
                    </p>
                </div>

            <?php else: ?>

                <div class="resultado-error">
                    <h3>Informe NO confiable</h3>

                    <p>
                        Se detectaron modificaciones posteriores al cierre
                        o la firma digital no es válida.
                    </p>
                </div>

            <?php endif; ?>

            <h3>Hash original</h3>

            <textarea
                class="hash-box"
                readonly
            ><?= htmlspecialchars(
                $hashOriginal,
                ENT_QUOTES,
                'UTF-8'
            ) ?></textarea>

            <h3>Hash actual</h3>

            <textarea
                class="hash-box"
                readonly
            ><?= htmlspecialchars(
                $hashActual,
                ENT_QUOTES,
                'UTF-8'
            ) ?></textarea>

            <p>
                <strong>Firma digital válida:</strong>
                <?= $firmaEsValida ? 'Sí' : 'No' ?>
            </p>

        <?php endif; ?>

        <div class="acciones-informe">

            <a
                class="boton boton-primario"
                href="VerInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($periodoInicio) ?>&fin=<?= urlencode($fin) ?>"
            >
                Volver al informe
            </a>

        </div>

    </section>

</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>