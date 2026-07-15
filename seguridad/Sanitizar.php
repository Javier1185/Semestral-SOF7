<?php

/**
 * Clase encargada únicamente de LIMPIAR datos de entrada (inputs).
 * No valida reglas de negocio (eso lo hace Validaciones) ni
 * reemplaza el uso de PDO con parámetros preparados (que ya
 * previene inyección SQL). Su objetivo es normalizar strings,
 * remover HTML/tags sueltos y prevenir XSS básico antes de
 * usar el dato o guardarlo en BD.
 */
class Sanitizar
{
    /**
     * Limpieza general de texto: quita espacios extremos,
     * remueve etiquetas HTML y normaliza espacios múltiples.
     */
    public static function texto(?string $valor): string
    {
        $valor = (string) $valor;
        $valor = trim($valor);
        $valor = strip_tags($valor);
        // Colapsa espacios/tabs/saltos de línea repetidos en uno solo.
        $valor = preg_replace('/\s+/', ' ', $valor);
        return $valor;
    }

    /**
     * Limpieza específica para correos: quita espacios, pasa a
     * minúsculas y remueve caracteres no permitidos en un email
     * usando el filtro nativo de PHP.
     */
    public static function correo(?string $valor): string
    {
        $valor = trim((string) $valor);
        $valor = strtolower($valor);
        $valor = filter_var($valor, FILTER_SANITIZE_EMAIL);
        return $valor === false ? '' : $valor;
    }

    /**
     * Para contraseñas: NO se debe hacer strip_tags ni trim agresivo,
     * porque podría alterar la contraseña real del usuario (por ejemplo,
     * si el usuario puso espacios intencionales). Solo forzamos que
     * sea string y limitamos su longitud máxima para evitar abusos
     * (ataques de contraseñas extremadamente largas / DoS en el hash).
     */
    public static function contrasena(?string $valor, int $largoMaximo = 255): string
    {
        $valor = (string) $valor;
        if (strlen($valor) > $largoMaximo) {
            $valor = substr($valor, 0, $largoMaximo);
        }
        return $valor;
    }

    /**
     * Sanitiza un entero (útil para ids, cantidades, etc.).
     */
    public static function entero($valor): int
    {
        return (int) filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitiza un número decimal (útil para montos de débito/crédito).
     */
    public static function decimal($valor): float
    {
        $valor = filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return (float) $valor;
    }

    /**
     * Sanitiza texto que se va a mostrar (echo) en HTML,
     * escapando caracteres especiales. Útil para salida, no para
     * guardar en BD.
     */
    public static function paraSalidaHtml(?string $valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Aplica texto() a todos los valores de un arreglo (por ejemplo,
     * para limpiar $_POST completo de una vez).
     */
    public static function arreglo(array $datos): array
    {
        $limpio = [];
        foreach ($datos as $clave => $valor) {
            if (is_array($valor)) {
                $limpio[$clave] = self::arreglo($valor);
            } else {
                $limpio[$clave] = self::texto((string) $valor);
            }
        }
        return $limpio;
    }
}