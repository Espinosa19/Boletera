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