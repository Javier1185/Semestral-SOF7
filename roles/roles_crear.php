<?php
?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Nuevo Rol</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-4">

    <h2>Registrar Rol</h2>

    <form action="roles_guardar.php" method="POST">

        <div class="mb-3">

            <label>Nombre del Rol</label>

            <input
                type="text"
                name="nombre"
                class="form-control"
                required>

        </div>

        <button class="btn btn-success">
            Guardar
        </button>

        <a href="roles_index.php" class="btn btn-secondary">
            Volver
        </a>

    </form>

</div>

</body>

</html>