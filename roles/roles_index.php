<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../config/config.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ' . BASE_URL . '/vistas/auth/login.php');
    exit;
}

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$sql = "
SELECT
    id,
    nombre
FROM roles
ORDER BY nombre
";

$roles = $pdo->query($sql)->fetchAll();

include '../vistas/layout/header.php';
include '../vistas/layout/sidebar.php';

?>

<h2>Gestión de Roles</h2>

<p>
    <a href="roles_crear.php" class="boton-login">
        Nuevo Rol
    </a>
</p>

<table style="width:100%;border-collapse:collapse;background:#FFF;box-shadow:var(--sombra-suave);">

    <thead style="background:var(--color-primario);color:white;">

        <tr>

            <th style="padding:12px;">ID</th>

            <th>Nombre</th>

            <th>Acciones</th>

        </tr>

    </thead>

    <tbody>

    <?php foreach($roles as $rol): ?>

        <tr style="border-bottom:1px solid #ddd;">

            <td style="padding:12px;">
                <?= $rol['id'] ?>
            </td>

            <td>
                <?= htmlspecialchars($rol['nombre']) ?>
            </td>

            <td>

                <a href="roles_modificar.php?id=<?= $rol['id'] ?>">
                    Editar
                </a>

            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

</main>

<?php
include '../vistas/layout/footer.php';
?>