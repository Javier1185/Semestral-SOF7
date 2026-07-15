<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_GET['id'];

$stmt = $pdo->prepare("
SELECT
d.id,
d.fecha,
d.descripcion,
u.nombre usuario
FROM diario d
INNER JOIN usuarios u
ON d.usuario_id=u.id
WHERE d.id=?
");

$stmt->execute([$id]);

$diario = $stmt->fetch();

$stmt = $pdo->prepare("
SELECT
c.codigo,
c.nombre,
dd.debito,
dd.credito
FROM diario_detalle dd
INNER JOIN cuentas c
ON dd.cuenta_id=c.id
WHERE dd.diario_id=?
");

$stmt->execute([$id]);

$detalle = $stmt->fetchAll();

?>

<?php require_once '../vistas/layout/header.php'; ?>
<?php require_once '../vistas/layout/sidebar.php'; ?>

<div class="container mt-4">

<h2>Asiento #<?= $diario['id'] ?></h2>

<p>
<strong>Fecha:</strong>
<?= $diario['fecha'] ?>
</p>

<p>
<strong>Descripción:</strong>
<?= htmlspecialchars($diario['descripcion']) ?>
</p>

<p>
<strong>Usuario:</strong>
<?= htmlspecialchars($diario['usuario']) ?>
</p>

<table class="table table-bordered">

<thead>
<tr>
<th>Código</th>
<th>Cuenta</th>
<th>Débito</th>
<th>Crédito</th>
</tr>
</thead>

<tbody>

<?php foreach($detalle as $d): ?>

<tr>

<td><?= $d['codigo'] ?></td>

<td><?= htmlspecialchars($d['nombre']) ?></td>

<td><?= number_format($d['debito'],2) ?></td>

<td><?= number_format($d['credito'],2) ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<a
href="diario_index.php"
class="btn btn-secondary">
Volver </a>

</div>

<?php require_once '../vistas/layout/footer.php'; ?>
