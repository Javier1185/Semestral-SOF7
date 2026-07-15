<?php

require_once '../config/Conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_GET['id'] ?? 0;

// Obtener usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$usuario = $stmt->fetch();

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Obtener roles
$sqlRoles = "SELECT id, nombre FROM roles ORDER BY nombre";
$roles = $pdo->query($sqlRoles)->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <h2>Editar Usuario</h2>

    <form action="usuarios_actualizar.php" method="POST">

        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

        <div class="mb-3">
            <label>Nombre</label>
            <input
                type="text"
                name="nombre"
                class="form-control"
                value="<?= htmlspecialchars($usuario['nombre']) ?>"
                required>
        </div>

        <div class="mb-3">
            <label>Correo</label>
            <input
                type="email"
                name="correo"
                class="form-control"
                value="<?= htmlspecialchars($usuario['correo']) ?>"
                required>
        </div>

        <div class="mb-3">
            <label>Rol</label>

            <select
                name="rol_id"
                class="form-control"
                required>

                <?php foreach($roles as $rol): ?>

                    <option
                        value="<?= $rol['id'] ?>"
                        <?= ($rol['id'] == $usuario['rol_id']) ? 'selected' : '' ?>>

                        <?= htmlspecialchars($rol['nombre']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <button class="btn btn-success">
            Actualizar
        </button>

        <a href="usuarios_index.php" class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

</body>
</html>