<?php
/**
 * VerificarInforme.php
 * Verifica la integridad de un informe mediante el hash y la firma digital.
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

$tipo   = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin    = Validador::limpiarTexto($_GET['fin'] ?? '');

if (!Validador::validarTipoInforme($tipo)) die("Tipo de informe inválido.");
if (!Validador::validarFecha($fin)) die("Fecha final inválida.");
if ($tipo === 'estado_resultados' && !Validador::validarFecha($inicio)) die("Fecha inicial inválida.");

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$sql = "SELECT c.*,u.nombre AS nombre_usuario,u.correo AS correo_usuario
        FROM cierres c
        INNER JOIN usuarios u ON c.usuario_id=u.id
        WHERE c.tipo=:tipo
          AND c.periodo_inicio=:inicio
          AND c.periodo_fin=:fin
          AND c.estado='CERRADO'
        ORDER BY c.fecha_cierre DESC
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':tipo'=>$tipo,
    ':inicio'=>$inicio ?: null,
    ':fin'=>$fin
]);

$cierre = $stmt->fetch(PDO::FETCH_ASSOC);

$hashActual='';
$hashOriginal='';
$firmaEsValida=false;
$informeConfiable=false;

if(!$cierre){

    Bitacora::registrar(
        Sesion::obtenerId(),
        'VERIFICAR_INFORME',
        'cierres',
        null,
        'Intentó verificar un informe sin cierre definitivo.'
    );

}else{

    $modelo = new InformeContable();
    $firma = new FirmaDigital();

    $contenido = $modelo->generarContenidoHash($tipo,$inicio,$fin);

    $hashActual   = $firma->generarHash($contenido);
    $hashOriginal = $cierre['hash_datos'];

    $firmaEsValida = $firma->verificarFirma(
        $hashOriginal,
        $cierre['firma']
    );

    $informeConfiable = (
        $hashActual === $hashOriginal &&
        $firmaEsValida
    );

    Bitacora::registrar(
        Sesion::obtenerId(),
        $informeConfiable ? 'VERIFICAR_INFORME' : 'INFORME_NO_CONFIABLE',
        'cierres',
        $cierre['id'],
        $informeConfiable ?
        'Informe verificado correctamente.' :
        'El informe fue modificado después del cierre.'
    );
}

require_once __DIR__.'/../layout/header.php';
require_once __DIR__.'/../layout/sidebar.php';
?>

<link rel="stylesheet" href="../../assets/css/informe/estilo.css">

<div class="contenido">
<section class="informes-panel">

<h1>Verificación del Informe</h1>

<?php if(!$cierre): ?>

<div class="alerta alerta-advertencia">
Este informe todavía no tiene un cierre definitivo realizado por el Gerente Financiero.
</div>

<?php else: ?>

<p><strong>Cerrado por:</strong> <?= htmlspecialchars($cierre['nombre_usuario']) ?></p>
<p><strong>Correo:</strong> <?= htmlspecialchars($cierre['correo_usuario']) ?></p>
<p><strong>Fecha:</strong> <?= htmlspecialchars($cierre['fecha_cierre']) ?></p>
<p><strong>Estado:</strong> <?= htmlspecialchars($cierre['estado']) ?></p>

<?php if($informeConfiable): ?>

<div class="resultado-correcto">
<h3>Informe confiable</h3>
<p>El informe mantiene la misma información registrada durante el cierre.</p>
</div>

<?php else: ?>

<div class="resultado-error">
<h3>Informe NO confiable</h3>
<p>Se detectaron modificaciones posteriores al cierre o la firma no es válida.</p>
</div>

<?php endif; ?>

<h3>Hash original</h3>
<textarea class="hash-box" readonly><?= htmlspecialchars($hashOriginal) ?></textarea>

<h3>Hash actual</h3>
<textarea class="hash-box" readonly><?= htmlspecialchars($hashActual) ?></textarea>

<p><strong>Firma digital válida:</strong> <?= $firmaEsValida ? 'Sí' : 'No'; ?></p>

<?php endif; ?>

<div class="acciones-informe">
<a class="boton boton-primario"
href="VerInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
Volver al informe
</a>
</div>

</section>
</div>

<?php require_once __DIR__.'/../layout/footer.php'; ?>
