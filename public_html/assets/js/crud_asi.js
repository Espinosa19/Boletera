
document.addEventListener("DOMContentLoaded", () => {

    const closeModalBtn = document.getElementById("closeModalBtn");


    closeModalBtn.onclick = function() {
        window.location.href="asientos-tabla.php"
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
function agregarOtraZona() {
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
    const buttClonado=contenedorClonado.querySelector("#eliminar")
    if(buttClonado){
        buttClonado.remove()
    }
    const button = document.createElement("button");
    button.id = "eliminar";
    button.textContent = "Eliminar Zona"; // Establecer el texto del botón
    
    // Agregar un evento al botón para eliminar el contenedor clonado
    button.addEventListener('click', function() {
        // Confirmación antes de eliminar
        const confirmacion = confirm("¿Estás seguro de que quieres eliminar esta zona?");
        
        // Si se confirma, eliminar el contenedor clonado
        if (confirmacion) {
            contenedorClonado.remove(); // Elimina solo el contenedor clonado
            alert("Zona eliminada con éxito.");
        } else {
            alert("La eliminación ha sido cancelada.");
        }
    });
    contenedorClonado.appendChild(button)
    // Añadir el contenedor clonado y el botón de eliminar al contenedor principal
    contenedor.appendChild(contenedorClonado);
}
document.getElementById('eliminar').addEventListener('click', () => {
    const confirmacion = confirm("¿Estás seguro de que quieres eliminar esta zona?");
    
    if (confirmacion) {
        const contenedorZonas = document.querySelector('.contenedor-zonas');
        if (contenedorZonas) {
            contenedorZonas.remove(); // Elimina el contenedor .contenedor-zonas
            alert("Zona eliminada con éxito.");
        }
    } else {
        alert("La eliminación ha sido cancelada.");
    }
});

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

    // Crear un div contenedor para los elementos
    const divZona = document.createElement("div");
    divZona.classList.add("zona-sin-asiento");

    // Verificar si el select con la clase 'zona_nombre' existe
    const selectOriginal = document.querySelector('.zona_nombre');
    if (selectOriginal) {
        // Clonar el select con clase 'zona_nombre'
        const selectClonado = selectOriginal.cloneNode(true);

        // Crear el label para el nombre de la zona
        const labelNombre = document.createElement("label");
        labelNombre.setAttribute("for", "zona");
        labelNombre.textContent = "Nombre de la Zona:";

        // Crear el label para la cantidad de asientos
        const label = document.createElement("label");
        label.setAttribute("for", "cantidad");
        label.textContent = "Cantidad de Asientos:";

        // Crear el input
        const input = document.createElement("input");
        input.type = "number";
        input.classList.add("cantidad_asientos");
        input.min = "1";
        input.required = true;
        input.placeholder = "Cantidad de boletos";

        // Crear botón para eliminar la zona
        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = "Eliminar Zona";
        btnEliminar.classList.add("btn-eliminar-zona");
        btnEliminar.addEventListener("click", function() {
            divZona.remove();
        });

        // Agregar elementos al divZona
        divZona.appendChild(labelNombre);
        divZona.appendChild(selectClonado); // Agregar el select clonado
        divZona.appendChild(label);
        divZona.appendChild(input);
        divZona.appendChild(btnEliminar); // Agregar el botón de eliminar

        // Agregar el divZona al contenedor principal
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

            // Crear un contenedor adicional para los inputs
            const divColumna2=document.createElement("div")
            const divSub = document.createElement("div");
            divSub.classList.add('contenedor-sub');

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

            // Crear div de limitación
            const divLimitacion = document.createElement("div");
            const inputlabelimitacion = document.createElement('label');
            inputlabelimitacion.setAttribute("for", "limitacion"); // Corregido
            inputlabelimitacion.textContent = "Colocar una limitación";
            const inputLimitacion = document.createElement("input");
            inputLimitacion.classList.add("limitacion"); // Corregido
            inputLimitacion.placeholder = "Colocar una limitacion";
            inputLimitacion.type = 'number';

            // Crear botón para agregar limitación
            const divButtonLimitacion = document.createElement("button");
            divButtonLimitacion.classList.add('agregar-limitacion');
            divButtonLimitacion.textContent = "Agregar Limitación";

            // Crear botón para eliminar limitación
            const divButtonEliminarLimitacion = document.createElement("button");
            divButtonEliminarLimitacion.classList.add('eliminar-limitacion');
            divButtonEliminarLimitacion.textContent = "Eliminar Limitación";

            // Evento de clic para agregar un nuevo contenedor de limitación
            divButtonLimitacion.addEventListener('click', function() {
                const nuevaLimitacion = document.createElement("div");
                const labelNuevaLimitacion = document.createElement('label');
                labelNuevaLimitacion.textContent = "Colocar una limitación adicional";
                const inputNuevaLimitacion = document.createElement("input");
                inputNuevaLimitacion.classList.add("limitacion");
                inputNuevaLimitacion.placeholder = "Colocar una limitacion adicional";
                inputNuevaLimitacion.type = 'number';

                nuevaLimitacion.appendChild(labelNuevaLimitacion);
                nuevaLimitacion.appendChild(inputNuevaLimitacion);

                // Añadir el nuevo contenedor de limitación dentro de divSub
                divSub.appendChild(nuevaLimitacion);
            });

            // Evento de clic para eliminar la última limitación
            divButtonEliminarLimitacion.addEventListener('click', function() {
                const limitaciones = divSub.querySelectorAll('.limitacion');
                if (limitaciones.length > 0) {
                    const lastLimitacion = limitaciones[limitaciones.length - 1].parentElement;
                    divSub.removeChild(lastLimitacion); // Elimina el último contenedor de limitación
                }
            });

            // Añadir los elementos creados al divLimitacion
            divLimitacion.appendChild(inputlabelimitacion);
            divLimitacion.appendChild(inputLimitacion);

            // Agregar los divs de inputs al contenedor principal
            divSub.appendChild(divInicio);
            divSub.appendChild(divFin);
            divSub.appendChild(divLimitacion);
            divColumna2.appendChild(divButtonLimitacion);
            divColumna2.appendChild(divButtonEliminarLimitacion)
            // Agregar el contenedor y los botones a la estructura final
            div.appendChild(divSub);
            div.appendChild(divColumna2);

            // Finalmente agregar el div creado al contenedor principal
            contenedorsub.appendChild(div);
        }
    } else {
        if (divExistente) {
            divExistente.remove();
        }
    }
}

function obtenerDatosAsientos() {
    const dataSinAsientos = [];
    const dataAsientos = [];
    const datos = [];

    const bloquesAsientos = document.querySelectorAll('.contenedor-zonas');
    const bloquesSinAsientos = document.querySelectorAll('.zona-sin-asiento');

    // Verificamos si existen zonas con asientos
    if (bloquesAsientos.length > 0) {
        for (const zona of bloquesAsientos) {
            let rangoTotal = 0; // Se usa let para acumular valores correctamente
            const limitaciones = [];
            const filas = [];

            const nombreZonaElement = zona.querySelector('.zona_nombre');
            const nombreZona = nombreZonaElement ? nombreZonaElement.value.trim() : "";
            if (!nombreZona) {
                alert("Falta el nombre de la zona.");
                return false;
            }

            const inputsContainer = zona.querySelectorAll(".flex-container");
            for (const item of inputsContainer) {
                const inputInicioElement = item.querySelector('.asientoInicio');
                const inputFinElement = item.querySelector('.asientoFin');

                if (!inputInicioElement || !inputFinElement) {
                    alert("Falta uno de los campos de asientos.");
                    return false;
                }

                const inputInicio = parseInt(inputInicioElement.value, 10);
                const inputFin = parseInt(inputFinElement.value, 10);
                const fila = inputInicioElement.getAttribute("data-fila");

                if (isNaN(inputInicio) || isNaN(inputFin)) {
                    alert("Por favor, ingresa valores válidos para 'Asiento desde' y 'Asiento hasta'.");
                    return false;
                }

                if (inputInicio >= inputFin) {
                    alert("El número de asiento 'hasta' debe ser mayor que 'desde'.");
                    return false;
                }

                if (fila) filas.push(fila);

                item.querySelectorAll('.limitacion').forEach(lim => {
                    const limitacionValue = lim.value.trim();
                    if (limitacionValue) limitaciones.push(limitacionValue);
                });

                rangoTotal += (inputFin - inputInicio);
            }

            if (rangoTotal === 0) {
                alert("Por favor, ingresa al menos un rango válido de asientos.");
                return false;
            }

            dataAsientos.push({
                zona: nombreZona,
                rango: rangoTotal,
                filas: filas,
                limitaciones: limitaciones
            });
        }
    }

    // Verificamos si existen zonas sin asientos
    if (bloquesSinAsientos.length > 0) {
        for (const zona of bloquesSinAsientos) {
            const cantidadAsientos = zona.querySelector(".cantidad_asientos");
            const nombreZonaElement = zona.querySelector(".zona_nombre");

            const cantidad = cantidadAsientos ? parseInt(cantidadAsientos.value, 10) : 0;
            const nombreZona = nombreZonaElement ? nombreZonaElement.value.trim() : "";

            if (isNaN(cantidad) || cantidad <= 0) {
                alert("Por favor, ingresa una cantidad válida de asientos en zonas sin asientos.");
                return false;
            }

            dataSinAsientos.push({
                cantidad: cantidad,
                nombre_zona: nombreZona
            });
        }
    }

    // Validar campos de tipoAsientoId, recintoId y funcion
    const tipoAsientoId = document.getElementById("tipoAsiento_s")?.value.trim();
    const recintoId = document.getElementById("recinto2")?.value.trim();
    const funcion = document.querySelector("#funcion2")?.value.trim();

    if (!tipoAsientoId || !recintoId || !funcion) {
        alert("Por favor, completa todos los campos obligatorios (tipo de asiento, recinto y función).");
        return false;
    }

    // Si todo es válido, agregar los datos al arreglo final
    datos.push({
        datos: dataAsientos,
        datosSin: dataSinAsientos.length > 0 ? dataSinAsientos : null,
        tipoAsientoId: tipoAsientoId,
        recintoId: recintoId,
        funcion_even: funcion
    });

    return datos;
}

document.getElementById("obtenerDatosBtn").onclick = async function() {
    const confirmarEnvio = confirm("¿Estás seguro de que deseas enviar los datos?");
    if (!confirmarEnvio) {
        return; // Si el usuario cancela, no se ejecuta la solicitud
    }

    const dataAsientos = obtenerDatosAsientos();
    console.log(dataAsientos)

    try {
        const response = await fetch('./apis/apia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dataAsientos),
        });

        const resultado = await response.text();
        console.log(resultado);

        if (resultado.exito) {
            alert("Datos enviados correctamente.");
            window.location.href = "asientos-tabla.php";
        } else {
            alert("Error al enviar los datos.");
        }
    } catch (error) {
        console.error("Error en la solicitud:", error);
        alert("Hubo un problema al enviar los datos.");
    }
}
})