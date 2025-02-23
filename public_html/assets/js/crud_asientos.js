
document.addEventListener("DOMContentLoaded", () => {
    const openModalBtn = document.getElementById("openModalBtn");
    const zonaModal = document.getElementById("zonaModal");
    const closeModalBtn = document.getElementById("closeModalBtn");

    openModalBtn.onclick = function() {
        zonaModal.style.display = "block";
    };

    closeModalBtn.onclick = function() {
        zonaModal.style.display = "none";
    };
    document.getElementById('addSeatBtn').addEventListener('click', function() {
        document.getElementById('addSeatModal').style.display = 'block'; // Muestra el modal
    });
    
    document.getElementById('closeModalBtn2').addEventListener('click', function() {
        document.getElementById('addSeatModal').style.display = 'none'; // Cierra el modal
        });document.getElementById('addSeatTypeBtn').addEventListener('click', function() {
    const seatContainer = document.getElementById('seatContainer');
    
    const seatBlock = document.createElement('div');
    seatBlock.classList.add('seat-type-block');

    // Clonar el select de tipo de asiento
    const tipoAsientoLabel = document.createElement('label');
    tipoAsientoLabel.innerText = 'Tipo de Asiento:';
    
    const tipoAsientoSelect = document.querySelector('.tipo_asiento_id_p').cloneNode(true);
    tipoAsientoSelect.removeAttribute('id'); // Elimina el ID para evitar duplicados
    tipoAsientoSelect.classList.add('tipo_asiento_id_p');
    
    // Crear un input para la cantidad
    const cantidadLabel = document.createElement('label');
    cantidadLabel.innerText = 'Cantidad de Asientos:';
    
    const cantidadInput = document.createElement('input');
    cantidadInput.type = 'number';
    cantidadInput.min = '1';
    cantidadInput.required = true;
    cantidadInput.placeholder = 'Cantidad de boletos';
    cantidadInput.classList.add('cantidad_asientos'); // Asigna clase para capturarlo ms tarde
    
    // Botn para eliminar el bloque de asiento
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.innerText = 'Eliminar';
    removeBtn.classList.add('removeBtn');

    removeBtn.addEventListener('click', function() {
        seatContainer.removeChild(seatBlock);
    });

    // Agregar los elementos al bloque
    seatBlock.appendChild(tipoAsientoLabel);
    seatBlock.appendChild(tipoAsientoSelect);
    seatBlock.appendChild(cantidadLabel);
    seatBlock.appendChild(cantidadInput);
    seatBlock.appendChild(removeBtn);

    // Agregar el nuevo bloque al contenedor
    seatContainer.appendChild(seatBlock);
});

document.getElementById('addSeatForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar el envo tradicional del formulario

    // Capturar los valores de los elementos
    const recintoId = document.querySelector('.recinto_id').value;
    const funcionId = document.querySelector('.funcion_id').value;
    const zona=document.querySelector('.zona_nombre2').value
    // Capturar los tipos de asiento y sus cantidades
    const tiposAsientos = Array.from(document.querySelectorAll('.seat-type-block')).map(tipo => {
        const tipoAsientoId = tipo.querySelector('.tipo_asiento_id_p').value; // ID del tipo de asiento
        const cantidadAsientos = tipo.querySelector('.cantidad_asientos').value; // Cantidad de asientos
        return { tipoAsientoId, cantidadAsientos };
    });

    const data = {
        zona:zona,
        recintoId: recintoId,
        funcionId: funcionId,
        tiposAsientos: tiposAsientos // Agregando tipos de asientos y cantidades
    };

    fetch('./apis/apia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(result => {
        console.log(result); // Procesar la respuesta del servidor
    })
    .catch(error => {
        console.error('Error:', error); // Manejar errores
    });
});

    // Cerrar el modal al hacer clic en el botón
    document.getElementById('closeModalBtn').addEventListener('click', function() {
        document.getElementById('addSeatModal').style.display = 'none';
    });

    document.getElementById("recinto1").addEventListener("change", function() {
    const recintoId = this.value;
    if (recintoId) {
        fetch(`./apis/apiof.php?recinto_id=${recintoId}`)
        .then(response => response.text())
        .then(data => {
            console.log(data)
            const zonas2 = document.querySelector('.zona2');

            const selec = document.createElement('select');
            selec.classList.add('zona_nombre2');
            data.zonas.forEach(zona => {
                const option = document.createElement("option");
                option.value = zona;
                option.textContent = zona;
                selec.appendChild(option);
            });
            zonas2.appendChild(selec);
            const funcionSelect = document.getElementById("funcion1");
            funcionSelect.innerHTML = ""; 
            const defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.textContent = "Seleccione una funcin";
            funcionSelect.appendChild(defaultOption);
            data.funciones.forEach(funcion => {
                const option = document.createElement("option");
                option.value = funcion._id; // El ID de la funcin
                option.textContent = `${funcion.nombre} - ${funcion.fecha_inicio} a ${funcion.fecha_fin}`;
                funcionSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error al obtener las funciones:", error);
        });
    }
});
   document.getElementById("recinto2").addEventListener("change", function() {
    const recintoId = this.value;
    if (recintoId) {
        fetch(`./apis/apiof.php?recinto_id=${recintoId}`)
        .then(response => response.json())
        .then(data => {
            console.log(data)
            const zonas = document.getElementById('zonas');
            zonas.innerHTML = ''; 
        
            const selec = document.createElement('select');
            selec.classList.add('zona_nombre');
            data.zonas.forEach(zona => {
                const option = document.createElement("option");
                option.value = zona.nombre_zona; // Usar una propiedad específica como value
                option.textContent = zona.nombre_zona; // Usar la misma propiedad para el texto
                selec.appendChild(option);
            });
            zonas.appendChild(selec); // Agregar el select de zonas al contenedor

            const funcionSelect = document.getElementById("funcion2");
            funcionSelect.innerHTML = ""; // Limpiar el select de funciones

            const defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.textContent = "Seleccione una función";
            funcionSelect.appendChild(defaultOption);

            data.funciones.forEach(funcion => {
                const option = document.createElement("option");
                option.value = funcion._id; // El ID de la funcin
                option.textContent = `${funcion.nombre} - ${funcion.fecha_inicio} a ${funcion.fecha_fin}`;
                funcionSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error al obtener las funciones:", error);
        });
    }
});


    // Lgica para agregar nuevas zonas
const agregarZonaBtn = document.getElementById("agregarZonaBtn");
const zonasContainer = document.getElementById("zonasContainer");
const checkboxContainer = document.getElementById('checkboxContainer');

// Generar casillas de verificación de A a Z
for (let i = 65; i <= 90; i++) { // 65 es 'A' y 90 es 'Z' en ASCII
    const letra = String.fromCharCode(i);
    
    // Crear un elemento de casilla de verificacin
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.name = 'filas';
    checkbox.value = letra;
    checkbox.id = letra;

    // Crear una etiqueta para la casilla
    const label = document.createElement('label');
    label.htmlFor = letra;
    label.textContent = letra;

    // Agregar la casilla y la etiqueta al contenedor
    checkboxContainer.appendChild(checkbox);
    checkboxContainer.appendChild(label);
    checkboxContainer.appendChild(document.createElement('br')); // Salto de línea
}

// Agregar la casilla de verificacin para la letra Ñ
const letran = '';
const checkboxn = document.createElement('input');
checkboxn.type = 'checkbox';
checkboxn.name = 'filas';
checkboxn.value = letran;
checkboxn.id = letran;

const labeln = document.createElement('label');
labeln.htmlFor = letran;
labeln.textContent = letran;

checkboxContainer.appendChild(checkboxn);
checkboxContainer.appendChild(labeln);
checkboxContainer.appendChild(document.createElement('br')); // Salto de línea para la 

agregarZonaBtn.addEventListener('click', () => {
    const tipoAsientoId = document.getElementById("tipoAsiento_s").value;
    const recintoId = document.getElementById("recinto2").value;
    const nombreZona = document.querySelector(".zona_nombre").value;
    const filasSeleccionadas = Array.from(document.querySelectorAll('input[name="filas"]:checked'))
                                    .map(fila => fila.value);
    const asientosInicio = parseInt(document.getElementById("asientosInicio").value);
    const asientosFin = parseInt(document.getElementById("asientosFin").value);

    if (nombreZona && filasSeleccionadas.length > 0 && asientosInicio > 0 && asientosFin > 0 && asientosFin >= asientosInicio) {
        const zonaDiv = document.createElement("div");
        zonaDiv.classList.add("zona");
        
        const tabla = document.createElement("table");
        const encabezado = document.createElement("thead");
        const headerRow = document.createElement("tr");
        
        const thZona = document.createElement("th");
        thZona.colSpan = 3; // Ajustar para tres columnas
        thZona.textContent = nombreZona;
        headerRow.appendChild(thZona);
        tabla.appendChild(encabezado);

        // Encabezado de las columnas
        const header = document.createElement("tr");
        header.innerHTML = "<th>Zona</th><th>Fila</th><th>Número de Asiento</th><th>Estado</th>";
        encabezado.appendChild(header);
        tabla.appendChild(encabezado);

        const cuerpoTabla = document.createElement("tbody");
        filasSeleccionadas.forEach(fila => {
            for (let i = asientosInicio; i <= asientosFin; i++) {
                const filaTr = document.createElement("tr");
        
                const nomTd = document.createElement("td");
                nomTd.textContent = nombreZona;
                filaTr.appendChild(nomTd);

                const filaTd = document.createElement("td");
                filaTd.textContent = fila;
                filaTr.appendChild(filaTd);

                // Columna del nmero de asiento
                const asientoTd = document.createElement("td");
                asientoTd.textContent = i;
                filaTr.appendChild(asientoTd);

                // Columna del select para el estado del asiento
                const estadoTd = document.createElement("td");
                const select = document.createElement("select");
                select.innerHTML = '<option value="Disponible">Disponible</option>'
                                + '<option value="Reservado">Reservado</option>'
                                + '<option value="Vendido">Vendido</option>';

                estadoTd.appendChild(select);
                filaTr.appendChild(estadoTd);

                cuerpoTabla.appendChild(filaTr);
            }
        });
        tabla.appendChild(cuerpoTabla);

        // Botn para eliminar la zona
        const eliminarBtn = document.createElement("button");
        eliminarBtn.textContent = "Eliminar Zona";
        eliminarBtn.onclick = function() {
            zonasContainer.removeChild(zonaDiv);
        };

        zonaDiv.appendChild(tabla);
        zonaDiv.appendChild(eliminarBtn);
        zonasContainer.appendChild(zonaDiv);

        // Limpiar y cerrar el modal
        document.querySelector(".zona_nombre").value = "";
        document.querySelectorAll('input[name="filas"]:checked').forEach(cb => cb.checked = false);
        document.getElementById("asientosInicio").value = "";
        document.getElementById("asientosFin").value = "";
        zonaModal.style.display = "none";
    } else {
        alert("Por favor, complete todos los campos y asegúrese de que el rango de asientos es vlido.");
    }
});



    function obtenerDatosAsientos() {
    const bloquesAsientos = document.querySelectorAll('.zona');
    const dataAsientos = [];
    bloquesAsientos.forEach(zona => {
        const asientos = zona.querySelectorAll('tbody tr');
        const tipoAsientoId = document.getElementById("tipoAsiento_s").value; // ID del tipo de asiento
        const recintoId = document.getElementById("recinto2").value; // ID del recinto
        const funcion =document.querySelector("#funcion2").value;
        asientos.forEach(asiento => {
            const zona = asiento.querySelector('td:nth-child(1)').textContent; // letra de la fila
            const fila = asiento.querySelector('td:nth-child(2)').textContent; // letra de la fila

            const numeroAsiento = asiento.querySelector('td:nth-child(3)').textContent; // nmero de asiento
            const estado = asiento.querySelector('select').value; 

            dataAsientos.push({
                zona:zona, 
                fila: fila,
                asiento: numeroAsiento,
                estado: estado,
                tipoAsientoId: tipoAsientoId, 
                recintoId: recintoId,
                funcion_even:funcion
            });
        });
    });
    return dataAsientos; // Asegrate de retornar los datos
}


document.getElementById("obtenerDatosBtn").onclick = async function() {
        const dataAsientos = obtenerDatosAsientos();
        console.log(dataAsientos)
        const response = await fetch('./apis/apia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dataAsientos), 
        });

        const resultado = await response.text(); 
        console.log(resultado)
        if (resultado.exito) {
            alert("Datos enviados correctamente.");
            window.location.href="asientos.php"
        } else {
            alert("Error al enviar los datos.");
        }
    };
}) 