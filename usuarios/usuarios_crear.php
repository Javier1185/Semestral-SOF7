<?php

require_once '../config/Conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

/*
Traemos todos los roles para llenar el select.
*/
$sql = "SELECT id, nombre FROM roles ORDER BY nombre";
$roles = $pdo->query($sql)->fetchAll();

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Nuevo Usuario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

    <h2>Registrar Usuario</h2>

    <form action="usuarios_guardar.php" method="POST">

        <div class="mb-3">

            <label>Nombre</label>

            <input
                type="text"
                name="nombre"
                class="form-control"
                required>

        </div>

        <div class="mb-3">

            <label>Correo</label>

            <input
                type="email"
                name="correo"
                class="form-control"
                required>

        </div>

        <div class="mb-3">

            <label>Contraseña</label>

            <input
                type="password"
                name="contrasena"
                class="form-control"
                required>

        </div>

        <div class="mb-3">

            <label>Rol</label>

            <select
                name="rol_id"
                class="form-control"
                required>

                <?php foreach($roles as $rol): ?>

                    <option value="<?= $rol['id'] ?>">
                        <?= htmlspecialchars($rol['nombre']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <button class="btn btn-success">
            Guardar
        </button>

        <a href="usuarios_index.php" class="btn btn-secondary">
            Volver
        </a>

    </form>

</div>

</body>

</html>