<?php

require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT *
    FROM cuentas
    WHERE id = ?
");

$stmt->execute([$id]);

$cuenta = $stmt->fetch();

if (!$cuenta) {
    die("Cuenta no encontrada");
}
?>

<?php require_once '../vistas/layout/header.php'; ?>
<?php require_once '../vistas/layout/sidebar.php'; ?>

<div class="container mt-4">

<h2>Editar Cuenta</h2>

<form action="cuentas_actualizar.php" method="POST">

<input type="hidden" name="id" value="<?= $cuenta['id'] ?>">

<div class="mb-3">
<label>Código</label>
<input
    type="text"
    name="codigo"
    class="form-control"
    value="<?= htmlspecialchars($cuenta['codigo']) ?>"
    required>
</div>

<div class="mb-3">
<label>Nombre</label>
<input
    type="text"
    name="nombre"
    class="form-control"
    value="<?= htmlspecialchars($cuenta['nombre']) ?>"
    required>
</div>

<div class="mb-3">
<label>Clase</label>

<select name="clase" class="form-control">

<option value="1" <?= $cuenta['clase']==1?'selected':'' ?>>
Activo
</option>

<option value="2" <?= $cuenta['clase']==2?'selected':'' ?>>
Pasivo
</option>

<option value="3" <?= $cuenta['clase']==3?'selected':'' ?>>
Patrimonio
</option>

<option value="4" <?= $cuenta['clase']==4?'selected':'' ?>>
Ingresos
</option>

<option value="5" <?= $cuenta['clase']==5?'selected':'' ?>>
Gastos
</option>

</select>

</div>

<div class="mb-3">

<label>Estado</label>

<select name="activo" class="form-control">

<option value="1" <?= $cuenta['activo']==1?'selected':'' ?>>
Activa
</option>

<option value="0" <?= $cuenta['activo']==0?'selected':'' ?>>
Inactiva
</option>

</select>

</div>

<button class="btn btn-success">
Actualizar
</button>

<a href="cuentas_index.php" class="btn btn-secondary">
Cancelar
</a>

</form>

</div>

<?php require_once '../vistas/layout/footer.php'; ?>
