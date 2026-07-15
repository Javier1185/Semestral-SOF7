<?php

require_once __DIR__ . '/../config/Sesion.php';

/**
 * ============================================================================
 * CLASE AUTORIZACION
 * ============================================================================
 * Punto único para exigir sesión iniciada + permiso de módulo antes de
 * ejecutar cualquier script de cuentas/, diario/, etc.
 *
 * Uso (primera línea después de los require_once del archivo):
 *      Autorizacion::requerir('cuentas');            // solo necesita "ver"
 *      Autorizacion::requerir('cuentas', 'editar');   // necesita "editar"
 * ============================================================================
 */
class Autorizacion
{
    public static function requerir(string $modulo, string $accion = 'ver'): void
    {
        Sesion::iniciar();

        if (!Sesion::estaLogueado()) {
            header('Location: ../controladores/AuthController.php');
            exit;
        }

        if (!Sesion::tieneAcceso($modulo, $accion)) {
            http_response_code(403);
            die('No tienes permiso para acceder a este módulo.');
        }
    }
}
