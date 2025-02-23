<?php
    
require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recintos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Recintos</h1>

        
        <button id="add-recinto">Agregar Recinto</button>

        <div id="recintos-container">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Ciudad</th>
                <th>Estado</th>
                <th>Capacidad</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="recintos-table-body">      
        <?php if(empty($recintos)): ?>
    <tr>
        <td colspan="6">No hay recintos registrados.</td>
    </tr>
<?php else: ?>
    <?php foreach($recintos as $recinto): ?>
        <tr>
            <td><?php echo htmlspecialchars($recinto['nombre']); ?></td>
            <td><?php echo htmlspecialchars($recinto['ciudad']); ?></td>
            <td><?php echo htmlspecialchars($recinto['estado']); ?></td>
            <td><?php echo htmlspecialchars($recinto['capacidad']); ?></td>
            <td><?php echo htmlspecialchars($recinto['activo'] ? "Sí" : "No"); ?></td>
            <td>
            <button class="edit-button" data-id="<?php echo $recinto['_id'];?>">Editar</button>
            <button class="delete-button" data-id="<?php echo $recinto['_id'];?>">Eliminar</button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>

</tbody>

    </table>
</div>
<form id="recinto-form" style="display: none;">
    <!-- Información del Recinto -->
    <h2>Información del Recinto</h2>
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required><br>

    <label for="estado">Estado:</label>
    <select id="estado" name="estado" required>
        <option value="">Selecciona un estado</option>
        <!-- Lista de todos los estados -->
    </select><br>

    <label for="ciudad">Ciudad:</label>
    <select id="ciudad" name="ciudad" required>
        <option value="">Selecciona una ciudad</option>
        <!-- Ciudades dinámicas aparecerán aquí -->
    </select><br>

    <label for="capacidad">Capacidad:</label>
    <input type="number" id="capacidad" name="capacidad" required><br>

    <label for="activo">Activo:</label>
    <input type="checkbox" id="activo" name="activo"><br>

    <label for="mapa_svg_url">URL del Mapa SVG:</label>
    <input type="url" id="mapa_svg_url" name="mapa_svg_url" placeholder="https://ejemplo.com/mapa.svg" required><br>

    <label for="mapa_svg_file">O carga el Mapa SVG:</label>
    <input type="file" id="mapa_svg_file" name="mapa_svg_file" accept=".svg"><br>

    <h2>Zonas del Recinto</h2>
    <div id="zonas-container">
        <div class="zona">
            <label for="nombre_zona_1">Nombre de la Zona:</label>
            <input type="text" id="nombre_zona_1" name="nombre_zona[]" required><br>

            <label for="tipo_1">Tipo:</label>
            <select id="tipo_1" name="tipo[]" required>
                <option value="">Selecciona un tipo</option>
                <option value="Asiento">Asiento</option>
                <option value="Pie">Pie</option>
            </select><br>

            <label for="capacidad_zona_1">Capacidad:</label>
            <input type="number" id="capacidad_zona_1" name="capacidad_zona[]" required><br>

            <label for="precio_default_1">Precio Default:</label>
            <input type="number" id="precio_default_1" name="precio_default[]" required><br>

            <label for="descripcion_1">Descripción:</label>
            <textarea id="descripcion_1" name="descripcion[]" required></textarea><br>

            <!-- Campo oculto para almacenar el ID de la zona si es necesario -->
            <input type="hidden" name="id_zona[]" value="">
        </div>
    </div>

    <!-- Botones para gestionar zonas -->
    <button type="button" id="agregarZona">Agregar Zona</button>
    <button type="submit" id="submit-button">Guardar</button>
    <button type="button" id="cancel">Cancelar</button>
</form>



    <script src="../assets/js/crud_recintos.js"></script>
</body>
</html>

<style>
 body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
        }

        /* Estilos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        /* Estilos para el formulario */
        #recintoForm {
            display: none; /* Oculta el formulario inicialmente */
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }
        select{
            width:100%;
            height:35px;
        }

        input[type="text"],
        input[type="number"],
        input[type="checkbox"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        button:hover {
            background-color: #45a049;
        }

        #cancelButton {
            background-color: #f44336; /* Color rojo para el botón de cancelar */
        }

        #cancelButton:hover {
            background-color: #d32f2f; /* Color rojo más oscuro al pasar el mouse */
        }
/* Media queries para dispositivos mviles */
@media (max-width: 768px) {
    .container {
        width: 95%;
    }

    form {
        flex-direction: column;
    }

    table, thead, tbody, th, td, tr {
        display: block;
    }

    table thead {
        display: none;
    }

    table tr {
        margin-bottom: 15px;
    }

    table td {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        border: none;
        border-bottom: 1px solid #ddd;
    }

    table td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #333;
    }
}

</style>
