<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$sql = "
SELECT
    d.id,
    d.fecha,
    d.descripcion,
    d.estado,
    u.nombre AS usuario
FROM diario d
INNER JOIN usuarios u
ON d.usuario_id = u.id
ORDER BY d.fecha DESC
";

$diarios = $pdo->query($sql)->fetchAll();

?>

<?php require_once '../vistas/layout/header.php'; ?>
<?php require_once '../vistas/layout/sidebar.php'; ?>

<div class="container mt-4">

<h2>Diario General</h2>

<a href="diario_nuevo.php" class="btn btn-primary mb-3">
Nuevo Asiento
</a>

<table class="table table-bordered">

<thead>
<tr>
<th>ID</th>
<th>Fecha</th>
<th>Descripción</th>
<th>Usuario</th>
<th>Estado</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>

<?php foreach($diarios as $d): ?>

<tr>

<td><?= $d['id'] ?></td>
<td><?= $d['fecha'] ?></td>
<td><?= htmlspecialchars($d['descripcion']) ?></td>
<td><?= htmlspecialchars($d['usuario']) ?></td>
<td><?= htmlspecialchars($d['estado']) ?></td>

<td>

<a
href="diario_ver.php?id=<?= $d['id'] ?>"
class="btn btn-info btn-sm">
Ver </a>

<a href="bitacora/bitacora_index.php" class="btn btn-info">
    Ver Bitácora
</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php require_once '../vistas/layout/footer.php'; ?>
