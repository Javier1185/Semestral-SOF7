<?php
/*
| Integra:
| - Sesión del proyecto.
| - Bitácora.
| - Header, Sidebar y Footer.
| - Estado de Resultados.
| - Balance General.
| - Scroll especial para evitar que el footer tape el Balance General.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../config/Sesion.php';
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
| RECIBIR Y SANITIZAR LOS PARÁMETROS
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

if (
    $tipo === 'estado_resultados'
    && $inicio > $fin
) {
    die('La fecha inicial no puede ser posterior a la fecha final.');
}

/*
|--------------------------------------------------------------------------
| CREAR EL MODELO DE INFORMES
|--------------------------------------------------------------------------
*/

$informe = new InformeContable();

/*
|--------------------------------------------------------------------------
| REGISTRAR LA CONSULTA EN LA BITÁCORA
|--------------------------------------------------------------------------
*/

$detallePeriodo = $tipo === 'balance_general'
    ? $fin
    : $inicio . ' al ' . $fin;

Bitacora::registrar(
    Sesion::obtenerId(),
    'VER_INFORME',
    null,
    null,
    'Visualizó el informe '
    . $tipo
    . ' correspondiente al período '
    . $detallePeriodo
);

/*
|--------------------------------------------------------------------------
| CARGAR EL LAYOUT DEL SISTEMA
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<link
    rel="stylesheet"
    href="<?= BASE_URL ?>/assets/css/informe/estilo.css"
>

<!--
|--------------------------------------------------------------------------
| ESTILO LOCAL PARA EL SCROLL
|--------------------------------------------------------------------------
| Solo afecta esta página.
| No modifica el CSS general ni las demás pantallas del sistema.
|--------------------------------------------------------------------------
-->

<style>
    .contenedor-scroll-informe {
        width: 100%;
    }

    .contenedor-scroll-informe.balance-con-scroll {
        /*
         * Limita la altura disponible y permite desplazamiento vertical.
         * El espacio inferior evita que el footer tape los botones.
         */
        max-height: calc(100vh - 145px);
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 12px;
        padding-bottom: 140px;
        scrollbar-gutter: stable;
    }

    .contenedor-scroll-informe.balance-con-scroll .informes-panel {
        margin-bottom: 40px;
    }

    @media (max-width: 768px) {
        .contenedor-scroll-informe.balance-con-scroll {
            max-height: calc(100vh - 125px);
            padding-bottom: 170px;
        }
    }
</style>

<div class="contenido">

    <?php
    /*
    |--------------------------------------------------------------------------
    | CLASE CONDICIONAL DEL CONTENEDOR
    |--------------------------------------------------------------------------
    | El scroll especial se aplica únicamente al Balance General.
    |--------------------------------------------------------------------------
    */

    $claseScroll = $tipo === 'balance_general'
        ? 'contenedor-scroll-informe balance-con-scroll'
        : 'contenedor-scroll-informe';
    ?>

    <div class="<?= $claseScroll ?>">

        <section class="informes-panel">

            <div class="titulo-modulo">

                <h1>
                    <?= $tipo === 'estado_resultados'
                        ? 'Estado de Resultados'
                        : 'Balance General'
                    ?>
                </h1>

                <?php if ($tipo === 'estado_resultados'): ?>

                    <p>
                        Período:
                        <?= htmlspecialchars(
                            $inicio,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        al
                        <?= htmlspecialchars(
                            $fin,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                <?php else: ?>

                    <p>
                        Fecha:
                        <?= htmlspecialchars(
                            $fin,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                <?php endif; ?>

            </div>

            <?php if ($tipo === 'estado_resultados'): ?>

                <?php
                /*
                |--------------------------------------------------------------------------
                | ESTADO DE RESULTADOS
                |--------------------------------------------------------------------------
                */

                $resultado = $informe->estadoResultados(
                    $inicio,
                    $fin
                );
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

                            <td>
                                <?= number_format(
                                    $resultado['ingresos'],
                                    2
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Gastos</td>

                            <td>
                                <?= number_format(
                                    $resultado['gastos'],
                                    2
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Utilidad Neta</strong>
                            </td>

                            <td>
                                <strong>
                                    <?= number_format(
                                        $resultado['utilidad_neta'],
                                        2
                                    ) ?>
                                </strong>
                            </td>
                        </tr>

                    </tbody>

                </table>

            <?php else: ?>

                <?php
                /*
                |--------------------------------------------------------------------------
                | BALANCE GENERAL
                |--------------------------------------------------------------------------
                */

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

                        <tr>
                            <td>Activo</td>

                            <td>
                                <?= number_format(
                                    $resultado['activo'],
                                    2
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Pasivo</td>

                            <td>
                                <?= number_format(
                                    $resultado['pasivo'],
                                    2
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Patrimonio</td>

                            <td>
                                <?= number_format(
                                    $resultado['patrimonio'],
                                    2
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>
                                    Pasivo + Patrimonio
                                </strong>
                            </td>

                            <td>
                                <strong>
                                    <?= number_format(
                                        $resultado['pasivo']
                                        + $resultado['patrimonio'],
                                        2
                                    ) ?>
                                </strong>
                            </td>
                        </tr>

                    </tbody>

                </table>

                <?php if ($resultado['cuadra']): ?>

                    <div class="resultado-correcto">
                        El balance cumple la ecuación contable.
                    </div>

                <?php else: ?>

                    <div class="resultado-error">
                        El balance NO cumple la ecuación contable.
                    </div>

                <?php endif; ?>

            <?php endif; ?>

            <!-- Acciones disponibles para ambos informes -->

            <div class="acciones-informe">

                <a
                    class="boton boton-primario"
                    href="GenerarPdf.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>"
                >
                    Generar PDF
                </a>

                <a
                    class="boton boton-success"
                    href="FirmarInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>"
                >
                    Firmar Informe
                </a>

                <a
                    class="boton boton-warning"
                    href="VerificarInforme.php?tipo=<?= urlencode($tipo) ?>&inicio=<?= urlencode($inicio) ?>&fin=<?= urlencode($fin) ?>"
                >
                    Verificar Confiabilidad
                </a>

                <a
                    class="boton boton-danger"
                    href="SeleccionarInforme.php"
                >
                    Volver
                </a>

            </div>

        </section>

    </div>

</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>