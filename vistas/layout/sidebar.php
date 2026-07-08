<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Sesion.php';
?>
<nav class="barra-lateral">
    <a href="<?= BASE_URL ?>/index.php">Inicio</a>

    <?php if (Sesion::tieneAcceso('cuentas')): ?>
        <a href="<?= BASE_URL ?>/cuentas/cuentas_index.php">Catálogo de cuentas</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('diario')): ?>
        <a href="<?= BASE_URL ?>/diario/diario_index.php">Diario general</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('usuarios')): ?>
        <a href="<?= BASE_URL ?>/controladores/UsuarioController.php">Usuarios</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('roles')): ?>
        <a href="<?= BASE_URL ?>/controladores/RolController.php">Roles y permisos</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('informes')): ?>
        <a href="<?= BASE_URL ?>/controladores/InformeController.php">Informes</a>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>/bitacora/bitacora_index.php" class="btn btn-info">
        Ver Bitácora
    </a>
</nav>

<main class="contenido">