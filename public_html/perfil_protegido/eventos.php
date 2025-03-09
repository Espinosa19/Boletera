<?php  
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="../assets/css/eventos.css">
</head>
<body>
    <div class="container">
        <h1>Eventos</h1>
        <button id="btn-agregar" onclick="mostrarFormularioCrear()">Agregar Evento</button>
        
        <div id="formulario" class="formulario" hidden style="display:none">
            <h2 id="titulo-formulario">Agregar Evento</h2>
            <form method="POST" enctype="multipart/form-data" onsubmit="guardarEvento(event)"> 
                <label>Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                <select name="categoria" id="categoria">
                    <optgroup label="🎤 Conciertos y Música">
                        <option value="pop">Pop</option>
                        <option value="rock">Rock</option>
                        <option value="electronica">Electrónica</option>
                        <option value="regueton">Reguetón</option>
                        <option value="musica_clasica">Música Clásica</option>
                        <option value="jazz_blues">Jazz & Blues</option>
                        <option value="regional_mexicano">Regional Mexicano</option>
                        <option value="indie">Indie</option>
                    </optgroup>
                    <optgroup label="🎭 Teatro y Cultura">
                        <option value="obras_teatro">Obras de Teatro</option>
                        <option value="ballet">Ballet</option>
                        <option value="opera">Ópera</option>
                        <option value="standup_comedy">Stand-Up Comedy</option>
                        <option value="exposiciones_arte">Exposiciones de Arte</option>
                        <option value="festivales_culturales">Festivales Culturales</option>
                    </optgroup>
                    <optgroup label="⚽ Deportes">
                        <option value="futbol">Fútbol</option>
                        <option value="baloncesto">Baloncesto</option>
                        <option value="beisbol">Béisbol</option>
                        <option value="lucha_libre">Lucha Libre</option>
                        <option value="automovilismo">Automovilismo</option>
                        <option value="boxeo">Boxeo</option>
                        <option value="artes_marciales">Artes Marciales</option>
                    </optgroup>
                    <optgroup label="🎡 Ferias y Festivales">
                        <option value="festivales_gastronomicos">Festivales Gastronómicos</option>
                        <option value="festivales_musica">Festivales de Música</option>
                        <option value="ferias_tecnologicas">Ferias Tecnológicas</option>
                        <option value="ferias_libro">Ferias del Libro</option>
                    </optgroup>
                </select>

                <label>Descripción:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>

                <label for="recomendado">Recomendado:</label>
<input type="checkbox" name="recomendado" id="recomendado">

                <label>Subir Imagen:</label>
                <input type="file" name="imagen" accept="image/*" id="imagen">
                
                <h2>Recintos y Funciones</h2>
                <div id="recintos-container">
                    <div class="recinto-container">
                        <div class="recinto-header">
                            <label id="label-principal">Seleccione un Recinto:</label>
                            <button id="button-delete-principal" type="button" class="eliminar-recinto" onclick="eliminarRecinto(this)">Eliminar Recinto</button>
                        </div>
                        <select name="recinto_id[]" id="sele_recinto" required>
                            <option value="">Seleccione un recinto</option>
                            <?php if (!empty($recintos)): ?>
                                <?php foreach ($recintos as $recinto): ?>
                                    <option data-name="<?= htmlspecialchars($recinto['nombre']) ?>" value="<?= htmlspecialchars($recinto['_id']) ?>">
                                        <?= htmlspecialchars($recinto['nombre'] . " - " . $recinto['ciudad']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </select>

                        <h3 id="titulo-re">Funciones del Recinto</h3>
                        <div class="funciones-container">
                            <div class="funcion">
                                <div>
                                    <label>Fecha de Inicio:</label>
                                    <input type="datetime-local" name="fecha_inicio[]" required> 
                                </div>
                                <div>

                                <label>Fecha de Fin:</label>
                                <input type="datetime-local" name="fecha_fin[]" required>
                                </div>

                                <button type="button" class="eliminar-funcion" onclick="eliminarFuncion(this)">Eliminar Función</button>
                            </div>
                        </div>
                        <div class="funciones-modificar"></div>
                        <button id="primer-button" type="button" onclick="agregarFuncion2(this)">Agregar Función</button>
                    </div>
                </div>
                
                <button type="button" onclick="agregarRecinto()">Agregar Recinto</button>
                <button type="submit" class="btn">Agregar</button>
                <button type="button" onclick="cancelarFormulario()">Cancelar</button>
            </form>
        </div>
    </div>

    <div id="eventos">
    <table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Recintos</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($eventos as $evento): ?>
            <tr>
                <td><?php echo htmlspecialchars($evento['nombre']); ?></td>
                <td><?php echo htmlspecialchars($evento['descripcion']); ?></td>
                <td>
                    <?php 
                    if (!empty($evento['recintos'])) {
                        foreach ($evento['recintos'] as $recinto) {
                            echo 'Recinto: '. htmlspecialchars($recinto['nombre']) . "<br>";
                            if (!empty($recinto['funciones'])) {
                                foreach ($recinto['funciones'] as $funcion) {
                                    // Imprimir las fechas de las funciones
                                    echo "Inicio: " . htmlspecialchars($funcion['fecha_inicio']) . " - Fin: " . htmlspecialchars($funcion['fecha_fin']) . "<br>";
                                }
                            }
                        }
                    } else {
                        echo "Sin recintos";
                    }
                    ?>
                </td>
                <td>
                <button onclick="editarEvento('<?php echo (string) $evento['_id']; ?>')">Editar</button>
                <button onclick="eliminarEvento('<?php echo $evento['_id']; ?>')">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    </div>
<script src="../assets/js/proceso_event.js"></script>
</body>
</html>
