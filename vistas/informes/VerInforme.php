<?php

session_start();

require_once __DIR__ . '/../../modelos/informes_firma/InformeContable.php';
require_once __DIR__ . '/../../modelos/informes_firma/Validador.php';
require_once __DIR__ . '/../../modelos/Bitacora.php';

// Recibe y limpia datos.
$tipo = Validador::limpiarTexto($_GET['tipo'] ?? '');
$inicio = Validador::limpiarTexto($_GET['inicio'] ?? '');
$fin = Validador::limpiarTexto($_GET['fin'] ?? '');

// Valida tipo de informe.
if (!Validador::validarTipoInforme($tipo)) {
    die("Tipo de informe inválido.");
}

// Valida fecha final.
if (!Validador::validarFecha($fin)) {
    die("Fecha final inválida.");
}

// Valida fecha inicial solo para Estado de Resultados.
if ($tipo === 'estado_resultados' && !Validador::validarFecha($inicio)) {
    die("Fecha de inicio inválida.");
}

// Crea el objeto que calcula los informes.
$informe = new InformeContable();

// Registra en bitácora que el usuario visualizó el informe.
if (isset($_SESSION['usuario_id'])) {
    Bitacora::registrar(
        $_SESSION['usuario_id'],
        'VER_INFORME',
        null,
        null,
        'El usuario visualizó el informe ' . $tipo . ' del período ' . $inicio . ' al ' . $fin
    );
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Informe</title>
</head>
<body>

<h2>Resultado del Informe</h2>

<?php if ($tipo === 'estado_resultados'): ?>

    <?php $resultado = $informe->estadoResultados($inicio, $fin); ?>

    <h3>Estado de Resultados</h3>
    <p>Desde: <?= htmlspecialchars($inicio) ?> hasta <?= htmlspecialchars($fin) ?></p>

    <table border="1" cellpadding="8">
        <tr>
            <th>Concepto</th>
            <th>Monto</th>
        </tr>
        <tr>
            <td>Ingresos</td>
            <td><?= number_format($resultado['ingresos'], 2) ?></td>
        </tr>
        <tr>
            <td>Gastos</td>
            <td><?= number_format($resultado['gastos'], 2) ?></td>
        </tr>
        <tr>
            <td><strong>Utilidad Neta</strong></td>
            <td><strong><?= number_format($resultado['utilidad_neta'], 2) ?></strong></td>
        </tr>
    </table>

<?php else: ?>

    <?php $resultado = $informe->balanceGeneral($fin); ?>

    <h3>Balance General</h3>
    <p>Fecha: <?= htmlspecialchars($fin) ?></p>

    <table border="1" cellpadding="8">
        <tr>
            <th>Concepto</th>
            <th>Monto</th>
        </tr>
        <tr>
            <td>Activo</td>
            <td><?= number_format($resultado['activo'], 2) ?></td>
        </tr>
        <tr>
            <td>Pasivo</td>
            <td><?= number_format($resultado['pasivo'], 2) ?></td>
        </tr>
        <tr>
            <td>Patrimonio</td>
            <td><?= number_format($resultado['patrimonio'], 2) ?></td>
        </tr>
        <tr>
            <td><strong>Pasivo + Patrimonio</strong></td>
            <td><strong><?= number_format($resultado['pasivo'] + $resultado['patrimonio'], 2) ?></strong></td>
        </tr>
    </table>

    <?php if ($resultado['cuadra']): ?>
        <p style="color:green;">El balance cumple la ecuación contable.</p>
    <?php else: ?>
        <p style="color:red;">El balance NO cumple la ecuación contable.</p>
    <?php endif; ?>

<?php endif; ?>

<br><br>

<a href="GenerarPdf.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
    Generar PDF
</a>

<br><br>

<?php if (isset($_SESSION['usuario_id'])): ?>
    <a href="FirmarInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
        Firmar informe
    </a>
<?php endif; ?>

<br><br>

<a href="VerificarInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>">
    Verificar confiabilidad
</a>

<br><br>

<a href="SeleccionarInforme.php">Volver</a>

</body>
</html>