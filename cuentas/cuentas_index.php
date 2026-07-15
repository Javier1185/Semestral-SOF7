<?php
require_once '../config/Conexion.php';

try {

    $pdo = Conexion::obtenerInstancia()->obtenerPDO();

    $sql = "SELECT
                c.id,
                c.codigo,
                c.nombre,
                c.clase,
                c.fecha_registro,
                c.activo,
                u.nombre AS usuario
            FROM cuentas c
            LEFT JOIN usuarios u
                ON c.usuario_id = u.id
            ORDER BY c.codigo";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

require_once '../vistas/layout/header.php';
require_once '../vistas/layout/sidebar.php';
?>

<div class="container mt-4">

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

<?php if(count($cuentas)>0): ?>

<?php foreach($cuentas as $cuenta): ?>

<tr>

<td><?= htmlspecialchars($cuenta['codigo']) ?></td>

<td><?= htmlspecialchars($cuenta['nombre']) ?></td>

<td><?= htmlspecialchars($cuenta['clase']) ?></td>

<td><?= htmlspecialchars($cuenta['usuario'] ?? '') ?></td>

<td><?= htmlspecialchars($cuenta['fecha_registro']) ?></td>

<td><?= $cuenta['activo'] ? 'Activa':'Inactiva' ?></td>

<td>

<a href="cuentas_modificar.php?id=<?= $cuenta['id'] ?>" class="btn btn-warning btn-sm">Editar</a>

<a href="cuentas_desactivar.php?id=<?= $cuenta['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Desactivar cuenta?')">Desactivar</a>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>
<td colspan="7" class="text-center">
No existen cuentas registradas.
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

<?php require_once '../vistas/layout/footer.php'; ?>