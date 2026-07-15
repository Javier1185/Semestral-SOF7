<?php
/**
 * GenerarPdf.php
 * Versión integrada con Sesion, Bitácora y TCPDF.
 */

require_once __DIR__ . '/../../config/Sesion.php';
require_once __DIR__ . '/../../config/Conexion.php';
require_once __DIR__ . '/../../modelos/informes_firma/InformeContable.php';
require_once __DIR__ . '/../../modelos/informes_firma/Validador.php';
require_once __DIR__ . '/../../modelos/Bitacora.php';
require_once __DIR__ . '/../../tcpdf/tcpdf.php';

Sesion::iniciar();

if(!Sesion::estaLogueado()){
    die('Debe iniciar sesión.');
}

$tipo   = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin    = Validador::limpiarTexto($_GET['fin'] ?? '');

if(!Validador::validarTipoInforme($tipo)) die('Tipo inválido.');
if(!Validador::validarFecha($fin)) die('Fecha final inválida.');
if($tipo==='estado_resultados' && !Validador::validarFecha($inicio)) die('Fecha inicial inválida.');

Bitacora::registrar(
    Sesion::obtenerId(),
    'GENERAR_PDF',
    null,
    null,
    "Generó el PDF del informe {$tipo}"
);

$informe = new InformeContable();

$pdf = new TCPDF();
$pdf->SetCreator('Sistema Contable');
$pdf->SetAuthor('Grupo Desarrollo 7');
$pdf->SetTitle('Informe Contable');
$pdf->SetMargins(15,15,15);
$pdf->AddPage();

if(file_exists(__DIR__.'/../../assets/img/logo.png')){
    $pdf->Image(__DIR__.'/../../assets/img/logo.png',15,10,20);
}

$html = '<h2 style="text-align:center">INFORME CONTABLE</h2>';
$html .= '<p><strong>Fecha de generación:</strong> '.date('d/m/Y H:i').'</p>';
$html .= '<p><strong>Generado por:</strong> '.htmlspecialchars(Sesion::obtenerNombre()).'</p><hr>';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();
$q=$pdo->prepare("SELECT c.estado,c.fecha_cierre,u.nombre
FROM cierres c
LEFT JOIN usuarios u ON u.id=c.usuario_id
WHERE c.tipo=? AND c.periodo_fin=?
ORDER BY c.fecha_cierre DESC LIMIT 1");
$q->execute([$tipo,$fin]);
$cierre=$q->fetch();

if($cierre){
    $html.='<p><strong>Estado:</strong> '.$cierre['estado'].'</p>';
    $html.='<p><strong>Cerrado por:</strong> '.$cierre['nombre'].'</p>';
    $html.='<p><strong>Fecha de cierre:</strong> '.$cierre['fecha_cierre'].'</p><hr>';
}else{
    $html.='<p><strong>Estado:</strong> EN REVISIÓN</p><hr>';
}

if($tipo==='estado_resultados'){
    $r=$informe->estadoResultados($inicio,$fin);
    $html.='<h3>Estado de Resultados</h3>';
    $html.='<table border="1" cellpadding="6">
    <tr><th>Concepto</th><th>Monto</th></tr>
    <tr><td>Ingresos</td><td>'.number_format($r['ingresos'],2).'</td></tr>
    <tr><td>Gastos</td><td>'.number_format($r['gastos'],2).'</td></tr>
    <tr><td><b>Utilidad Neta</b></td><td><b>'.number_format($r['utilidad_neta'],2).'</b></td></tr>
    </table>';
}else{
    $r=$informe->balanceGeneral($fin);
    $html.='<h3>Balance General</h3>';
    $html.='<table border="1" cellpadding="6">
    <tr><th>Concepto</th><th>Monto</th></tr>
    <tr><td>Activo</td><td>'.number_format($r['activo'],2).'</td></tr>
    <tr><td>Pasivo</td><td>'.number_format($r['pasivo'],2).'</td></tr>
    <tr><td>Patrimonio</td><td>'.number_format($r['patrimonio'],2).'</td></tr>
    <tr><td><b>Pasivo + Patrimonio</b></td><td><b>'.number_format($r['pasivo']+$r['patrimonio'],2).'</b></td></tr>
    </table>';
}

$pdf->writeHTML($html,true,false,true,false,'');
$pdf->Output('InformeContable.pdf','I');