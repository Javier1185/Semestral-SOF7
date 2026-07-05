<?php

require_once __DIR__ . '/../config/Sesion.php';

Sesion::cerrarSesion();
header('Location: ../vistas/publico/landing.php');
exit;