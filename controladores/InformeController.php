<?php

/*
|--------------------------------------------------------------------------
| CONTROLADOR DE INFORMES
|--------------------------------------------------------------------------
| Este controlador funciona como punto de entrada al módulo de informes.
|
| Sus responsabilidades son:
| 1. Iniciar la sesión.
| 2. Verificar que el usuario haya iniciado sesión.
| 3. Comprobar que su rol tenga permiso para ver informes.
| 4. Cargar la pantalla de selección de informes.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../config/Sesion.php';

/*
|--------------------------------------------------------------------------
| INICIAR SESIÓN
|--------------------------------------------------------------------------
| La clase Sesion evita iniciar dos veces la misma sesión.
|--------------------------------------------------------------------------
*/

Sesion::iniciar();

/*
|--------------------------------------------------------------------------
| VERIFICAR AUTENTICACIÓN
|--------------------------------------------------------------------------
| Si el usuario no ha iniciado sesión, se redirige al formulario de acceso.
|--------------------------------------------------------------------------
*/

if (!Sesion::estaLogueado()) {
    header('Location: ../index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| VERIFICAR PERMISOS
|--------------------------------------------------------------------------
| Consulta la tabla permisos para comprobar si el rol actual puede
| visualizar el módulo de informes.
|--------------------------------------------------------------------------
*/

if (!Sesion::tieneAcceso('informes', 'ver')) {
    http_response_code(403);
    die('No tiene permiso para acceder al módulo de informes.');
}

/*
|--------------------------------------------------------------------------
| CARGAR LA VISTA
|--------------------------------------------------------------------------
| Una vez validada la sesión y el permiso, se muestra el formulario.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../vistas/informes/SeleccionarInforme.php';