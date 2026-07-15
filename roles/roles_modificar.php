<?php

require_once '../config/Conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM roles WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$rol = $stmt->fetch();

if (!$rol) {
    die("Rol no encontrado.");
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Rol</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <h2>Editar Rol</h2>

    <form action="roles_actualizar.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= $rol['id'] ?>">

        <div class="mb-3">

            <label>Nombre del Rol</label>

            <input
                type="text"
                name="nombre"
                class="form-control"
                value="<?= htmlspecialchars($rol['nombre']) ?>"
                required>

        </div>

        <button class="btn btn-success">
            Actualizar
        </button>

        <a href="roles_index.php" class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

</body>
</html>