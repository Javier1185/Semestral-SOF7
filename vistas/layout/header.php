<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/Sesion.php';

$usuario = Sesion::usuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Contable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilo.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/informe/estilo.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/barra.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
</head>             
<body>

<header class="barra-superior">
    <span class="marca">Sistema Contable</span>

    <?php if ($usuario): ?>
        <div class="info-usuario">
            <span><?= htmlspecialchars($usuario['nombre']) ?> · <?= htmlspecialchars($usuario['rol_nombre']) ?></span>
            <a href="<?= BASE_URL ?>/controladores/LogoutController.php">Cerrar sesión</a>
        </div>
    <?php endif; ?>
</header>

<div class="contenedor-principal">