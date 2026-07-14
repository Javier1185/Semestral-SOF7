<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$cuentas = $pdo->query("
SELECT id,codigo,nombre
FROM cuentas
WHERE activo=1
ORDER BY codigo
")->fetchAll();

?>

<!DOCTYPE html>

<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Asiento</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
function agregarFila(){

const tabla =
document.getElementById("detalle");

const fila =
tabla.insertRow();

fila.innerHTML = `
<td>

<select
name="cuenta_id[]"
class="form-control"
required>

<?php foreach($cuentas as $c): ?>

<option value="<?= $c['id'] ?>">
<?= $c['codigo'] ?> - <?= $c['nombre'] ?>
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

</head>

<body>

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
class="form-control">

<?php foreach($cuentas as $c): ?>

<option value="<?= $c['id'] ?>">
<?= $c['codigo'] ?> - <?= $c['nombre'] ?>
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
Agregar Línea </button>

<button
type="submit"
class="btn btn-success">
Guardar Asiento </button>

</form>

</div>

</body>
</html>
