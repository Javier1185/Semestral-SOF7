<?php
require_once __DIR__ . '/../../config/config.php';
?>

<nav class="barra-lateral">

    <a href="<?= BASE_URL ?>/index.php">Inicio</a>

    <a href="<?= BASE_URL ?>/usuarios/usuarios_index.php">
        Usuarios
    </a>

    <a href="<?= BASE_URL ?>/roles/roles_index.php">
        Roles y permisos
    </a>

    <a href="<?= BASE_URL ?>/cuentas/cuentas_index.php">
        Catálogo de cuentas
    </a>

    <a href="<?= BASE_URL ?>/diario/diario_index.php">
        Diario general
    </a>

    <?php if (Sesion::tieneAcceso('informes')): ?>
        <a href="<?= BASE_URL ?>/Controladores/InformeController.php">Informes</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('roles')): ?>
        <a href="<?= BASE_URL ?>/bitacora/bitacora_index.php">Bitácora</a>
    <?php endif; ?>

</nav>

</nav>