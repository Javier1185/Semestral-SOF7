<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../modelos/Bitacora.php';

Sesion::iniciar();

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$nombre = trim($_POST['nombre']);

$sql = "
INSERT INTO roles
(
    nombre
)
VALUES
(
    ?
)
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $nombre
]);

$idRol = $pdo->lastInsertId();

Bitacora::registrar(
    Sesion::obtenerId(),
    'crear',
    'roles',
    $idRol,
    'Se creó el rol: ' . $nombre
);

header('Location: roles_index.php');
exit;
