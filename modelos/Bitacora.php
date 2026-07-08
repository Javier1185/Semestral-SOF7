<?php

require_once __DIR__ . '/../config/Conexion.php';

/**
 * Registra cada acción relevante que hace un usuario dentro del sistema
 * (login, login fallido, crear, actualizar, ocultar, cerrar informe, etc.)
 * Ubicar este archivo en: modelos/Bitacora.php
 */
class Bitacora
{
    public static function registrar(
        ?int $usuarioId,
        string $accion,
        ?string $tabla = null,
        ?int $registroId = null,
        ?string $detalle = null
    ): void {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $sql = "INSERT INTO bitacora (usuario_id, accion, tabla_afectada, registro_id, detalle, ip_address)
                VALUES (:usuario_id, :accion, :tabla, :registro_id, :detalle, :ip)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'usuario_id'  => $usuarioId,
            'accion'      => $accion,
            'tabla'       => $tabla,
            'registro_id' => $registroId,
            'detalle'     => $detalle,
            'ip'          => $ip,
        ]);
    }
}