<?php
require_once '../config/conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$sql = "
SELECT
    c.id,
    c.codigo,
    c.nombre,
    c.clase,
    c.fecha_registro,
    c.activo,
    u.nombre AS usuario
FROM cuentas c
INNER JOIN usuarios u
    ON c.usuario_id = u.id
ORDER BY c.codigo
";

$cuentas = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>

<html lang="es">
<head>
<meta charset="UTF-8">
<title>Catálogo de Cuentas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

```
<h2>Catálogo de Cuentas</h2>

<a href="cuentas_crear.php" class="btn btn-primary mb-3">
    Nueva Cuenta
</a>

<table class="table table-bordered table-hover">

    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Clase</th>
            <th>Usuario</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach($cuentas as $cuenta): ?>

    <tr>
        <td><?= htmlspecialchars($cuenta['codigo']) ?></td>

        <td><?= htmlspecialchars($cuenta['nombre']) ?></td>

        <td><?= $cuenta['clase'] ?></td>

        <td><?= htmlspecialchars($cuenta['usuario']) ?></td>

        <td><?= $cuenta['fecha_registro'] ?></td>

        <td>
            <?= $cuenta['activo'] ? 'Activa' : 'Inactiva' ?>
        </td>

        <td>

            <a
                href="cuentas_modificar.php?id=<?= $cuenta['id'] ?>"
                class="btn btn-warning btn-sm">
                Editar
            </a>

            <a
                href="cuentas_desactivar.php?id=<?= $cuenta['id'] ?>"
                class="btn btn-danger btn-sm"
                onclick="return confirm('¿Desactivar cuenta?')">
                Desactivar
            </a>

        </td>

    </tr>

    <?php endforeach; ?>

    </tbody>

</table>
```

</div>

</body>
</html>
