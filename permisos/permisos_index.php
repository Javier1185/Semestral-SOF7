<?php

require_once '../config/Conexion.php';
require_once '../config/Sesion.php';
require_once '../config/config.php';

Sesion::iniciar();

if (!Sesion::estaLogueado()) {
    header('Location: ' . BASE_URL . '/vistas/auth/login.php');
    exit;
}

$pdo = Conexion::obtenerInstancia()->obtenerPDO();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $rolId = (int)$_POST['rol_id'];

    $pdo->prepare("DELETE FROM permisos WHERE rol_id = ?")->execute([$rolId]);

    $modulos = ['usuarios','roles','cuentas','diario','informes'];

    foreach($modulos as $modulo){

        $ver = isset($_POST['ver'][$modulo]) ? 1 : 0;
        $editar = isset($_POST['editar'][$modulo]) ? 1 : 0;

        $stmt = $pdo->prepare("
            INSERT INTO permisos
            (
                rol_id,
                modulo,
                ver,
                editar
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $rolId,
            $modulo,
            $ver,
            $editar
        ]);
    }

    echo "<script>alert('Permisos actualizados correctamente');</script>";
}

$roles = $pdo->query("SELECT * FROM roles ORDER BY nombre")->fetchAll();

$rolSeleccionado = $_GET['rol'] ?? ($roles[0]['id'] ?? 1);

$stmt = $pdo->prepare("SELECT * FROM permisos WHERE rol_id=?");
$stmt->execute([$rolSeleccionado]);

$permisos = [];

foreach($stmt->fetchAll() as $fila){
    $permisos[$fila['modulo']] = $fila;
}

$modulos = ['usuarios','roles','cuentas','diario','informes'];

include '../vistas/layout/header.php';
include '../vistas/layout/sidebar.php';

?>

<h2>Roles y Permisos</h2>

<form method="GET">

<label>Rol</label>

<select
name="rol"
onchange="this.form.submit()">

<?php foreach($roles as $rol): ?>

<option
value="<?= $rol['id'] ?>"
<?= $rolSeleccionado==$rol['id']?'selected':'' ?>>

<?= htmlspecialchars($rol['nombre']) ?>

</option>

<?php endforeach; ?>

</select>

</form>

<br>

<form method="POST">

<input
type="hidden"
name="rol_id"
value="<?= $rolSeleccionado ?>">

<table style="width:100%;border-collapse:collapse;background:white;">

<thead style="background:var(--color-primario);color:white;">

<tr>

<th style="padding:10px;">Módulo</th>

<th>Ver</th>

<th>Editar</th>

</tr>

</thead>

<tbody>

<?php foreach($modulos as $modulo): ?>

<tr>

<td style="padding:10px;">

<?= ucfirst($modulo) ?>

</td>

<td align="center">

<input
type="checkbox"
name="ver[<?= $modulo ?>]"
<?= (!empty($permisos[$modulo]['ver']))?'checked':'' ?>>

</td>

<td align="center">

<input
type="checkbox"
name="editar[<?= $modulo ?>]"
<?= (!empty($permisos[$modulo]['editar']))?'checked':'' ?>>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<br>

<button class="boton-login">

Guardar Permisos

</button>

</form>

</main>

<?php
include '../vistas/layout/footer.php';
?>