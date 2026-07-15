<?php

session_start();

require_once __DIR__ . '/../../modelos/informes_firma/InformeContable.php';
require_once __DIR__ . '/../../modelos/informes_firma/Validador.php';
require_once __DIR__ . '/../../modelos/Bitacora.php';
require_once __DIR__ . '/../../tcpdf/tcpdf.php';

/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE DATOS
|--------------------------------------------------------------------------
| Se reciben los parámetros enviados desde VerInforme.php y se validan
| antes de generar el PDF.
|--------------------------------------------------------------------------
*/

$tipo   = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin    = Validador::limpiarTexto($_GET['fin'] ?? '');

if (!Validador::validarTipoInforme($tipo)) {
    die("Tipo de informe inválido.");
}

if (!Validador::validarFecha($fin)) {
    die("La fecha final es inválida.");
}

if ($tipo === "estado_resultados" && !Validador::validarFecha($inicio)) {
    die("La fecha inicial es inválida.");
}

/*
REGISTRO EN BITÁCORA
Se registra que el usuario generó un PDF.
*/

if (isset($_SESSION['usuario_id'])) {

    Bitacora::registrar(
        $_SESSION['usuario_id'],
        'GENERAR_PDF',
        null,
        null,
        'Se generó el PDF del informe ' . $tipo .
        ' correspondiente al período ' . $inicio .
        ' - ' . $fin
    );
}

/*
|--------------------------------------------------------------------------
| OBTENER DATOS DEL INFORME
|--------------------------------------------------------------------------
*/

$informe = new InformeContable();

/*
|--------------------------------------------------------------------------
| CREAR PDF
|--------------------------------------------------------------------------
*/

$pdf = new TCPDF();

$pdf->SetCreator('Sistema Contable');
$pdf->SetAuthor('Grupo Desarrollo 7');
$pdf->SetTitle('Informe Contable');
$pdf->SetMargins(15,15,15);
$pdf->AddPage();

/*
|--------------------------------------------------------------------------
| CONTENIDO DEL PDF
|--------------------------------------------------------------------------
*/

$html = '
<h2 style="text-align:center;">INFORME CONTABLE</h2>
<hr>
';

if ($tipo === "estado_resultados") {

    $resultado = $informe->estadoResultados($inicio, $fin);

    $html .= '
    <h3>Estado de Resultados</h3>

    <p><strong>Período:</strong> '.$inicio.' al '.$fin.'</p>

    <table border="1" cellpadding="6">

        <tr bgcolor="#dddddd">
            <th width="70%">Concepto</th>
            <th width="30%">Monto</th>
        </tr>

        <tr>
            <td>Ingresos</td>
            <td>'.number_format($resultado["ingresos"],2).'</td>
        </tr>

        <tr>
            <td>Gastos</td>
            <td>'.number_format($resultado["gastos"],2).'</td>
        </tr>

        <tr>
            <td><strong>Utilidad Neta</strong></td>
            <td><strong>'.number_format($resultado["utilidad_neta"],2).'</strong></td>
        </tr>

    </table>';

} else {

    $resultado = $informe->balanceGeneral($fin);

    $html .= '
    <h3>Balance General</h3>

    <p><strong>Fecha:</strong> '.$fin.'</p>

    <table border="1" cellpadding="6">

        <tr bgcolor="#dddddd">
            <th width="70%">Concepto</th>
            <th width="30%">Monto</th>
        </tr>

        <tr>
            <td>Activo</td>
            <td>'.number_format($resultado["activo"],2).'</td>
        </tr>

        <tr>
            <td>Pasivo</td>
            <td>'.number_format($resultado["pasivo"],2).'</td>
        </tr>

        <tr>
            <td>Patrimonio</td>
            <td>'.number_format($resultado["patrimonio"],2).'</td>
        </tr>

        <tr>
            <td><strong>Pasivo + Patrimonio</strong></td>
            <td><strong>'.number_format($resultado["pasivo"] + $resultado["patrimonio"],2).'</strong></td>
        </tr>

    </table>';

    if($resultado["cuadra"]){

        $html .= '
        <br>
        <span style="color:green;">
            ✔ El Balance cumple la ecuación contable.
        </span>';

    }else{

        $html .= '
        <br>
        <span style="color:red;">
            ✘ El Balance NO cumple la ecuación contable.
        </span>';

    }

}

/*
ESCRIBIR EL CONTENIDO EN EL PDF
*/

$pdf->writeHTML($html,true,false,true,false,'');

/*
MOSTRAR PDF
*/

$pdf->Output('InformeContable.pdf','I');