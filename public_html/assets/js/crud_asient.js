
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

    document.getElementById("agregarOtraZona").addEventListener("click", () => {
    agregarOtraZona();

    // Espera un poco antes de verificar si los checkboxes clonados existen
   
});
    // Cerrar el modal al hacer clic en el botón
    document.getElementById('closeModalBtn').addEventListener('click', function() {
        document.getElementById('addSeatModal').style.display = 'none';
    });
   document.getElementById('agregarZonaSinAsiento').addEventListener("click",()=>{
        agregarZonaSinAsiento();
   }

)
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
const inputsContainer = document.getElementById('inputsContainer'); // Contenedor para los inputs dinámicos

if (!checkboxContainer || !inputsContainer) {
    console.error('Contenedor no encontrado');
    return;
}
generarInputsChec()
document.addEventListener("change", (event) => {
    if (event.target && event.target.matches('#checkboxContainer input[name="filas"]')) {
        const contenedorPadre = event.target.closest(".contenedor-zonas"); // Encuentra el contenedor padre más cercano
            manejarInputsAsientos(event.target,contenedorPadre); // Llama la función cuando un checkbox cambie
    }
});


function agregarOtraZona(){
    const contenedor = document.getElementById('contenedor-padre-zonas');
    
    // Crear una línea separadora (hr)
    const hr = document.createElement("hr");
    contenedor.appendChild(hr);

    // Seleccionar el contenedor original y clonar
    const contenedorZonas = document.querySelector(".contenedor-zonas");
    const contenedorClonado = contenedorZonas.cloneNode(true); // Clona el contenedor y sus hijos

    // Limpiar los valores de los inputs dentro del contenedor clonado
    const inputs = contenedorClonado.querySelectorAll("input");
    inputs.forEach(input => {
        input.checked = false; // Desmarcar los checkboxes
    });

    // Limpiar los elementos dentro de inputsContainer solo en el contenedor clonado
    const inputsContainer = contenedorClonado.querySelector("#inputsContainer"); // Selecciona solo el inputsContainer dentro del contenedor clonado
    if (inputsContainer) {
        inputsContainer.innerHTML = ""; // Vaciar los contenidos del contenedor
    }

    // Añadir el contenedor clonado al contenedor principal
    contenedor.appendChild(contenedorClonado);
}
function generarInputsChec(){
    const checkboxContainer = document.getElementById('checkboxContainer');

    // Generar casillas de verificación de A a Z
    for (let i = 65; i <= 90; i++) {
        const letra = String.fromCharCode(i);
        
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.name = 'filas';
    checkbox.value = letra;
    checkbox.id = letra;
    
    const label = document.createElement('label');
    label.htmlFor = letra;
    label.textContent = letra;
    
    checkbox.addEventListener('change', function () {
        manejarInputsAsientos();
    });

    checkboxContainer.appendChild(checkbox);
    checkboxContainer.appendChild(label);
    checkboxContainer.appendChild(document.createElement('br'));
}

// Agregar casilla para la letra Ñ
const checkboxn = document.createElement('input');
checkboxn.type = 'checkbox';
checkboxn.name = 'filas';
checkboxn.value = 'Ñ';
checkboxn.id = 'Ñ';

const labeln = document.createElement('label');
labeln.htmlFor = 'Ñ';
labeln.textContent = 'Ñ';

checkboxn.addEventListener('change', function () {
    manejarInputsAsientos();
});

checkboxContainer.appendChild(checkboxn);
checkboxContainer.appendChild(labeln);
checkboxContainer.appendChild(document.createElement('br'));
}
function agregarZonaSinAsiento() { 
    const contenedor = document.getElementById('contenedor-padre-zonas');

    if (!contenedor) {
        console.error("Error: No se encontró el contenedor con ID 'contenedor-padre-zonas'");
        return;
    }

    // Crear una línea separadora
    const hr = document.createElement("hr");
    contenedor.appendChild(hr);

    // Crear un div contenedor para los elementos
    const divZona = document.createElement("div");
    divZona.classList.add("zona-sin-asiento");

    // Verificar si el select con la clase 'zona_nombre' existe
    const selectOriginal = document.querySelector('.zona_nombre');
    if (selectOriginal) {
        // Clonar el select con clase 'zona_nombre'
        const selectClonado = selectOriginal.cloneNode(true);

        // Crear el label
        const label = document.createElement("label");
        label.setAttribute("for", "cantidad");
        label.textContent = "Cantidad de Asientos:";
        const labelNombre = document.createElement("label");
        labelNombre.setAttribute("for", "zona");
        labelNombre.textContent = "Nombre de la Zona:";

        // Crear el input
        const input = document.createElement("input");
        input.type = "number";
        input.classList.add("cantidad_asientos");
        input.min = "1";
        input.required = true;
        input.placeholder = "Cantidad de boletos";

        divZona.appendChild(labelNombre);
        divZona.appendChild(selectClonado); // Agregar el select clonado
        divZona.appendChild(label);
        divZona.appendChild(input);

        // Agregar el div contenedor al contenedor principal
        contenedor.appendChild(divZona);
    } else {
        console.error("Error: No se encontró el elemento select con la clase 'zona_nombre'");
    }
}
// Función para agregar o eliminar inputs según la selección de checkboxes
function manejarInputsAsientos(elemento, contenedorCheck) {

    // Buscar el contenedor dentro del contenedorCheck
    const contenedorsub = contenedorCheck.querySelector('#inputsContainer');

    // Verificar si el contenedor existe
    if (!contenedorsub) {
        console.error("Error: No se encontró #inputsContainer dentro de contenedorCheck.");
        return; // Detiene la ejecución para evitar errores
    }

    const idDiv = `input-container-${elemento.value}`;
    let divExistente = contenedorsub.querySelector(`#${idDiv}`);

    if (elemento.checked) {
        if (!divExistente) {
            const div = document.createElement('div');
            div.classList.add('flex-container');
            div.id = idDiv;

            // Crear input para asientoInicio
            const divInicio = document.createElement('div');
            const labelInicio = document.createElement('label');
            labelInicio.textContent = `Asientos desde (${elemento.value}):`;
            const inputInicio = document.createElement('input');
            inputInicio.type = 'number';
            inputInicio.min = '1';
            inputInicio.max = '30';
            inputInicio.classList.add('asientoInicio');
            inputInicio.dataset.fila = elemento.value;

            divInicio.appendChild(labelInicio);
            divInicio.appendChild(inputInicio);

            // Crear input para asientoFin
            const divFin = document.createElement('div');
            const labelFin = document.createElement('label');
            labelFin.textContent = `hasta (${elemento.value}):`;
            const inputFin = document.createElement('input');
            inputFin.type = 'number';
            inputFin.min = '1';
            inputFin.max = '30';
            inputFin.classList.add('asientoFin');
            inputFin.dataset.fila = elemento.value;

            divFin.appendChild(labelFin);
            divFin.appendChild(inputFin);

            // Agregar los divs de inputs al contenedor principal
            div.appendChild(divInicio);
            div.appendChild(divFin);

            contenedorsub.appendChild(div);
        }
    } else {
        if (divExistente) {
            divExistente.remove();
        }
    }
}


// Añadir un evento para escuchar los cambios en los checkboxes y regenerar los inputs


// Código para agregar una zona
const agregarZonaBtn = document.getElementById('agregarZonaBtn');
const zonasContainer = document.getElementById('zonasContainer');
agregarZonaBtn.addEventListener('click', () => {
    const tipoAsientoId = document.getElementById("tipoAsiento_s").value;
    const recintoId = document.getElementById("recinto2").value;
    const nombreZona = document.querySelector(".zona_nombre").value;
    const inputsContainers = document.querySelectorAll("#inputsContainer"); // Obtener todos los contenedores de inputs
    const filasSeleccionadas = Array.from(document.querySelectorAll('input[name="filas"]:checked'))
                                    .map(fila => fila.value);

    let asientosInicioArray = [];
    let asientosFinArray = [];

    // Recorremos todos los contenedores de inputs y obtenemos los valores de AsientoInicio y AsientoFin
    inputsContainers.forEach(container => {
        const asientosInicio = parseInt(container.querySelector(".asientoInicio").value);
        const asientosFin = parseInt(container.querySelector(".asientoFin").value);

        // Verificamos que los valores sean válidos
        if (asientosInicio && asientosFin && asientosFin >= asientosInicio) {
            asientosInicioArray.push(asientosInicio);
            asientosFinArray.push(asientosFin);
        }
    });
console.log(asientosInicioArray)
    // Validamos que todos los campos requeridos estén completos y los arreglos tengan valores
    if (nombreZona && filasSeleccionadas.length > 0 && asientosInicioArray.length > 0 && asientosFinArray.length > 0) {
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
            // Recorremos los rangos de asientos para cada zona
            asientosInicioArray.forEach((inicio, index) => {
                const fin = asientosFinArray[index];
                for (let i = inicio; i <= fin; i++) {
                    const filaTr = document.createElement("tr");

                    const nomTd = document.createElement("td");
                    nomTd.textContent = nombreZona;
                    filaTr.appendChild(nomTd);

                    const filaTd = document.createElement("td");
                    filaTd.textContent = fila;
                    filaTr.appendChild(filaTd);

                    // Columna del número de asiento
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
        });

        tabla.appendChild(cuerpoTabla);

        // Botón para eliminar la zona
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
        document.querySelectorAll('.asientoInicio').forEach(input => input.value = "");
        document.querySelectorAll('.asientoFin').forEach(input => input.value = "");
        zonaModal.style.display = "none";
    } else {
        alert("Por favor, complete todos los campos y asegúrese de que el rango de asientos es válido.");
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

        const resultado = await response.json(); 
        console.log(resultado)
        if (resultado.exito) {
            alert("Datos enviados correctamente.");
            window.location.href="./asientos.php"
        } else {
            alert("Error al enviar los datos.");
        }
    };
}) 