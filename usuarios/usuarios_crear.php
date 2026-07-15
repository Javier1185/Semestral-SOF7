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
    die('No tiene permisos para crear usuarios.');
}

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

/*
Traemos todos los roles para llenar el select.
*/
$sql = "
SELECT
    id,
    nombre
FROM roles
ORDER BY nombre
";

$roles = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

include '../vistas/layout/header.php';
include '../vistas/layout/sidebar.php';

?>

<h2>Registrar Usuario</h2>

<div class="card-formulario">

    <form action="usuarios_guardar.php" method="POST">

        <div class="grupo-formulario">

            <label>Nombre del Usuario</label>

            <input
                type="text"
                name="nombre"
                required
                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{3,100}"
                title="Ingrese únicamente letras y espacios. Mínimo 3 caracteres.">

        </div>

        <div class="grupo-formulario">

            <label>Correo Electrónico</label>

            <input
                type="email"
                name="correo"
                required>

        </div>

        <div class="grupo-formulario">

            <label>Contraseña</label>

            <input
                type="password"
                name="contrasena"
                minlength="8"
                required>

        </div>

        <div class="grupo-formulario">

            <label>Rol</label>

            <select
                name="rol_id"
                required>

                <?php foreach ($roles as $rol): ?>

                    <option value="<?= $rol['id'] ?>">

                        <?= htmlspecialchars($rol['nombre']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="acciones-formulario">

            <button
                type="submit"
                class="boton-login">

                Registrar Usuario

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