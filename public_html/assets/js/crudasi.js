
document.addEventListener("DOMContentLoaded", () => {

    const closeModalBtn = document.getElementById("closeModalBtn");


    closeModalBtn.onclick = function() {
        window.location.href="asientos-tabla.php"
    };

    // Cerrar el modal al hacer clic en el botón
    document.getElementById('closeModalBtn').addEventListener('click', function() {
        document.getElementById('addSeatModal').style.display = 'none';
    });


document.getElementById("evento").addEventListener("change", function () { 
    const evento = this.value; // Usamos `this.value` dentro de una función normal
    if (evento) {
        fetch(`./apis/apiof.php?evento_id=${evento}`) // Usamos `evento` en lugar de `recintoId`
        .then(response => response.json())
        .then(data => {
            console.log(data);

            const recinto2 = document.getElementById("recinto2");
            recinto2.innerHTML = ""; // Limpiar opciones previas

            // Agregar una opción por defecto
            const defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.innerText = "Seleccione un recinto";
            recinto2.appendChild(defaultOption);

            // Agregar las opciones de los recintos
            data.forEach(reci => {
                const option = document.createElement("option");
                option.value = reci.recinto; 
                option.setAttribute("data-funcion",reci._id);
                option.innerText = `${reci.nombre}-${reci.fecha_inicio} a ${reci.fecha_fin}`;
                recinto2.appendChild(option);
            });
        })
        .catch(error => console.error("Error en la petición:", error));
    }
});
document.getElementById("recinto2").addEventListener("change", function () {
    const recintoId = this.value;
    const evento = document.getElementById("evento").value;

    if (recintoId) {
        fetch(`./apis/apiof.php?recinto_id=${recintoId}&evento_id=${evento}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);

                const contenedor_padre_zonas = document.getElementById("contenedor-padre-zonas");
                contenedor_padre_zonas.innerHTML = ""; // Limpiar el contenedor antes de agregar nuevas zonas

                data.zonas.forEach(zona => {
                    let capacidad = zona.capacidad || 0; // Usar 0 si no hay capacidad definida
                    // Crear un nuevo contenedor para cada zona
                    const contenedor_zonas = document.createElement("div");
                    contenedor_zonas.classList.add("contenedor-zonas"); // Usar una clase en lugar de un ID para evitar duplicados
                    contenedor_zonas.setAttribute("data-capacidad", capacidad); // Agregar capacidad como atributo

                    const nombreZonaElement = document.createElement("label");
                    nombreZonaElement.textContent = "Nombre de la Zona:";
                    contenedor_zonas.appendChild(nombreZonaElement);

                    const zonas = document.createElement("div");
                    zonas.classList.add("zonas"); // Usar una clase en lugar de un ID para evitar duplicados

                    const select = document.createElement("select");
                    select.classList.add("zona_nombre");
                    const option = document.createElement("option");
                    option.value = zona.nombre_zona;
                    option.textContent = zona.nombre_zona;
                    select.appendChild(option);
                    contenedor_zonas.appendChild(select);

                    // Agregar contenido según el tipo de zona
                    if (zona.tipo === "Asiento") {
                        contenedor_zonas.appendChild(crearZonaConAsientos());
                        
                        const input = document.createElement("div");
                        input.id="inputsContainer";
                        contenedor_zonas.appendChild(input);
                        contenedor_zonas.appendChild(document.createElement("hr"));
                    } else if (zona.tipo === "sin_asiento") {
                        contenedor_zonas.appendChild(crearZonaSinAsientos());
                    }

                    // Agregar el contenedor de la zona al contenedor padre
                    contenedor_padre_zonas.appendChild(contenedor_zonas);
                });
            })
            .catch(error => {
                console.error("Error al obtener las funciones:", error);
            });
    }
});
// Función para crear una zona con asientos
function crearZonaConAsientos() {

    const checkboxContainer = document.createElement('div');
    checkboxContainer.id = "checkboxContainer";

    // Generar checkboxes de A-Z
    for (let i = 65; i <= 90; i++) {
        agregarCheckbox(checkboxContainer, String.fromCharCode(i));
    }

    // Agregar checkbox para la letra Ñ
    agregarCheckbox(checkboxContainer, "Ñ");

    // Botón para eliminar zona

   

    return checkboxContainer;
}
function agregarCheckbox(container, letra) {
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.name = 'filas';
    checkbox.value = letra;
    checkbox.id = letra;

    const label = document.createElement('label');
    label.htmlFor = letra;
    label.textContent = letra;

    checkbox.addEventListener('change', manejarInputsAsientos);

    container.appendChild(checkbox);
    container.appendChild(label);
    container.appendChild(document.createElement('br'));
}
// Función para crear una zona sin asientos
function crearZonaSinAsientos() {
    const divZona = document.createElement("div");
    divZona.classList.add("zona-sin-asiento");

    const labelNombre = document.createElement("label");
    labelNombre.textContent = "Nombre de la Zona:";

    const input = document.createElement("input");
    input.type = "text";
    input.classList.add("zona_nombre");
    input.required = true;

    const btnEliminar = document.createElement("button");
    btnEliminar.textContent = "Eliminar Zona";
    btnEliminar.classList.add("btn-eliminar-zona");
    btnEliminar.addEventListener("click", function () {
        divZona.remove();
    });

    divZona.appendChild(labelNombre);
    divZona.appendChild(input);
    divZona.appendChild(btnEliminar);

    return divZona;
}

document.addEventListener("change", (event) => {
    if (event.target && event.target.matches('#checkboxContainer input[name="filas"]')) {
        const contenedorPadre = event.target.closest(".contenedor-zonas"); // Encuentra el contenedor padre más cercano
            manejarInputsAsientos(event.target,contenedorPadre); // Llama la función cuando un checkbox cambie
    }
});

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
// Función para agregar o eliminar inputs según la selección de checkboxes
function manejarInputsAsientos(elemento, contenedorCheck) {
    // Buscar el contenedor dentro del contenedorCheck
    const contenedorsub = contenedorCheck.querySelector('#inputsContainer');

    // Verificar si el contenedor existe
    if (!contenedorsub) {
        console.error("Error: No se encontró #inputsContainer dentro de contenedorCheck.");
        return; // Detiene la ejecución para evitar errores
    }
    const capacidadMaxima = parseInt(contenedorCheck.getAttribute("data-capacidad"), 10) || 0;

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
    const select = document.getElementById("recinto2"); // Reemplaza "miSelect" con el ID real
    const opcionSeleccionada = select.options[select.selectedIndex];
    const funcion = opcionSeleccionada.getAttribute("data-funcion");
    const e=document.getElementById("evento").value.trim();
    if (!tipoAsientoId || !recintoId || !funcion) {
        alert("Por favor, completa todos los campos obligatorios (tipo de asiento, recinto y función).");
        return false;
    }
    // Si todo es válido, agregar los datos al arreglo final
    datos.push({
        datos: dataAsientos,
        datosSin: dataSinAsientos.length > 0 ? dataSinAsientos : null,
        evento:e,
        tipoAsientoId: tipoAsientoId,
        recintoId: recintoId,
        funcion_even: funcion
    });
    console.log(datos);

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

        const resultado = await response.json();
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