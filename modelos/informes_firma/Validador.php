<?php

class Validador {

    public static function limpiarTexto($dato) {
        return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
    }

    public static function validarFecha($fecha) {
        $formato = DateTime::createFromFormat('Y-m-d', $fecha);
        return $formato && $formato->format('Y-m-d') === $fecha;
    }

    public static function validarTipoInforme($tipo) {
        return in_array($tipo, ['estado_resultados', 'balance_general']);
    }
}