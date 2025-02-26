<?php  

require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Boletos</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 14px;
            color: #333;
        }

        input, select, button {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
            padding: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Registro de Boletos</h2>

        <form id="boletoForm">
            <label for="evento">Evento:</label>
            <select id="evento" required>
                <option value="">Seleccione un evento</option>
                <?php foreach($eventos as $evento): ?>
                <option value="<?php echo htmlspecialchars($evento['_id']); ?>">
                    <?php echo htmlspecialchars($evento['nombre']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select id="recinto" required>
                <option value="">Seleccione un recinto</option>
                
            </select>
            
            <label for="cantidad">Cantidad de Boletos:</label>
            <input type="number" id="cantidad" min="1" required>

            <select id="zona" required>
                <option value="">Seleccionar Asiento</option>
                
            </select>

            <label for="metodo">Metodo de Pago:</label>
            <select id="metodo" required>
                <option value="Efectivo">Efectivo</option>
                <option value="Tarjeta">Tarjeta</option>
            </select>

            <button type="submit">Registrar Boletos</button>
        </form>

        <div id="mensaje"></div>
    </div>
    <script src="../assets/js/crud_crear.js"></script>
</body>
</html>
