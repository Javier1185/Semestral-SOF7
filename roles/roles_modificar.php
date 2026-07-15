<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../config/config.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ' . BASE_URL . '/vistas/auth/login.php');
    exit;
}

if (!Sesion::tieneAcceso('roles', 'editar')) {
    die('No tiene permisos para modificar roles.');
}

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM roles WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$rol = $stmt->fetch();

if (!$rol) {
    die("Rol no encontrado.");
}

include '../vistas/layout/header.php';
include '../vistas/layout/sidebar.php';

?>

<h2>Editar Rol</h2>

<div class="card-formulario">

    <form action="roles_actualizar.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= $rol['id'] ?>">

        <div class="grupo-formulario">

            <label>Nombre del Rol</label>

            <input
                type="text"
                name="nombre"
                value="<?= htmlspecialchars($rol['nombre']) ?>"
                required>

        </div>

        <div class="acciones-formulario">

            <button
                type="submit"
                class="boton-login">

                Actualizar Rol

            </button>

            <a
                href="roles_index.php"
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