<?php

require_once __DIR__ . '/../config/Conexion.php';

/**
 * Clase Bitacora
 *
 * Registra acciones importantes que realiza un usuario dentro del sistema.
 * Ejemplo:
 * - Login
 * - Crear registros
 * - Actualizar registros
 * - Firmar informe
 * - Cerrar informe
 * - Generar PDF
 */
class Bitacora
{
    public static function registrar(
        $usuarioId,
        $accion,
        $tabla = null,
        $registroId = null,
        $detalle = null
    ): void {
        try {
            // Obtiene la conexión PDO del proyecto.
            $pdo = Conexion::obtenerInstancia()->obtenerPDO();

            // Guarda la IP del usuario.
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;

            // Inserta el registro en la tabla bitacora.
            $sql = "INSERT INTO bitacora 
                    (usuario_id, accion, tabla_afectada, registro_id, detalle, ip_address)
                    VALUES 
                    (:usuario_id, :accion, :tabla, :registro_id, :detalle, :ip)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':usuario_id'  => $usuarioId,
                ':accion'      => $accion,
                ':tabla'       => $tabla,
                ':registro_id' => $registroId,
                ':detalle'     => $detalle,
                ':ip'          => $ip
            ]);

        } catch (Exception $e) {
            // No detenemos el sistema si falla la bitácora.
            error_log("Error en bitácora: " . $e->getMessage());
        }
    }
}