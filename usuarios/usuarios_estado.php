<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../modelos/Bitacora.php';

Sesion::iniciar();

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$id = (int) ($_GET['id'] ?? 0);

// Obtener estado actual y nombre
$sql = "SELECT nombre, estado_actividad FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$usuario = $stmt->fetch();

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Cambiar estado
$nuevoEstado = $usuario['estado_actividad'] ? 0 : 1;

$sql = "
UPDATE usuarios
SET
    estado_actividad = ?,
    actualizado_por = ?,
    fecha_actualizacion = NOW()
WHERE id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $nuevoEstado,
    Sesion::obtenerId(),
    $id
]);

Bitacora::registrar(
    Sesion::obtenerId(),
    'cambio_estado',
    'usuarios',
    $id,
    'Usuario "' . $usuario['nombre'] . '" cambiado a ' . ($nuevoEstado ? 'Activo' : 'Inactivo')
);

header('Location: usuarios_index.php');
exit;