<?php
/**
|--------------------------------------------------------------------------
| FirmarInforme.php
|--------------------------------------------------------------------------
| Registra la firma de un informe contable.
|
| - Todos los usuarios pueden firmar el informe como REVISADO.
| - El usuario Administrador realiza el cierre definitivo como CERRADO.
| - Genera un hash SHA-256.
| - Firma digitalmente el hash con OpenSSL.
| - Guarda el registro en la tabla cierres.
| - Registra la acción en la bitácora.
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
| RECIBIR Y LIMPIAR LOS PARÁMETROS
|--------------------------------------------------------------------------
*/

$tipo = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin = Validador::limpiarTexto($_GET['fin'] ?? '');

/*
|--------------------------------------------------------------------------
| VALIDAR EL TIPO DE INFORME Y LAS FECHAS
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
| DEFINIR EL PERÍODO QUE SE GUARDARÁ
|--------------------------------------------------------------------------
| El Estado de Resultados necesita una fecha inicial y una fecha final.
|
| El Balance General representa la situación contable en una sola fecha.
| Por eso, para el Balance General, la fecha final se guarda también como
| fecha inicial. Esto evita enviar NULL a periodo_inicio.
|--------------------------------------------------------------------------
*/

$periodoInicio = $tipo === 'balance_general'
    ? $fin
    : $inicio;

/*
|--------------------------------------------------------------------------
| VALIDAR EL ORDEN DEL RANGO DE FECHAS
|--------------------------------------------------------------------------
| Esta validación aplica únicamente al Estado de Resultados.
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
| OBTENER LOS DATOS DEL USUARIO AUTENTICADO
|--------------------------------------------------------------------------
*/

$usuario = Sesion::usuarioActual();

if ($usuario === null) {
    die('No fue posible obtener los datos del usuario.');
}

$usuarioId = (int) $usuario['id'];
$nombreUsuario = $usuario['nombre'];
$rolUsuario = $usuario['rol_nombre'];

/*
|--------------------------------------------------------------------------
| DETERMINAR EL TIPO DE FIRMA
|--------------------------------------------------------------------------
| En este proyecto, el rol Administrador realiza el cierre definitivo.
| Los demás roles registran una firma de revisión.
|--------------------------------------------------------------------------
*/

$esDefinitiva = (
    mb_strtolower(trim($rolUsuario)) === 'administrador'
);

$estado = $esDefinitiva
    ? 'CERRADO'
    : 'REVISADO';

/*
|--------------------------------------------------------------------------
| GENERAR EL HASH Y LA FIRMA DIGITAL
|--------------------------------------------------------------------------
*/

$modelo = new InformeContable();
$firmaDigital = new FirmaDigital();

$contenido = $modelo->generarContenidoHash(
    $tipo,
    $periodoInicio,
    $fin
);

$hash = $firmaDigital->generarHash($contenido);
$firma = $firmaDigital->firmarHash($hash);

/*
|--------------------------------------------------------------------------
| CONECTAR A LA BASE DE DATOS
|--------------------------------------------------------------------------
*/

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

/*
|--------------------------------------------------------------------------
| GUARDAR LA FIRMA EN LA TABLA CIERRES
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
        :inicio,
        :fin,
        :usuario,
        :hash,
        :firma,
        :estado,
        NOW()
    )
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':tipo' => $tipo,
    ':inicio' => $periodoInicio,
    ':fin' => $fin,
    ':usuario' => $usuarioId,
    ':hash' => $hash,
    ':firma' => $firma,
    ':estado' => $estado
]);

$cierreId = (int) $pdo->lastInsertId();

/*
|--------------------------------------------------------------------------
| REGISTRAR LA ACCIÓN EN LA BITÁCORA
|--------------------------------------------------------------------------
*/

$detalleAccion = $esDefinitiva
    ? 'Se realizó el cierre definitivo'
    : 'Se registró una firma de revisión';

Bitacora::registrar(
    $usuarioId,
    $esDefinitiva
        ? 'CERRAR_INFORME'
        : 'FIRMAR_INFORME',
    'cierres',
    $cierreId,
    $detalleAccion
    . ' del informe '
    . $tipo
    . ' ('
    . $periodoInicio
    . ' - '
    . $fin
    . ').'
);

/*
|--------------------------------------------------------------------------
| CARGAR EL DISEÑO GENERAL DEL SISTEMA
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
            <h1>Informe firmado correctamente</h1>

            <p>
                La firma fue registrada en el sistema.
            </p>
        </div>

        <p>
            <strong>Usuario:</strong>
            <?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            <strong>Rol:</strong>
            <?= htmlspecialchars($rolUsuario, ENT_QUOTES, 'UTF-8') ?>
        </p>

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

        <p>
            <strong>Estado:</strong>
            <?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <?php if ($esDefinitiva): ?>

            <div class="resultado-correcto">
                <strong>
                    Cierre definitivo realizado correctamente.
                </strong>

                <br>

                El usuario Administrador autorizó y cerró el informe.
            </div>

        <?php else: ?>

            <div class="alerta alerta-info">
                <strong>Firma registrada correctamente.</strong>

                <br>

                El informe quedó en estado
                <strong>REVISADO</strong>
                hasta que el Administrador realice el cierre definitivo.
            </div>

        <?php endif; ?>

        <h3>Hash generado</h3>

        <textarea
            class="hash-box"
            readonly
        ><?= htmlspecialchars($hash, ENT_QUOTES, 'UTF-8') ?></textarea>

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