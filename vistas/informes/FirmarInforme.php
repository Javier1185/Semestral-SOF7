<?php
/**
|--------------------------------------------------------------------------
| FirmarInforme.php
|--------------------------------------------------------------------------
| Registra la firma de un informe contable.
| - Todos los usuarios pueden firmar (REVISADO).
| - El Gerente Financiero realiza el cierre definitivo (CERRADO).
| - Genera hash SHA-256 y firma digital.
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

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header("Location: ../../index.php");
    exit;
}

/*=========================
= Validación de parámetros =
=========================*/

$tipo   = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin    = Validador::limpiarTexto($_GET['fin'] ?? '');

if (!Validador::validarTipoInforme($tipo)) die("Tipo de informe inválido.");
if (!Validador::validarFecha($fin)) die("Fecha final inválida.");
if ($tipo === 'estado_resultados' && !Validador::validarFecha($inicio)) die("Fecha inicial inválida.");

/*=====================
= Usuario autenticado =
=====================*/

$usuario = Sesion::usuarioActual();

$usuarioId     = $usuario['id'];
$nombreUsuario = $usuario['nombre'];
$rolUsuario    = $usuario['rol_nombre'];

$esDefinitiva = ($rolUsuario === 'Gerente Financiero');
$estado       = $esDefinitiva ? 'CERRADO' : 'REVISADO';

/*==============================
= Generación del hash y firma =
==============================*/

$modelo = new InformeContable();
$firmaDigital = new FirmaDigital();

$contenido = $modelo->generarContenidoHash($tipo,$inicio,$fin);
$hash       = $firmaDigital->generarHash($contenido);
$firma      = $firmaDigital->firmarHash($hash);

/*=====================
= Guardar en BD =
=====================*/

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$sql = "INSERT INTO cierres
(tipo,periodo_inicio,periodo_fin,usuario_id,hash_datos,firma,estado,fecha_cierre)
VALUES
(:tipo,:inicio,:fin,:usuario,:hash,:firma,:estado,NOW())";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':tipo'=>$tipo,
    ':inicio'=>$inicio ?: null,
    ':fin'=>$fin,
    ':usuario'=>$usuarioId,
    ':hash'=>$hash,
    ':firma'=>$firma,
    ':estado'=>$estado
]);

$cierreId = $pdo->lastInsertId();

/*=====================
= Bitácora =
=====================*/

Bitacora::registrar(
    $usuarioId,
    $esDefinitiva ? 'CERRAR_INFORME' : 'FIRMAR_INFORME',
    'cierres',
    $cierreId,
    ($esDefinitiva
        ? 'Se realizó el cierre definitivo'
        : 'Se registró una firma de revisión')
    ." del informe {$tipo} ({$inicio} - {$fin})."
);

require_once __DIR__.'/../layout/header.php';
require_once __DIR__.'/../layout/sidebar.php';
?>

<link rel="stylesheet" href="../../assets/css/informe/estilo.css">

<div class="contenido">
<section class="informes-panel">

<h1>Informe firmado correctamente</h1>

<p><strong>Usuario:</strong> <?= htmlspecialchars($nombreUsuario) ?></p>
<p><strong>Rol:</strong> <?= htmlspecialchars($rolUsuario) ?></p>
<p><strong>Estado:</strong> <?= htmlspecialchars($estado) ?></p>

<?php if($esDefinitiva): ?>

<div class="resultado-correcto">
<strong>✔ Cierre definitivo realizado correctamente.</strong><br>
El Gerente Financiero autorizó el informe.
</div>

<?php else: ?>

<div class="alerta alerta-info">
<strong>✔ Firma registrada.</strong><br>
El informe quedó en estado <strong>REVISADO</strong> hasta que el Gerente Financiero realice el cierre definitivo.
</div>

<?php endif; ?>

<h3>Hash generado</h3>

<textarea class="hash-box" readonly><?= htmlspecialchars($hash) ?></textarea>

<div class="acciones-informe">

<a class="boton boton-primario"
href="VerInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
Volver al informe
</a>

</div>

</section>
</div>

<?php require_once __DIR__.'/../layout/footer.php'; ?>