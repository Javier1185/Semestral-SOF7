<?php
/*
|--------------------------------------------------------------------------
| VerInforme.php
|--------------------------------------------------------------------------
| Vista principal del módulo de informes contables.
| Integra:
| - Sesión del proyecto
| - Bitácora
| - Header, Sidebar y Footer
| - Estado de Resultados
| - Balance General
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../config/Sesion.php';
require_once __DIR__ . '/../../modelos/informes_firma/InformeContable.php';
require_once __DIR__ . '/../../modelos/informes_firma/Validador.php';
require_once __DIR__ . '/../../modelos/Bitacora.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ../../index.php');
    exit;
}

$tipo   = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin    = Validador::limpiarTexto($_GET['fin'] ?? '');

if (!Validador::validarTipoInforme($tipo)) {
    die('Tipo de informe inválido.');
}

if (!Validador::validarFecha($fin)) {
    die('Fecha final inválida.');
}

if ($tipo === 'estado_resultados' && !Validador::validarFecha($inicio)) {
    die('Fecha inicial inválida.');
}

$informe = new InformeContable();

Bitacora::registrar(
    Sesion::obtenerId(),
    'VER_INFORME',
    null,
    null,
    "Visualizó el informe {$tipo} del período {$inicio} al {$fin}"
);

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/informe/estilo.css">

<div class="contenido">

<section class="informes-panel">

<div class="titulo-modulo">
<h1><?= $tipo === 'estado_resultados' ? 'Estado de Resultados' : 'Balance General'; ?></h1>

<?php if($tipo==='estado_resultados'): ?>
<p>Período: <?= htmlspecialchars($inicio) ?> al <?= htmlspecialchars($fin) ?></p>
<?php else: ?>
<p>Fecha: <?= htmlspecialchars($fin) ?></p>
<?php endif; ?>
</div>

<?php if($tipo==='estado_resultados'):
$resultado = $informe->estadoResultados($inicio,$fin);
?>

<table class="tabla-informe">
<thead>
<tr>
<th>Concepto</th>
<th>Monto</th>
</tr>
</thead>
<tbody>
<tr>
<td>Ingresos</td>
<td><?= number_format($resultado['ingresos'],2) ?></td>
</tr>
<tr>
<td>Gastos</td>
<td><?= number_format($resultado['gastos'],2) ?></td>
</tr>
<tr>
<td><strong>Utilidad Neta</strong></td>
<td><strong><?= number_format($resultado['utilidad_neta'],2) ?></strong></td>
</tr>
</tbody>
</table>

<?php else:
$resultado = $informe->balanceGeneral($fin);
?>

<table class="tabla-informe">
<thead>
<tr>
<th>Concepto</th>
<th>Monto</th>
</tr>
</thead>
<tbody>
<tr><td>Activo</td><td><?= number_format($resultado['activo'],2) ?></td></tr>
<tr><td>Pasivo</td><td><?= number_format($resultado['pasivo'],2) ?></td></tr>
<tr><td>Patrimonio</td><td><?= number_format($resultado['patrimonio'],2) ?></td></tr>
<tr>
<td><strong>Pasivo + Patrimonio</strong></td>
<td><strong><?= number_format($resultado['pasivo']+$resultado['patrimonio'],2) ?></strong></td>
</tr>
</tbody>
</table>

<?php if($resultado['cuadra']): ?>
<div class="resultado-correcto">
El balance cumple la ecuación contable.
</div>
<?php else: ?>
<div class="resultado-error">
El balance NO cumple la ecuación contable.
</div>
<?php endif; ?>

<?php endif; ?>

<div class="acciones-informe">

<a class="boton boton-primario"
href="GenerarPdf.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
Generar PDF
</a>

<a class="boton boton-success"
href="FirmarInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
Firmar Informe
</a>

<a class="boton boton-warning"
href="VerificarInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
Verificar Confiabilidad
</a>

<a class="boton boton-danger"
href="SeleccionarInforme.php">
Volver
</a>

</div>

</section>

</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>