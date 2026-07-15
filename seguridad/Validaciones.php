<?php

class Validaciones
{
    // Quita espacios y etiquetas HTML sueltas para prevenir XSS básico.
    public static function sanitizarTexto(string $valor): string
    {
        $valor = trim($valor);
        $valor = strip_tags($valor);
        return $valor;
    }

    // Valida que un correo tenga formato correcto.
    public static function validarCorreo(string $correo): bool
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Valida que un valor no venga vacío después de sanitizarlo.
    public static function noVacio(string $valor): bool
    {
        return self::sanitizarTexto($valor) !== '';
    }

    // Valida que el nombre solo contenga letras, espacios y acentos.
    public static function validarNombre(string $nombre): bool
    {
        return preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{3,100}$/u', trim($nombre)) === 1;
    }

    // Valida que un valor sea numérico (útil para montos de debito/credito).
    public static function esNumerico($valor): bool
    {
        return is_numeric($valor);
    }

    // Valida que una fecha venga en formato YYYY-MM-DD y sea una fecha real.
    public static function validarFecha(string $fecha): bool
    {
        $partes = explode('-', $fecha);
        if (count($partes) !== 3) {
            return false;
        }

        [$anio, $mes, $dia] = $partes;

        return checkdate((int)$mes, (int)$dia, (int)$anio);
    }
}