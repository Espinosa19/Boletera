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
    <link rel="stylesheet" href="../assets/css/complementos.css">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
      <link rel="stylesheet" href="../assets/css/tablas.css">

</head>
<body>
    <?php include __DIR__ . '/complementos/header.php'; ?>
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
            <input type="text" id="nombre_zona" name="nombre_zona[]" required><br>

            <label for="tipo_1">Tipo:</label>
            <select id="tipo" name="tipo[]" required>
                <option value="">Selecciona un tipo</option>
                <option value="Asiento">Asiento</option>
                <option value="Pie">Pie</option>
            </select><br>

            <label for="capacidad_zona_1">Capacidad:</label>
            <input type="number" id="capacidad_zona" name="capacidad_zona[]" required><br>

            

            <label for="descripcion_1">Descripción:</label>
            <textarea id="descripcion" name="descripcion[]" required></textarea><br>

            <!-- Campo oculto para almacenar el ID de la zona si es necesario -->
            <input type="hidden" name="id_zona[]" value="">
        </div>
    </div>

    <!-- Botones para gestionar zonas -->
    <button type="button" id="agregarZona">Agregar Zona</button>
    <button type="submit" id="submit-button">Guardar</button>
    <button type="button" id="cancel">Cancelar</button>
</form>



    <script src="../assets/js/crud_recin.js"></script>
</body>
</html>

<style>
 
        /* Estilos para el formulario */
      
    
      

</style>
