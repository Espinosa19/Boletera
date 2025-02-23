<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="./assets/css/for_to.css">
   
</head>
<body>

    <div class="form-container">
        <h2>Ingresar Token</h2>
        <form id="tokenForm"  method="POST">
            <div class="input-group">
                <label for="token">Token</label>
                <input type="text" id="token" name="token" placeholder="Ingrese su token">
                <div id="tokenError" class="error">El token es obligatorio.</div>
            </div>
            <button type="submit" class="submit-btn">Enviar</button>
        </form>
    </div>
<script src="assets/js/verificar_t.js"></script>
</body>
</html>
