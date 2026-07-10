<?php

require_once __DIR__ . '/../config/Conexion.php';

/**
 * ============================================================================
 * CLASE SESION
 * ============================================================================
 * Esta clase centraliza el manejo de la sesión del usuario.
 *
 * Funciones principales:
 *  - Iniciar la sesión.
 *  - Guardar los datos del usuario autenticado.
 *  - Consultar el usuario actual.
 *  - Verificar si existe una sesión iniciada.
 *  - Cerrar la sesión.
 *  - Consultar permisos sobre los módulos del sistema.
 *
 * De esta forma evitamos trabajar directamente con $_SESSION en todo
 * el proyecto y mantenemos un código más limpio y organizado.
 * ============================================================================
 */
class Sesion
{

    /**
     * ------------------------------------------------------------------------
     * Inicia la sesión únicamente si todavía no existe.
     * ------------------------------------------------------------------------
     */
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * ------------------------------------------------------------------------
     * Guarda la información del usuario una vez autenticado.
     * ------------------------------------------------------------------------
     */
    public static function guardarUsuario(array $usuario): void
    {
        self::iniciar();

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['rol_id'] = $usuario['rol_id'];
        $_SESSION['rol_nombre'] = $usuario['rol_nombre'];
    }

    /**
     * ------------------------------------------------------------------------
     * Verifica si existe un usuario autenticado.
     * ------------------------------------------------------------------------
     */
    public static function estaLogueado(): bool
    {
        self::iniciar();

        return isset($_SESSION['usuario_id']);
    }

    /**
     * ------------------------------------------------------------------------
     * Devuelve toda la información del usuario autenticado.
     * ------------------------------------------------------------------------
     */
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
            'rol_nombre' => $_SESSION['rol_nombre']
        ];
    }

    /**
     * ------------------------------------------------------------------------
     * Devuelve únicamente el nombre del rol.
     *
     * Ejemplo:
     *      Gerente Financiero
     *      Contador
     *      Auditor
     * ------------------------------------------------------------------------
     */
    public static function obtenerRol(): ?string
    {
        self::iniciar();

        return $_SESSION['rol_nombre'] ?? null;
    }

    /**
     * ------------------------------------------------------------------------
     * Devuelve únicamente el nombre del usuario.
     * ------------------------------------------------------------------------
     */
    public static function obtenerNombre(): ?string
    {
        self::iniciar();

        return $_SESSION['nombre'] ?? null;
    }

    /**
     * ------------------------------------------------------------------------
     * Devuelve únicamente el ID del usuario.
     * ------------------------------------------------------------------------
     */
    public static function obtenerId(): ?int
    {
        self::iniciar();

        return $_SESSION['usuario_id'] ?? null;
    }

    /**
     * ------------------------------------------------------------------------
     * Cierra completamente la sesión.
     * ------------------------------------------------------------------------
     */
    public static function cerrarSesion(): void
    {
        self::iniciar();

        $_SESSION = [];

        session_destroy();
    }

    /**
     * ------------------------------------------------------------------------
     * Verifica si el usuario tiene permiso sobre un módulo.
     *
     * Ejemplos:
     *
     *  Sesion::tieneAcceso('usuarios');
     *
     *  Sesion::tieneAcceso('diario','editar');
     *
     *  Sesion::tieneAcceso('informes');
     * ------------------------------------------------------------------------
     */
    public static function tieneAcceso(
        string $modulo,
        string $accion = 'ver'
    ): bool {

        self::iniciar();

        if (!self::estaLogueado()) {
            return false;
        }

        $columna = ($accion === 'editar')
            ? 'editar'
            : 'ver';

        $pdo = Conexion::obtenerInstancia()->obtenerPDO();

        $sql = "
            SELECT $columna
            FROM permisos
            WHERE rol_id = :rol_id
            AND modulo = :modulo
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':rol_id' => $_SESSION['rol_id'],
            ':modulo' => $modulo
        ]);

        $resultado = $stmt->fetchColumn();

        return ($resultado !== false && (int)$resultado === 1);
    }
}