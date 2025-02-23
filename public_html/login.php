<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="./assets/css/estilos.css">

</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <form id="loginForm">
            <div class="input-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="Tu correo" required>
            </div>
            <div class="input-group">
                <label for="contra">Contraseña</label>
                <input type="password" id="contra" name="contra" placeholder="Tu contraseña" required>
            </div>
            <button type="submit" class="btn">Ingresar</button>
        </form>
        <div id="error" class="error-message"></div>
    </div>
<script src="assets/js/validacion_crede.js"></script>
</body>
</html>
