<?php  

require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recinto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        #zonasContainer {
            margin-top: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin: 0% auto;
            width: 40%;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
        }

        label {
            font-size: 14px;
            color: #555;
        }

        input[type="text"], select, input[type="number"] {
            width: 100%;
            padding: 10px 0px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .flex-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .flex-container div {
            flex: 1;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        .zona {
            margin-bottom: 20px;
        }

        .zona button {
            margin-top: 10px;
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .zona button:hover {
            background-color: #c82333;
        }

        #checkboxContainer {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        #checkboxContainer label {
            margin-right: 5px;
        }

    </style>
</head>
<body>

<h2>Gestión de Recinto</h2>
<button id="openModalBtn">Agregar Zona</button>
<button id="obtenerDatosBtn">Obtener Datos de Asientos</button>
<button id="addSeatBtn">Agregar Tipo de Boleto</button>
<div id="addSeatModal" style="display:none;">
    <form id="addSeatForm">
        <div>
            <label for="recinto">Recinto:</label>
            <select class="recinto_id" id="recinto1" required>
                <option value="">Seleccione un recinto</option>
                <?php foreach ($recintos as $recinto): ?>
                    <option value="<?php echo htmlspecialchars($recinto['_id']); ?>">
                        <?php echo htmlspecialchars($recinto['nombre']); ?> - Ubicación: <?php echo htmlspecialchars($recinto['ciudad']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="funcion">Función:</label>
            <select class="funcion_id" id="funcion1" required>
                    <option value="">Seleccione una función</option>
                <!-- Aquí se agregarán las funciones dinámicamente según el recinto seleccionado -->
            </select>
        </div>

        <!-- Contenedor para los tipos de asientos agregados -->
        <div id="seatContainer">
            <div class="seat-type-block">
                <label for="tipoAsiento">Tipo de Asiento:</label>
                <select class="tipo_asiento_id_p" required>
                    <option value="">Seleccione un tipo de asiento</option>
                    <?php foreach ($tipos as $tipoAsiento): ?>
                        <option value="<?php echo htmlspecialchars($tipoAsiento['_id']); ?>">
                            <?php echo htmlspecialchars($tipoAsiento['nombre']); ?> - Precio: <?php echo htmlspecialchars($tipoAsiento['precio']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class=zona2></div>

                <label for="cantidad">Cantidad de Asientos:</label>
                <input type="number" class="cantidad_asientos" min="1" required placeholder="Cantidad de boletos">
            </div>
        </div>
        <!-- Botón para agregar un nuevo tipo de boleto -->
        <button type="button" id="addSeatTypeBtn">Agregar Cantidad de Boleto</button>

        <div>
            <button type="submit">Guardar Boletos</button>
            <button type="button" id="closeModalBtn2">Cerrar</button>
        </div>
    </form>
</div>



<!-- Contenedor de la tabla generada -->
<div id="zonasContainer"></div>

<!-- Modal para agregar una nueva zona -->
<div id="zonaModal" class="modal">
    <div class="modal-content">
        <h2>Agregar Nueva Zona</h2>
         <div class="flex-container">
            <div>
                <label for="tipoAsiento">Tipo de Asiento:</label>
                <select class="tipo_asiento_id" id="tipoAsiento_s" required>
                    <option value="">Seleccione un tipo de asiento</option>
                    <?php foreach ($tipos as $tipoAsiento): ?>
                        <option value="<?php echo htmlspecialchars($tipoAsiento['_id']); ?>">
                            <?php echo htmlspecialchars($tipoAsiento['nombre']); ?> - Precio: <?php echo htmlspecialchars($tipoAsiento['precio']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="recinto">Recinto:</label>
                <select class="recinto_id" id="recinto2" required>
                    <option value="">Seleccione un recinto</option>
                    <?php foreach ($recintos as $recinto): ?>
                        <option value="<?php echo htmlspecialchars($recinto['_id']); ?>">
                            <?php echo htmlspecialchars($recinto['nombre']); ?> - Ubicacin: <?php echo htmlspecialchars($recinto['ciudad']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="funcion">Función:</label>
            <select class="funcion_id" id="funcion2" required>
                <option value="">Seleccione una función</option>
                <!-- Aquí se agregarán las funciones dinámicamente -->
            </select>
        </div>

        <label for="nombreZona">Nombre de la Zona:</label>
        <div id="zonas"></div>

        <label for="filasZona">Selecciona letras de filas (A-Z):</label><br>
        <div id="checkboxContainer"></div>
        

         <div class="flex-container">
             <div>
                <label for="asientosInicio">Asientos desde:</label>
                <input type="number" id="asientosInicio" min="1" max="30">
             </div>
             <div>
                <label for="asientosFin">hasta:</label>
                <input type="number" id="asientosFin" min="1" max="30">
             </div>
         </div>
        <button id="agregarZonaBtn">Aceptar</button>
        <button id="closeModalBtn">Cerrar</button>
    </div>
</div>
</body>
</html>