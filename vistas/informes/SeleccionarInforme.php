<?php

/*
| VISTA PARA SELECCIONAR INFORME
|--------------------------------------------------------------------------
| Esta pantalla permite elegir:
|
| - Estado de Resultados.
| - Balance General.
| - Fecha inicial.
| - Fecha final.
*/

require_once __DIR__ . '/../../config/Sesion.php';

Sesion::iniciar();

/*
|--------------------------------------------------------------------------
| SEGURIDAD ADICIONAL
|--------------------------------------------------------------------------
| Aunque normalmente esta vista es abierta desde InformeController.php,
| se vuelve a comprobar que exista una sesión activa.
|--------------------------------------------------------------------------
*/

if (!Sesion::estaLogueado()) {
    header('Location: ../../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Seleccionar Informe</title>
</head>

<body>

    <h2>Generar Informe Contable</h2>

    <p>
        Seleccione el informe y el período que desea consultar.
    </p>

    <!--
        IMPORTANTE:

        El navegador mantiene como dirección actual:

        controladores/InformeController.php

        Por eso debemos salir de la carpeta "controladores" y entrar en:

        vistas/informes/VerInforme.php
    -->

    <form
        action="../vistas/informes/VerInforme.php"
        method="GET"
        id="formInforme"
    >

        <div>
            <label for="tipo">
                Tipo de informe:
            </label>

            <select
                name="tipo"
                id="tipo"
                required
            >
                <option value="">
                    Seleccione...
                </option>

                <option value="estado_resultados">
                    Estado de Resultados
                </option>

                <option value="balance_general">
                    Balance General
                </option>
            </select>
        </div>

        <br>

        <div id="contenedorInicio">
            <label for="inicio">
                Fecha inicial:
            </label>

            <input
                type="date"
                name="inicio"
                id="inicio"
            >
        </div>

        <br>

        <div>
            <label for="fin">
                Fecha final:
            </label>

            <input
                type="date"
                name="fin"
                id="fin"
                required
            >
        </div>

        <br>

        <button type="submit">
            Ver informe
        </button>

    </form>

    <script>
        /*
        |--------------------------------------------------------------------------
        | CONTROL DE FECHA INICIAL
        |--------------------------------------------------------------------------
        | El Estado de Resultados necesita fecha inicial y final.
        |
        | El Balance General representa la situación en una fecha determinada,
        | por lo que solamente necesita la fecha final.
        |--------------------------------------------------------------------------
        */

        const tipoInforme = document.getElementById('tipo');
        const fechaInicio = document.getElementById('inicio');
        const contenedorInicio = document.getElementById('contenedorInicio');

        function actualizarCampos() {
            if (tipoInforme.value === 'balance_general') {
                contenedorInicio.style.display = 'none';
                fechaInicio.required = false;
                fechaInicio.value = '';
            } else {
                contenedorInicio.style.display = 'block';
                fechaInicio.required =
                    tipoInforme.value === 'estado_resultados';
            }
        }

        tipoInforme.addEventListener('change', actualizarCampos);

        actualizarCampos();
    </script>

</body>

</html>