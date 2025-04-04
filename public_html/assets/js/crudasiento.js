
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
    const contenedor_padre_zonas = document.getElementById("contenedor-padre-zonas");
    contenedor_padre_zonas.innerHTML = ""; // Limpiar el contenedor antes de agregar nuevas zonas
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
                   // Agregar capacidad como atributo
                   
                    

                    // Agregar contenido según el tipo de zona
                    if (zona.tipo === "Asiento") {
                        const contenedor_zonas = document.createElement("div");
                        contenedor_zonas.classList.add("contenedor-zonas"); 
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
                        contenedor_zonas.setAttribute("data-capacidad", capacidad);

                        contenedor_zonas.appendChild(crearZonaConAsientos());
                        
                        const input = document.createElement("div");
                        input.id="inputsContainer";
                        contenedor_zonas.appendChild(input);
                        contenedor_zonas.appendChild(document.createElement("hr"));
                        contenedor_padre_zonas.appendChild(contenedor_zonas);

                    } 
                    if (zona.tipo === "Pie") {
                        const contenedorSin_asiento = document.createElement("div");
                        contenedorSin_asiento.classList.add("zona-sin-asiento");
                        const nombreZonaElement = document.createElement("label");
                        nombreZonaElement.textContent = "Nombre de la Zona:";
                        contenedorSin_asiento.appendChild(nombreZonaElement);

                        const zonas = document.createElement("div");
                        zonas.classList.add("zonas"); // Usar una clase en lugar de un ID para evitar duplicados

                        const select = document.createElement("select");
                        select.classList.add("zona_nombre");
                        const option = document.createElement("option");
                        option.value = zona.nombre_zona;
                        option.textContent = zona.nombre_zona;
                        select.appendChild(option);
                        contenedorSin_asiento.appendChild(select);
                        contenedorSin_asiento.setAttribute("data-capacidad", capacidad);

                        console.log("Zona sin asientos");
                        const labelNombre = document.createElement("label");
                        labelNombre.textContent = "Cantidad de asientos:";
                    
                        const input = document.createElement("input");
                        input.type = "number";
                        input.className="cantidad_asientos";
                        input.required = true;
                    
                        
                    
                        contenedorSin_asiento.appendChild(labelNombre);
                        contenedorSin_asiento.appendChild(input);
                        contenedor_padre_zonas.appendChild(contenedorSin_asiento);

                    }

                    // Agregar el contenedor de la zona al contenedor padre
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


document.addEventListener("change", (event) => {
    if (event.target && event.target.matches('#checkboxContainer input[name="filas"]')) {
        const contenedorPadre = event.target.closest(".contenedor-zonas"); 
        console.log(contenedorPadre);
            manejarInputsAsientos(event.target,contenedorPadre); // Llama la función cuando un checkbox cambie
    }
});function manejarInputsAsientos(elemento, contenedorCheck) {
    const contenedorsub = contenedorCheck.querySelector('#inputsContainer');
    
    if (!contenedorsub) {
        console.error("Error: No se encontró #inputsContainer dentro de contenedorCheck.");
        return;
    }

    const capacidadMaxima = parseInt(contenedorCheck.getAttribute("data-capacidad"), 10) || 0;
    const idDiv = `input-container-${elemento.value}`;
    let divExistente = contenedorsub.querySelector(`#${idDiv}`);

    if (elemento.checked) {
        if (!divExistente) {
            const div = document.createElement('div');
            div.classList.add('flex-container');
            div.id = idDiv;

            const divColumna2 = document.createElement("div");
            const divSub = document.createElement("div");
            divSub.classList.add('contenedor-sub');

            const divInicio = document.createElement('div');
            const labelInicio = document.createElement('label');
            labelInicio.textContent = `Asientos desde (${elemento.value}):`;
            const inputInicio = document.createElement('input');
            inputInicio.type = 'number';
            inputInicio.min = '1';
            inputInicio.max = "100";
            inputInicio.classList.add('asientoInicio');
            inputInicio.dataset.fila = elemento.value;

            divInicio.appendChild(labelInicio);
            divInicio.appendChild(inputInicio);

            const divFin = document.createElement('div');
            const labelFin = document.createElement('label');
            labelFin.textContent = `hasta (${elemento.value}):`;
            const inputFin = document.createElement('input');
            inputFin.type = 'number';
            inputFin.min = '1';
            inputFin.max = "100";
            inputFin.classList.add('asientoFin');
            inputFin.dataset.fila = elemento.value;

            divFin.appendChild(labelFin);
            divFin.appendChild(inputFin);

            const divLimitacion = document.createElement("div");
            const inputlabelimitacion = document.createElement('label');
            inputlabelimitacion.setAttribute("for", "limitacion");
            inputlabelimitacion.textContent = "Colocar una limitación";
            const inputLimitacion = document.createElement("input");
            inputLimitacion.classList.add("limitacion");
            inputLimitacion.placeholder = "Colocar una limitación";
            inputLimitacion.type = 'number';

            const divButtonLimitacion = document.createElement("button");
            divButtonLimitacion.classList.add('agregar-limitacion');
            divButtonLimitacion.textContent = "Agregar Limitación";

            const divButtonEliminarLimitacion = document.createElement("button");
            divButtonEliminarLimitacion.classList.add('eliminar-limitacion');
            divButtonEliminarLimitacion.textContent = "Eliminar Limitación";

            divButtonLimitacion.addEventListener('click', function () {
                const nuevaLimitacion = document.createElement("div");
                const labelNuevaLimitacion = document.createElement('label');
                labelNuevaLimitacion.textContent = "Colocar una limitación adicional";
                const inputNuevaLimitacion = document.createElement("input");
                inputNuevaLimitacion.classList.add("limitacion");
                inputNuevaLimitacion.placeholder = "Colocar una limitación adicional";
                inputNuevaLimitacion.type = 'number';

                nuevaLimitacion.appendChild(labelNuevaLimitacion);
                nuevaLimitacion.appendChild(inputNuevaLimitacion);

                divSub.appendChild(nuevaLimitacion);
            });

            divButtonEliminarLimitacion.addEventListener('click', function () {
                const limitaciones = divSub.querySelectorAll('.limitacion');
                if (limitaciones.length > 0) {
                    const lastLimitacion = limitaciones[limitaciones.length - 1].parentElement;
                    divSub.removeChild(lastLimitacion);
                }
            });

            // Evento de input para calcular el total de asientos en el contenedor
            function recalcularTotalAsientos() {
                let totalAsientosInicio = 0;
                let totalAsientosFin = 0;
                let totalAsientos = 0;
                // Seleccionar todos los inputs de inicio y fin dentro de #contenedor-zonas
                const inputsInicio = contenedorCheck.querySelectorAll('.asientoInicio');
                const inputsFin = contenedorCheck.querySelectorAll('.asientoFin');
                const limitaciones = contenedorCheck.querySelectorAll('.limitacion');
                const limitacionValues = limitaciones.length;

                // Iterar sobre los inputs de inicio y fin y calcular el total
                inputsInicio.forEach(input => {
                    const inicio = parseInt(input.value, 10) || 0;
                    totalAsientosInicio += inicio;
                });

                inputsFin.forEach(input => {
                    const fin = parseInt(input.value, 10) || 0;
                    totalAsientosFin += fin;
                });
                totalAsientos = totalAsientosFin - totalAsientosInicio + 1; // +1 para incluir el asiento de inicio

                // Ajuste por las limitaciones
                totalAsientos -= limitacionValues;


                // Validación de la capacidad máxima
                if (totalAsientos > capacidadMaxima) {
                    alert(`El total de asientos (${totalAsientos}) excede la capacidad máxima (${capacidadMaxima}).`);
                }
            }

            // Llamar a la función de recalcular cada vez que un input cambie
            inputInicio.addEventListener('input', recalcularTotalAsientos);
            inputFin.addEventListener('input', recalcularTotalAsientos);

            divLimitacion.appendChild(inputlabelimitacion);
            divLimitacion.appendChild(inputLimitacion);
            divColumna2.appendChild(divButtonLimitacion);
            divColumna2.appendChild(divButtonEliminarLimitacion);

            divSub.appendChild(divInicio);
            divSub.appendChild(divFin);
            divSub.appendChild(divLimitacion);
            div.appendChild(divSub);
            div.appendChild(divColumna2);

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
    let matrizLimitaciones = []; // Matriz para almacenar las limitaciones por letra

    // Verificamos si existen zonas con asientos
    if (bloquesAsientos.length > 0) {
        for (const zona of bloquesAsientos) {
            let rangoTotal = []; // Se usa let para acumular valores correctamente
            const filas = [];
            const limitacionesPorLetra = {}; // Objeto para almacenar las limitaciones por letra en esta zona

            const nombreZonaElement = zona.querySelector('.zona_nombre');
            const nombreZona = nombreZonaElement ? nombreZonaElement.value.trim() : "";
            if (!nombreZona) {
                alert("Falta el nombre de la zona.");
                return false;
            }

            const inputsContainer = zona.querySelectorAll(".flex-container");
            console.log(inputsContainer);
            for (const item of inputsContainer) {
                const inputInicioElement = item.querySelector('.asientoInicio');
                const inputFinElement = item.querySelector('.asientoFin');
                const letra = item.querySelector('.asientoInicio')?.dataset.fila; // Obtener la letra asociada

                if (!inputInicioElement || !inputFinElement) {
                    alert("Falta uno de los campos de asientos.");
                    return false;
                }

                const inputInicio = parseInt(inputInicioElement.value, 10);
                const inputFin = parseInt(inputFinElement.value, 10);

                if (isNaN(inputInicio) || isNaN(inputFin)) {
                    alert("Por favor, ingresa valores válidos para 'Asiento desde' y 'Asiento hasta'.");
                    return false;
                }

                if (inputInicio >= inputFin) {
                    alert("El número de asiento 'hasta' debe ser mayor que 'desde'.");
                    return false;
                }


                // Procesar las limitaciones para esta letra
                const limitaciones = [];
                item.querySelectorAll('.limitacion').forEach(lim => {
                    const limitacionValue = parseInt(lim.value.trim(), 10);
                    if (!isNaN(limitacionValue)) {
                        limitaciones.push(limitacionValue);
                    }
                });

                // Almacenar las limitaciones por letra
                limitacionesPorLetra[letra] = limitaciones;

                rangoTotal.push(
                    {fila:letra,rangoInicio:inputInicio, rangoFin:inputFin}
                );
            }

            // Convertir el objeto de limitaciones por letra en un formato de matriz
            const limitacionesMatriz = Object.entries(limitacionesPorLetra).map(([letra, limitaciones]) => ({
                letra,
                limitaciones,
                totalLimitaciones: limitaciones.length
            }));

            matrizLimitaciones.push({
                zona: nombreZona,
                limitaciones: limitacionesMatriz
            });


           

            dataAsientos.push({
                zona: nombreZona,
                caracteristicas: rangoTotal,
                limitaciones: limitacionesMatriz
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
    const e = document.getElementById("evento").value.trim();
    if (!tipoAsientoId || !recintoId || !funcion) {
        alert("Por favor, completa todos los campos obligatorios (tipo de asiento, recinto y función).");
        return false;
    }
    // Si todo es válido, agregar los datos al arreglo final
    datos.push({
        datos: dataAsientos,
        datosSin: dataSinAsientos.length > 0 ? dataSinAsientos : null,
        evento: e,
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

        const resultado = await response.text();
        console.log(resultado);

        if (resultado.status) {
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