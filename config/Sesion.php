<?php

require_once __DIR__ . '/../config/Conexion.php';

/**
 * Clase Sesion
 * Envuelve el manejo de $_SESSION y centraliza la pregunta
 * "¿este rol puede ver o editar tal módulo?"
 */
class Sesion
{
    // Arranca la sesión de PHP si todavía no ha arrancado.
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Guarda los datos del usuario que acaba de iniciar sesión.
    public static function guardarUsuario(array $usuario): void
    {
        self::iniciar();
        $_SESSION['usuario_id']  = $usuario['id'];
        $_SESSION['nombre']      = $usuario['nombre'];
        $_SESSION['correo']      = $usuario['correo'];
        $_SESSION['rol_id']      = $usuario['rol_id'];
        $_SESSION['rol_nombre']  = $usuario['rol_nombre'];
    }

    public static function estaLogueado(): bool
    {
        self::iniciar();
        return isset($_SESSION['usuario_id']);
    }

    // Devuelve los datos del usuario actual, o null si nadie ha iniciado sesión.
    public static function usuarioActual(): ?array
    {
        self::iniciar();
        if (!self::estaLogueado()) {
            return null;
        }

        return [
            'id'         => $_SESSION['usuario_id'],
            'nombre'     => $_SESSION['nombre'],
            'correo'     => $_SESSION['correo'],
            'rol_id'     => $_SESSION['rol_id'],
            'rol_nombre' => $_SESSION['rol_nombre'],
        ];
    }

    // Cierra la sesión por completo.
    public static function cerrarSesion(): void
    {
        self::iniciar();
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Pregunta si el rol del usuario logueado puede ver o editar un módulo.
     * $accion puede ser 'ver' o 'editar'.
     * Ejemplo de uso: Sesion::tieneAcceso('diario', 'editar')
     */
    public static function tieneAcceso(string $modulo, string $accion = 'ver'): bool
    {
        self::iniciar();

        if (!self::estaLogueado()) {
            return false;
        }

        $columna = $accion === 'editar' ? 'editar' : 'ver';

        $pdo = Conexion::obtenerInstancia()->obtenerPDO();
        $sql = "SELECT $columna FROM permisos WHERE rol_id = :rol_id AND modulo = :modulo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'rol_id' => $_SESSION['rol_id'],
            'modulo' => $modulo,
        ]);

        $resultado = $stmt->fetchColumn();

        // Si no hay ninguna fila para ese rol+modulo, se asume que no tiene acceso.
        return $resultado !== false && (int) $resultado === 1;
    }
}