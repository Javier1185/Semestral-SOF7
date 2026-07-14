<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Sesion.php';
?>
<nav class="barra-lateral">
    <a href="<?= BASE_URL ?>/index.php">Inicio</a>

    <?php if (Sesion::tieneAcceso('usuarios')): ?>
        <a href="<?= BASE_URL ?>/Controladores/UsuarioController.php">Usuarios</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('roles')): ?>
        <a href="<?= BASE_URL ?>/Controladores/RolController.php">Roles y permisos</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('cuentas')): ?>
        <a href="<?= BASE_URL ?>/Controladores/CuentaController.php">Catálogo de cuentas</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('diario')): ?>
        <a href="<?= BASE_URL ?>/Controladores/DiarioController.php">Diario general</a>
    <?php endif; ?>

    <?php if (Sesion::tieneAcceso('informes')): ?>
        <a href="<?= BASE_URL ?>/Controladores/InformeController.php">Informes</a>
    <?php endif; ?>
</nav>

<main class="contenido">