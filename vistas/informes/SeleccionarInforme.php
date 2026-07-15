<?php

require_once __DIR__ . '/../../config/Sesion.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ../../index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| CARGAR LAYOUT DEL SISTEMA
|--------------------------------------------------------------------------
| Estas vistas mantienen el mismo encabezado, menú lateral y pie de página
| que utilizan los demás módulos.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<main class="contenido-principal">

    <section class="tarjeta informes-panel">

        <div class="titulo-modulo">
            <h1>Generar informe contable</h1>

            <p>
                Seleccione el tipo de informe y el período que desea consultar.
            </p>
        </div>

        <form
            action="<?= BASE_URL ?>/vistas/informes/VerInforme.php"
            method="GET"
            class="formulario-informe"
            id="formInforme"
        >

            <div class="campo-formulario">
                <label for="tipo">Tipo de informe</label>

                <select
                    name="tipo"
                    id="tipo"
                    required
                >
                    <option value="">Seleccione...</option>

                    <option value="estado_resultados">
                        Estado de Resultados
                    </option>

                    <option value="balance_general">
                        Balance General
                    </option>
                </select>
            </div>

            <div
                class="campo-formulario"
                id="contenedorInicio"
            >
                <label for="inicio">Fecha inicial</label>

                <input
                    type="date"
                    name="inicio"
                    id="inicio"
                >
            </div>

            <div class="campo-formulario">
                <label for="fin">Fecha final</label>

                <input
                    type="date"
                    name="fin"
                    id="fin"
                    required
                >
            </div>

            <div class="acciones-formulario">
                <button
                    type="submit"
                    class="boton boton-primario"
                >
                    Ver informe
                </button>
            </div>

        </form>

    </section>

</main>

<script>
    const tipoInforme = document.getElementById('tipo');
    const fechaInicio = document.getElementById('inicio');
    const contenedorInicio = document.getElementById('contenedorInicio');

    function actualizarCampos() {
        const esBalance = tipoInforme.value === 'balance_general';

        contenedorInicio.style.display = esBalance ? 'none' : 'block';
        fechaInicio.required = tipoInforme.value === 'estado_resultados';

        if (esBalance) {
            fechaInicio.value = '';
        }
    }

    tipoInforme.addEventListener('change', actualizarCampos);

    actualizarCampos();
</script>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>