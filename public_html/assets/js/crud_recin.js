document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('recinto-form');
    const tableContainer = document.getElementById('recintos-container');
    const addButton = document.getElementById('add-recinto');
    const cancelButton = document.getElementById('cancel');
    const zonasContainer = document.getElementById('zonas-container');
    const agregarZonaBtn = document.getElementById("agregarZona");
    let editingId = null;
    let formSubmitting = false;
    let zonaCounter = 1;

    addButton.addEventListener('click', function () {
        form.reset();
        editingId = null;
        form.style.display = 'block';
        tableContainer.style.display = 'none';
        addButton.style.display = 'none';
    });

    cancelButton.addEventListener('click', function () {
        form.style.display = 'none';
        tableContainer.style.display = 'block';
        addButton.style.display = 'inline-block';
    });

    agregarZonaBtn.addEventListener("click", () => {
        zonaCounter++;
        const zonaDiv = document.createElement('div');
        zonaDiv.classList.add('zona');
        zonaDiv.innerHTML = `
            <label>Nombre de la Zona:</label>
            <input type="text" name="nombre_zona[]" required><br>

            <label>Tipo:</label>
            <select name="tipo[]" required>
                <option value="">Selecciona un tipo</option>
                <option value="Asiento">Asiento</option>
                <option value="Pie">Pie</option>
            </select><br>

            <label>Capacidad:</label>
            <input type="number" name="capacidad_zona[]" required><br>

            <label>Precio Default:</label>
            <input type="number" name="precio_default[]" required><br>

            <label>Descripción:</label>
            <textarea name="descripcion[]" required></textarea><br>

            <input type="hidden" name="id_zona[]" value="">
        `;
        zonasContainer.appendChild(zonaDiv);
    });

    document.getElementById('mapa_svg_url').addEventListener('input', function () {
        document.getElementById('mapa_svg_file').disabled = this.value.trim() !== '';
    });

    document.getElementById('mapa_svg_file').addEventListener('change', function () {
        document.getElementById('mapa_svg_url').disabled = this.files.length > 0;
    });

    // Manejar la accin de guardar (crear o actualizar)
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (formSubmitting) return;
        formSubmitting = true;

        const formData = {
            id: editingId || null,
            nombre: form.nombre.value.trim(),
            ciudad: form.ciudad.value.trim(),
            estado: form.estado.value.trim(),
            capacidad: parseInt(form.capacidad.value.trim(), 10),
            activo: form.activo.checked,
            mapa_svg_url: form.mapa_svg_url.value.trim(),
            mapa_svg_data: null,
            zonas: []
        };

        if (!formData.mapa_svg_url) {
            const svgFile = form.mapa_svg_file.files[0];
            if (svgFile) {
                formData.mapa_svg_data = await convertFileToBase64(svgFile);
            }
        }

        document.querySelectorAll('.zona').forEach(zonaDiv => {
            formData.zonas.push({
                nombre_zona: zonaDiv.querySelector('[name="nombre_zona[]"]').value.trim(),
                tipo: zonaDiv.querySelector('[name="tipo[]"]').value,
                capacidad: parseInt(zonaDiv.querySelector('[name="capacidad_zona[]"]').value.trim(), 10),
                precio_default: parseFloat(zonaDiv.querySelector('[name="precio_default[]"]').value.trim()),
                descripcion: zonaDiv.querySelector('[name="descripcion[]"]').value.trim()
            });
        });

        try {
            const response = await fetch('./apis/apir.php' + (editingId ? `?id=${editingId}` : ''), {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
                credentials: "same-origin"
            });
            if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
            await response.json();
            loadRecintos();
            resetForm();
        } catch (error) {
            console.error('Error:', error);
        } finally {
            formSubmitting = false;
        }
    });


// Función para convertir el archivo a Base64
function convertFileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result.split(',')[1]); // Solo la parte base64
        reader.onerror = error => reject(error);
        reader.readAsDataURL(file);
    });
}


    const estadoSelect = document.getElementById('estado');
    const ciudadSelect = document.getElementById('ciudad');

    // Objeto con todos los estados de México y sus ciudades
    const ciudadesPorEstado = {
        'Aguascalientes': ['Aguascalientes', 'Jesús María', 'Calvillo'],
        'Baja California': ['Mexicali', 'Tijuana', 'Ensenada', 'Tecate', 'Rosarito'],
        'Baja California Sur': ['La Paz', 'Los Cabos', 'Comondú', 'Loreto', 'Mulegé'],
        'Campeche': ['Campeche', 'Ciudad del Carmen', 'Champotón'],
        'Chiapas': ['Tuxtla Gutirrez', 'San Cristóbal de las Casas', 'Tapachula'],
        'Chihuahua': ['Chihuahua', 'Ciudad Jurez', 'Delicias', 'Hidalgo del Parral'],
        'Coahuila': ['Saltillo', 'Torreón', 'Monclova', 'Piedras Negras'],
        'Colima': ['Colima', 'Manzanillo', 'Tecomán', 'Villa de Álvarez'],
        'Ciudad de México': ['Coyoacán', 'Iztapalapa', 'Cuauhtémoc', 'Tlalpan', 'Álvaro Obregón'],
        'Durango': ['Durango', 'Gómez Palacio', 'Lerdo', 'Santiago Papasquiaro'],
        'Guanajuato': ['Len', 'Guanajuato', 'Irapuato', 'Celaya', 'Salamanca'],
        'Guerrero': ['Acapulco', 'Chilpancingo', 'Zihuatanejo', 'Iguala'],
        'Hidalgo': ['Pachuca', 'Tulancingo', 'Tula de Allende', 'Tepeji del Río'],
        'Jalisco': ['Guadalajara', 'Zapopan', 'Puerto Vallarta', 'Tlaquepaque', 'Tonalá'],
        'México': ['Toluca', 'Naucalpan', 'Ecatepec', 'Tlalnepantla', 'Nezahualcóyotl'],
        'Michoacán': ['Morelia', 'Uruapan', 'Zamora', 'Lázaro Cárdenas', 'Pátzcuaro'],
        'Morelos': ['Cuernavaca', 'Jiutepec', 'Cuautla', 'Yautepec'],
        'Nayarit': ['Tepic', 'Bahía de Banderas', 'Xalisco', 'Compostela'],
        'Nuevo León': ['Monterrey', 'San Pedro Garza García', 'Guadalupe', 'Apodaca', 'San Nicols de los Garza'],
        'Oaxaca': ['Oaxaca de Juárez', 'Salina Cruz', 'Juchitán', 'Huajuapan de León'],
        'Puebla': ['Puebla', 'Tehuacán', 'Atlixco', 'Cholula'],
        'Quertaro': ['Querétaro', 'San Juan del Río', 'El Marqués', 'Corregidora'],
        'Quintana Roo': ['Cancún', 'Playa del Carmen', 'Chetumal', 'Cozumel'],
        'San Luis Potosí': ['San Luis Potosí', 'Soledad de Graciano Sánchez', 'Matehuala', 'Ciudad Valles'],
        'Sinaloa': ['Culiacán', 'Mazatlán', 'Los Mochis', 'Guasave'],
        'Sonora': ['Hermosillo', 'Ciudad Obregón', 'Nogales', 'Guaymas'],
        'Tabasco': ['Villahermosa', 'Comalcalco', 'Cárdenas', 'Paraso'],
        'Tamaulipas': ['Reynosa', 'Matamoros', 'Nuevo Laredo', 'Ciudad Victoria'],
        'Tlaxcala': ['Tlaxcala', 'Apizaco', 'Huamantla', 'Chiautempan'],
        'Veracruz': ['Veracruz', 'Xalapa', 'Coatzacoalcos', 'Poza Rica', 'Córdoba'],
        'Yucatán': ['Mérida', 'Valladolid', 'Tizimín', 'Progreso'],
        'Zacatecas': ['Zacatecas', 'Guadalupe', 'Fresnillo', 'Jerez']
    };

    // Poblar el select de estados
    for (const estado in ciudadesPorEstado) {
        const option = document.createElement('option');
        option.value = estado;
        option.textContent = estado;
        estadoSelect.appendChild(option);
    }

    // Manejar el cambio del select de estado
    estadoSelect.addEventListener('change', function () {
        const estadoSeleccionado = estadoSelect.value;

        // Limpiar el select de ciudades
        ciudadSelect.innerHTML = '<option value="">Selecciona una ciudad</option>';

        // Si se selecciona un estado, agregar las ciudades correspondientes
        if (ciudadesPorEstado[estadoSeleccionado]) {
            ciudadesPorEstado[estadoSeleccionado].forEach(function (ciudad) {
                const option = document.createElement('option');
                option.value = ciudad;
                option.textContent = ciudad;
                ciudadSelect.appendChild(option);
            });
        }
    });

    // Cargar todos los recintos
    function loadRecintos() {
        fetch('./apis/apir.php',{
            method: "GET",
            headers: {
                'Content-Type': 'application/json'
            },
        })
            .then(response => response.json())
            .then(data => {
                console.log(data)
               displayRecintos(data);
            });
   }
   const editButtons = document.querySelectorAll('.edit-button');
   editButtons.forEach(button => {
       button.addEventListener('click', function () {
           editRecinto(button.dataset.id); // Pasar ID al editor
       });
   });
   const deleteButtons = document.querySelectorAll('.delete-button');
   deleteButtons.forEach(button => {
       button.addEventListener('click', function () {
           deleteRecinto(button.dataset.id); // Pasar ID al eliminador
       });
   });
    // Mostrar recintos en la tabla
    function displayRecintos(recintos) {
        const tableBody = document.getElementById('recintos-table-body');
        tableBody.innerHTML = ''; // Limpiar contenido previo
        recintos.forEach(recinto => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${recinto.nombre}</td>
                <td>${recinto.ciudad}</td>
                <td>${recinto.estado}</td>
                <td>${recinto.capacidad}</td>
                <td>${recinto.activo ? 'Sí' : 'No'}</td>
                <td>
                    <button class="edit-button" data-id="${recinto._id.$oid}">Editar</button>
                    <button class="delete-button" data-id="${recinto._id.$oid}">Eliminar</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
        // Asignar eventos a los botones de editar y eliminar
        const editButtons = document.querySelectorAll('.edit-button');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                editRecinto(button.dataset.id); // Pasar ID al editor
            });
        });
        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                deleteRecinto(button.dataset.id); // Pasar ID al eliminador
            });
        });
    }
function editRecinto(id) {
    console.log(id)
    fetch(`./apis/apir.php`,{
        method:"POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ id })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener los datos del recinto.');
            }
            return response.json();
        })
        .then(data => {

// Verificar si el arreglo tiene elementos
            if (data && typeof data === 'object') {
                const recinto = data; // Acceder al objeto directamente
            
                // Llenar el formulario con la información del recinto
                document.getElementById('nombre').value = recinto.nombre;
                document.getElementById('estado').value = recinto.estado; // Primero establecer el estado
            
                // Limpiar el select de ciudades
                ciudadSelect.innerHTML = '<option value="">Selecciona una ciudad</option>';
            
                // Llenar las ciudades según el estado seleccionado
                if (ciudadesPorEstado[recinto.estado]) {
                    ciudadesPorEstado[recinto.estado].forEach(function (ciudad) {
                        const option = document.createElement('option');
                        option.value = ciudad;
                        option.textContent = ciudad;
                        ciudadSelect.appendChild(option);
                    });
                }
            
                // Establecer la ciudad seleccionada
                ciudadSelect.value = recinto.ciudad; // Luego establecer la ciudad
            
                document.getElementById('capacidad').value = recinto.capacidad;
                document.getElementById('activo').checked = recinto.activo;
            
                // Mostrar el formulario y ocultar la tabla
                form.style.display = 'block';
                tableContainer.style.display = 'none';
                editingId = id; // Establecer el ID del recinto en edición
                addButton.style.display = 'none';
            
                // Limpiar cualquier zona existente antes de agregar nuevas
                clearZonesFromForm();
            
                // Llenar las zonas (si existen)
                if (recinto.zonas && recinto.zonas.length > 0) {
                    recinto.zonas.forEach((zona, index) => {
                        // Llama a la función para agregar la zona al formulario
                        addZoneToForm(zona, index);
                    });
                }
            } else {
                console.error('No se encontró el recinto con ID:', id);
            }
        })
        .catch(error => {
            console.error('Ha ocurrido un error:', error);
            alert('Ocurrió un error al intentar cargar los datos. Por favor, inténtalo de nuevo más tarde.');
        });
}
function clearZonesFromForm() {
    const zoneContainer = document.getElementById('zonas-container'); // Asume que tienes un contenedor para las zonas
    zoneContainer.innerHTML = ''; // Limpiar el contenedor de zonas
}

// Función para agregar una zona al formulario
function addZoneToForm(zona, index) {
    const zoneContainer = document.getElementById('zonas-container'); // Asume que tienes un contenedor para las zonas

    // Crear un nuevo div para la zona
    const zoneDiv = document.createElement('div');
    zoneDiv.className = 'zona';

    // Crear los campos para la zona
    zoneDiv.innerHTML = `
        <h4>Zona ${index + 1}</h4>
        <label for="nombre_zona">Nombre de Zona:</label>
        <input type="text" id="nombre_zona" name="nombre_zona[]" value="${zona.nombre_zona}" required>

        <label for="descripcion">Descripción:</label>
        <input type="text" id="descripcion" name="descripcion[]" value="${zona.descripcion}" required>

        <label for="capacidad">Capacidad:</label>
        <input type="number" id="capacidad_zona" name="capacidad_zona[]" value="${zona.capacidad}" required>

        <label for="tipo">Tipo:</label>
        <input type="text" id="tipo" name="tipo[]" value="${zona.tipo}" required>

        <label for="precio_default">Precio Default:</label>
        <input type="number" id="precio_default" name="precio_default[]" value="${zona.precio_default}" required>
    `;

    // Agregar el nuevo div al contenedor de zonas
    zoneContainer.appendChild(zoneDiv);
}
    // Eliminar recinto
    function deleteRecinto(id) {
        if (confirm('Ests seguro de que deseas eliminar este recinto?')) {
            fetch(`./apis/apir.php`, {
                method: 'DELETE',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ id })

            }).then(response => response.json())
            .then(data => {
                console.log(data);
                loadRecintos(); // Recargar la lista de recintos
            });
        }
    }

    // Validar formulario
    function validateForm(data) {
        const errors = [];
        if (!data.nombre) errors.push('El nombre es obligatorio.');
        if (!data.ciudad) errors.push('La ciudad es obligatoria.');
        if (!data.estado) errors.push('El estado es obligatorio.');
        if (!data.capacidad || isNaN(data.capacidad) || data.capacidad <= 0) errors.push('La capacidad debe ser un número positivo.');
        return errors;
    }

    // Mostrar errores de validación
    function displayValidationErrors(errors) {
        const errorList = document.getElementById('validation-errors');
        errorList.innerHTML = ''; // Limpiar errores previos
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
        errorList.style.display = 'block'; // Mostrar lista de errores
    }

    // Reiniciar el formulario
    function resetForm() {
        form.reset();
        editingId = null; // Reiniciar el ID
        form.style.display = 'none'; // Ocultar formulario
        tableContainer.style.display = 'block'; // Mostrar tabla
        addButton.style.display = 'inline-block'; // Mostrar botón agregar
        const errorList = document.getElementById('validation-errors');
        errorList.innerHTML = ''; // Limpiar errores
        errorList.style.display = 'none'; // Ocultar lista de errores
    }
});
