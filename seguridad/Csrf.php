<?php

require_once __DIR__ . '/../config/Sesion.php';

/**
 * ============================================================================
 * CLASE CSRF
 * ============================================================================
 * Protege los formularios del sistema contra ataques CSRF (Cross-Site
 * Request Forgery), donde un sitio externo podría forzar al navegador de
 * un usuario ya autenticado a enviar una petición (crear cuenta, registrar
 * asiento, etc.) sin que el usuario lo sepa.
 *
 * Uso en una vista (formulario):
 *      <?= Csrf::campoFormulario() ?>
 *
 * Uso en el script que procesa el POST:
 *      Csrf::validarOMorir();
 * ============================================================================
 */
class Csrf
{
    private const CLAVE_SESION = 'csrf_token';

    // Genera (o reutiliza) el token guardado en la sesión actual.
    public static function generarToken(): string
    {
        Sesion::iniciar();

        if (empty($_SESSION[self::CLAVE_SESION])) {
            $_SESSION[self::CLAVE_SESION] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CLAVE_SESION];
    }

    // Devuelve el <input type="hidden"> listo para insertar en un <form>.
    public static function campoFormulario(): string
    {
        $token = htmlspecialchars(self::generarToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    // Compara el token recibido contra el guardado en sesión.
    // hash_equals evita ataques de "timing" al comparar strings.
    public static function validarToken(?string $token): bool
    {
        Sesion::iniciar();

        if (empty($_SESSION[self::CLAVE_SESION]) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION[self::CLAVE_SESION], $token);
    }

    // Atajo para usar al inicio de cada script *_guardar.php / *_actualizar.php.
    public static function validarOMorir(): void
    {
        if (!self::validarToken($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            die('Token de seguridad inválido o expirado. Vuelve atrás e inténtalo de nuevo.');
        }
    }
}
