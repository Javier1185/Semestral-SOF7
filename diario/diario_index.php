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

<!DOCTYPE html>

<html lang="es">
<head>
<meta charset="UTF-8">
<title>Diario General</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

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

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</body>
</html>
