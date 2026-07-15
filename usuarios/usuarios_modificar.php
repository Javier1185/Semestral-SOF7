<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../config/config.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ' . BASE_URL . '/vistas/auth/login.php');
    exit;
}

if (!Sesion::tieneAcceso('usuarios', 'editar')) {
    die('No tiene permisos para modificar usuarios.');
}

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

include '../vistas/layout/header.php';
include '../vistas/layout/sidebar.php';

?>

<h2>Editar Usuario</h2>

<div class="card-formulario">

    <form action="usuarios_actualizar.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= $usuario['id'] ?>">

        <div class="grupo-formulario">

            <label>Nombre del Usuario</label>

            <input
                type="text"
                name="nombre"
                value="<?= htmlspecialchars($usuario['nombre']) ?>"
                required>

        </div>

        <div class="grupo-formulario">

            <label>Correo Electrónico</label>

            <input
                type="email"
                name="correo"
                value="<?= htmlspecialchars($usuario['correo']) ?>"
                required>

        </div>

        <div class="grupo-formulario">

            <label>Rol</label>

            <select
                name="rol_id"
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

        <div class="acciones-formulario">

            <button
                type="submit"
                class="boton-login">

                Actualizar Usuario

            </button>

            <a
                href="usuarios_index.php"
                class="boton-secundario">

                Cancelar

            </a>

        </div>

    </form>

</div>

</main>

<?php
include '../vistas/layout/footer.php';
?>