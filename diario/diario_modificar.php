<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_GET['id'] ?? 0;

/* Cabecera */
$stmt = $pdo->prepare("
SELECT *
FROM diario
WHERE id = ?
");

$stmt->execute([$id]);

$diario = $stmt->fetch();

if (!$diario) {
    die("Asiento no encontrado");
}

/* Detalle */
$stmt = $pdo->prepare("
SELECT *
FROM diario_detalle
WHERE diario_id = ?
");

$stmt->execute([$id]);

$detalles = $stmt->fetchAll();

/* Cuentas */
$cuentas = $pdo->query("
SELECT id,codigo,nombre
FROM cuentas
WHERE activo = 1
ORDER BY codigo
")->fetchAll();

?>

<?php require_once '../vistas/layout/header.php'; ?>
<?php require_once '../vistas/layout/sidebar.php'; ?>

<div class="container mt-4">

<h2>Editar Asiento</h2>

<form action="diario_actualizar.php" method="POST">

<input type="hidden" name="id" value="<?= $diario['id'] ?>">

<div class="mb-3">
<label>Fecha</label>

<input
type="date"
name="fecha"
class="form-control"
value="<?= $diario['fecha'] ?>"
required>

</div>

<div class="mb-3">

<label>Descripción</label>

<input
type="text"
name="descripcion"
class="form-control"
value="<?= htmlspecialchars($diario['descripcion']) ?>"
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

<tbody>

<?php foreach($detalles as $detalle): ?>

<tr>

<td>

<select
name="cuenta_id[]"
class="form-control">

<?php foreach($cuentas as $cuenta): ?>

<option
value="<?= $cuenta['id'] ?>"
<?= ($cuenta['id'] == $detalle['cuenta_id']) ? 'selected' : '' ?>>

<?= $cuenta['codigo'] ?> - <?= $cuenta['nombre'] ?>

</option>

<?php endforeach; ?>

</select>

</td>

<td>

<input
type="number"
step="0.01"
name="debito[]"
value="<?= $detalle['debito'] ?>"
class="form-control">

</td>

<td>

<input
type="number"
step="0.01"
name="credito[]"
value="<?= $detalle['credito'] ?>"
class="form-control">

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<button class="btn btn-success">
Actualizar Asiento
</button>

<a href="diario_index.php" class="btn btn-secondary">
Cancelar
</a>

</form>

</div>

<?php require_once '../vistas/layout/footer.php'; ?>
