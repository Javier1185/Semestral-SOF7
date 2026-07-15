<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../config/config.php';

/*
Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ' . BASE_URL . '/vistas/auth/login.php');
    exit;
}
*/

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$sql = "
SELECT
    b.id,
    b.fecha,
    b.accion,
    b.tabla_afectada,
    b.registro_id,
    b.detalle,
    b.ip_address,
    u.nombre AS usuario
FROM bitacora b
LEFT JOIN usuarios u
ON b.usuario_id = u.id
ORDER BY b.fecha DESC
";

$bitacora = $pdo->query($sql)->fetchAll();

include '../vistas/layout/header.php';
include '../vistas/layout/sidebar.php';

?>

<h2>Bitácora del Sistema</h2>

<div class="contenedor-bitacora">

<table style="width:100%;border-collapse:collapse;background:white;box-shadow:var(--sombra-suave);">

    <thead style="background:var(--color-primario);color:white;position:sticky;top:0;z-index:10;">

        <tr>

            <th style="padding:10px;">Fecha</th>

            <th>Usuario</th>

            <th>Acción</th>

            <th>Tabla</th>

            <th>Registro</th>

            <th>Detalle</th>

            <th>IP</th>

        </tr>

    </thead>

    <tbody>

    <?php foreach($bitacora as $fila): ?>

        <tr style="border-bottom:1px solid #ddd;">

            <td style="padding:10px;">
                <?= $fila['fecha'] ?>
            </td>

            <td>
                <?= htmlspecialchars($fila['usuario'] ?? 'Sistema') ?>
            </td>

            <td>
                <?= htmlspecialchars($fila['accion']) ?>
            </td>

            <td>
                <?= htmlspecialchars($fila['tabla_afectada']) ?>
            </td>

            <td>
                <?= $fila['registro_id'] ?>
            </td>

            <td>
                <?= htmlspecialchars($fila['detalle']) ?>
            </td>

            <td>
                <?= htmlspecialchars($fila['ip_address']) ?>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

</div>

</main>

<?php
include '../vistas/layout/footer.php';
?>