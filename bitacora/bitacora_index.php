<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../config/config.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ' . BASE_URL . '/vistas/auth/login.php');
    exit;
}

// Solo el Administrador debería ver la bitácora completa del sistema.
if (!Sesion::tieneAcceso('bitacora')) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

try {
    $pdo = Conexion::obtenerInstancia()->obtenerPDO();

    $sql = "
    SELECT
        b.id,
        b.fecha,
        b.accion,
        b.tabla_afectada,
        b.registro_id,
        b.detalle,
        b.ip_address,
        u.nombre AS usuario
    FROM bitacora b
    LEFT JOIN usuarios u
        ON b.usuario_id = u.id
    ORDER BY b.fecha DESC
    ";

    $bitacora = $pdo->query($sql)->fetchAll();

} catch (PDOException $e) {
    // Nota: en producción es mejor loguear el error y mostrar un mensaje genérico,
    // en vez de exponer el mensaje real de la BD al usuario.
    die("Error al consultar la bitácora: " . $e->getMessage());
}

// Helper local para no repetir htmlspecialchars(..., ENT_QUOTES, 'UTF-8') en cada celda.
function h(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper para pintar un badge según el tipo de acción (mejora visual opcional).
function claseBadgeAccion(string $accion): string
{
    $accion = mb_strtolower($accion);

    return match (true) {
        str_contains($accion, 'elimin') => 'bg-danger',
        str_contains($accion, 'crea')   => 'bg-success',
        str_contains($accion, 'actualiz'), str_contains($accion, 'edit') => 'bg-warning',
        default => 'bg-info',
    };
}

require_once '../vistas/layout/header.php';
require_once '../vistas/layout/sidebar.php';
?>

<!--
    Este bloque de estilos es SOLO para esta vista. Fuerza el layout
    flex necesario (contenido -> container -> card -> tabla-scroll)
    para que el scroll quede contenido dentro de la tabla, sin tener
    que tocar barra.css / estilo.css / header.css globales.
-->
<style>
    /* .contenido lo define barra.css con flex:1 y padding, pero sin
       overflow controlado. Aquí, SOLO en esta página, lo convertimos
       en columna flex que no scrollea por sí sola. */
    .contenido {
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    .contenido .container.bitacora-wrap {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 100%;
    }

    .bitacora-wrap .card {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }

    .bitacora-wrap .card .tabla-scroll {
        flex: 1;
        min-height: 0;
        max-height: none;   /* ya no depende de un 65vh fijo */
        overflow-y: auto;
    }

    /* El thead de Bootstrap (table-dark) le gana en especificidad a
       .table th; lo neutralizamos aquí para que se vea tu azul marino. */
    .bitacora-wrap thead.table-dark th {
        background: var(--color-primario, #102A43) !important;
        color: #fff !important;
        border-color: var(--color-primario, #102A43) !important;
    }
</style>

<div class="container mt-4 bitacora-wrap">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bitácora del Sistema</h2>
        <span class="text-muted" style="color: var(--color-texto-suave); font-size: 0.9rem;">
            <?= count($bitacora) ?> registro<?= count($bitacora) === 1 ? '' : 's' ?>
        </span>
    </div>

    <div class="card">
        <div class="tabla-scroll">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Tabla</th>
                        <th>Registro</th>
                        <th>Detalle</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bitacora) > 0): ?>
                        <?php foreach ($bitacora as $fila): ?>
                            <tr>
                                <td><?= h($fila['fecha']) ?></td>
                                <td><?= h($fila['usuario'] ?? 'Sistema') ?></td>
                                <td>
                                    <span class="badge <?= claseBadgeAccion($fila['accion']) ?>">
                                        <?= h($fila['accion']) ?>
                                    </span>
                                </td>
                                <td><?= h($fila['tabla_afectada'] ?? '') ?></td>
                                <td><?= h((string) ($fila['registro_id'] ?? '')) ?></td>
                                <td><?= h($fila['detalle']) ?></td>
                                <td><?= h($fila['ip_address'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No existen registros en la bitácora.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once '../vistas/layout/footer.php'; ?>