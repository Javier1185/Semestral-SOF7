<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../config/config.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ' . BASE_URL . '/vistas/auth/login.php');
    exit;
}

if (!Sesion::tieneAcceso('usuarios')) {
    die('No tiene permisos para acceder a este módulo.');
}

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$sql = "
SELECT
    u.id,
    u.nombre,
    u.correo,
    r.nombre AS rol,
    u.estado_actividad,
    u.creado_en
FROM usuarios u
INNER JOIN roles r
ON u.rol_id = r.id
ORDER BY u.nombre
";

$usuarios = $pdo->query($sql)->fetchAll();

include '../vistas/layout/header.php';
include '../vistas/layout/sidebar.php';

?>

<h2>Gestión de Usuarios</h2>

<p>
    <a href="usuarios_crear.php" class="boton-login">
        Nuevo Usuario
    </a>
</p>

<table style="width:100%; border-collapse:collapse; background:#FFF; box-shadow:var(--sombra-suave);">

    <thead style="background:var(--color-primario); color:white;">

        <tr>

            <th style="padding:12px;">Nombre</th>

            <th>Correo</th>

            <th>Rol</th>

            <th>Estado</th>

            <th>Fecha</th>

            <th>Acciones</th>

        </tr>

    </thead>

    <tbody>

        <?php foreach($usuarios as $usuario): ?>

        <tr style="border-bottom:1px solid #ddd;">

            <td style="padding:12px;">
                <?= htmlspecialchars($usuario['nombre']) ?>
            </td>

            <td>
                <?= htmlspecialchars($usuario['correo']) ?>
            </td>

            <td>
                <?= htmlspecialchars($usuario['rol']) ?>
            </td>

           <td>

                 <?php if($usuario['estado_actividad']) : ?>

                   <span style="
                     background:#16A34A;
                     color:white;
                     padding:5px 12px;
                     border-radius:20px;
                     font-size:13px;
                     font-weight:bold;">
        Activo
    </span>

<?php else: ?>

    <span style="
        background:#DC2626;
        color:white;
        padding:5px 12px;
        border-radius:20px;
        font-size:13px;
        font-weight:bold;">
        Inactivo
    </span>

<?php endif; ?>

</td>

            <td>
                <?= $usuario['creado_en'] ?>
            </td>

            <td>

                <a
                    href="usuarios_modificar.php?id=<?= $usuario['id'] ?>">
                    Editar
                </a>

                |

                <a
                    href="usuarios_estado.php?id=<?= $usuario['id'] ?>"
                    onclick="return confirm('¿Desea cambiar el estado del usuario?');">

                    <?= $usuario['estado_actividad'] ? 'Desactivar' : 'Activar'; ?>

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