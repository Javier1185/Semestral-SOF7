<?php

require_once '../config/Conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$cuentas = $pdo->query("
    SELECT id, codigo, nombre
    FROM cuentas
    WHERE activo = 1
    ORDER BY codigo
")->fetchAll();

require_once '../vistas/layout/header.php';
require_once '../vistas/layout/sidebar.php';
?>

<div class="container mt-4">

    <h2>Nuevo Asiento</h2>

    <form action="diario_guardar.php" method="POST">

        <div class="mb-3">
            <label>Fecha</label>
            <input
                type="date"
                name="fecha"
                class="form-control"
                required>
        </div>

        <div class="mb-3">
            <label>Descripción</label>
            <input
                type="text"
                name="descripcion"
                class="form-control"
                required>
        </div>

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>Cuenta</th>
                    <th>Débito</th>
                    <th>Crédito</th>
                </tr>
            </thead>

            <tbody id="detalle">

                <tr>

                    <td>
                        <select
                            name="cuenta_id[]"
                            class="form-control"
                            required>

                            <?php foreach($cuentas as $cuenta): ?>

                                <option value="<?= $cuenta['id'] ?>">
                                    <?= $cuenta['codigo'] ?> - <?= htmlspecialchars($cuenta['nombre']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </td>

                    <td>
                        <input
                            type="number"
                            step="0.01"
                            name="debito[]"
                            class="form-control"
                            value="0">
                    </td>

                    <td>
                        <input
                            type="number"
                            step="0.01"
                            name="credito[]"
                            class="form-control"
                            value="0">
                    </td>

                </tr>

            </tbody>

        </table>

        <button
            type="button"
            class="btn btn-secondary"
            onclick="agregarFila()">
            Agregar Línea
        </button>

        <button
            type="submit"
            class="btn btn-success">
            Guardar Asiento
        </button>

    </form>

</div>

<script>

function agregarFila() {

    const tabla = document.getElementById("detalle");

    const fila = tabla.insertRow();

    fila.innerHTML = `
        <td>
            <select
                name="cuenta_id[]"
                class="form-control"
                required>

                <?php foreach($cuentas as $cuenta): ?>
                    <option value="<?= $cuenta['id'] ?>">
                        <?= $cuenta['codigo'] ?> - <?= htmlspecialchars($cuenta['nombre']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </td>

        <td>
            <input
                type="number"
                step="0.01"
                name="debito[]"
                class="form-control"
                value="0">
        </td>

        <td>
            <input
                type="number"
                step="0.01"
                name="credito[]"
                class="form-control"
                value="0">
        </td>
    `;
}

</script>

<?php require_once '../vistas/layout/footer.php'; ?>