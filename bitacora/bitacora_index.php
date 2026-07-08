<?php

require_once '../config/Conexion.php';

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

$sql = "
SELECT
    b.*,
    u.nombre AS usuario
FROM bitacora b
LEFT JOIN usuarios u
    ON b.usuario_id = u.id
ORDER BY b.fecha DESC
";

$stmt = $pdo->query($sql);

$registros = $stmt->fetchAll();
?>

<?php require_once '../vistas/layout/header.php'; ?>
<?php require_once '../vistas/layout/sidebar.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Bitácora del Sistema</h2>

        <a href="../index.php" class="btn btn-secondary">
            Volver
        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">

                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Tabla</th>
                            <th>Registro</th>
                            <th>Detalle</th>
                            <th>IP</th>
                            <th>Fecha</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if(count($registros) > 0): ?>

                        <?php foreach($registros as $fila): ?>

                        <tr>

                            <td>
                                <?= $fila['id'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['usuario'] ?? 'Sistema') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['accion']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['tabla_afectada']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['registro_id']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['detalle']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['ip_address']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($fila['fecha']) ?>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="8" class="text-center">

                                No existen registros en la bitácora.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php require_once '../vistas/layout/footer.php'; ?>