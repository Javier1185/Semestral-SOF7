<?php
?>

<!DOCTYPE html>

<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva Cuenta</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h2>Registrar Cuenta</h2>

<form action="cuentas_guardar.php" method="POST">

<div class="mb-3">
<label>Código</label>
<input
    type="text"
    name="codigo"
    class="form-control"
    required>
</div>

<div class="mb-3">
<label>Nombre</label>
<input
    type="text"
    name="nombre"
    class="form-control"
    required>
</div>

<div class="mb-3">
<label>Clase</label>

<select
 name="clase"
 class="form-control"
 required>

```
<option value="1">Activo</option>
<option value="2">Pasivo</option>
<option value="3">Patrimonio</option>
<option value="4">Ingresos</option>
<option value="5">Gastos</option>
```

</select>

</div>

<button class="btn btn-success">
Guardar
</button>

<a href="cuentas_index.php" class="btn btn-secondary">
Volver
</a>

</form>

</div>

</body>
</html>
