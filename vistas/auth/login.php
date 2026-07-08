 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Sistema Contable</title>
    <link rel="stylesheet" href="../assets/css/login/estilos.css">
</head>
<body class="pagina-login fondo-particulas">

    <canvas id="particles-canvas"></canvas>

    <div class="caja-login">
        <h1>Sistema Contable</h1>
        <p class="subtitulo">Inicia sesión para continuar</p>

        <?php if (!empty($error)): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="AuthController.php">
            <label for="correo">Correo</label>
            <input type="email" id="correo" name="correo" required autofocus>

            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <button type="submit">Entrar</button>
        </form>

        <a class="enlace-volver" href="../vistas/publico/landing.php">Volver al inicio</a>
    </div>

    <script src="../assets/css/login/particulas1.js"></script>
    <script src="../assets/js/Validaciones.js"></script>
</body>
</html> 
