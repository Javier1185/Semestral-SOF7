<?php

require_once __DIR__ . '/../../config/Conexion.php';

class InformeContable {

    private PDO $conexion;

    public function __construct() {
        $this->conexion = Conexion::obtenerInstancia()->obtenerPDO();
    }

    public function estadoResultados($inicio, $fin) {
        $sql = "
            SELECT 
                c.clase,
                SUM(dd.debito) AS total_debito,
                SUM(dd.credito) AS total_credito
            FROM diario_detalle dd
            INNER JOIN diario d ON dd.diario_id = d.id
            INNER JOIN cuentas c ON dd.cuenta_id = c.id
            WHERE d.fecha BETWEEN :inicio AND :fin
            AND d.activo = 1
            AND c.activo = 1
            AND c.clase IN (4,5)
            GROUP BY c.clase
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':inicio' => $inicio,
            ':fin' => $fin
        ]);

        $datos = $stmt->fetchAll();

        $ingresos = 0;
        $gastos = 0;

        foreach ($datos as $fila) {
            if ($fila['clase'] == 4) {
                $ingresos += $fila['total_credito'] - $fila['total_debito'];
            }

            if ($fila['clase'] == 5) {
                $gastos += $fila['total_debito'] - $fila['total_credito'];
            }
        }

        return [
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'utilidad_neta' => $ingresos - $gastos
        ];
    }

    public function balanceGeneral($fin) {
        $sql = "
            SELECT 
                c.clase,
                SUM(dd.debito) AS total_debito,
                SUM(dd.credito) AS total_credito
            FROM diario_detalle dd
            INNER JOIN diario d ON dd.diario_id = d.id
            INNER JOIN cuentas c ON dd.cuenta_id = c.id
            WHERE d.fecha <= :fin
            AND d.activo = 1
            AND c.activo = 1
            AND c.clase IN (1,2,3)
            GROUP BY c.clase
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':fin' => $fin
        ]);

        $datos = $stmt->fetchAll();

        $activo = 0;
        $pasivo = 0;
        $patrimonio = 0;

        foreach ($datos as $fila) {
            if ($fila['clase'] == 1) {
                $activo += $fila['total_debito'] - $fila['total_credito'];
            }

            if ($fila['clase'] == 2) {
                $pasivo += $fila['total_credito'] - $fila['total_debito'];
            }

            if ($fila['clase'] == 3) {
                $patrimonio += $fila['total_credito'] - $fila['total_debito'];
            }
        }

        return [
            'activo' => $activo,
            'pasivo' => $pasivo,
            'patrimonio' => $patrimonio,
            'cuadra' => round($activo, 2) == round($pasivo + $patrimonio, 2)
        ];
    }

    public function generarContenidoHash($tipo, $inicio, $fin) {
        if ($tipo === 'estado_resultados') {
            $datos = $this->estadoResultados($inicio, $fin);
        } else {
            $datos = $this->balanceGeneral($fin);
        }

        return json_encode([
            'tipo' => $tipo,
            'inicio' => $inicio,
            'fin' => $fin,
            'datos' => $datos
        ], JSON_UNESCAPED_UNICODE);
    }
}