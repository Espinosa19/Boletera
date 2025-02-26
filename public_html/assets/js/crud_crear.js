document.getElementById("evento").addEventListener("change", function() {
    const eventoId = this.value;
    const recintoSelect = document.getElementById("recinto");

    // Limpiar selects al cambiar el evento
    recintoSelect.innerHTML = '<option value="">Seleccione un recinto</option>';

    if (eventoId) {
        fetch("./apis/apib.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ evento_id: eventoId })
        })
        .then(response => response.json()) // Convertimos la respuesta a JSON
        .then(data => {
            console.log("Respuesta de la API:", data); // Depuración

            if (data.success && Array.isArray(data.recintos)) {
                data.recintos.forEach(recinto => {
                    // Iteramos sobre cada recinto
                    recinto.funciones.forEach(funcion => {
                        let option = document.createElement("option");
                        option.value = recinto.id; // ID del recinto
                        option.setAttribute("data-funcion-id", funcion.id); // ID de la función
                        
                        // Formatear las fechas antes de agregarlas al option
                        let fechaInicio = formatFecha(funcion.fecha_inicio);
                        let fechaFin = formatFecha(funcion.fecha_fin);

                        option.textContent = `${recinto.nombre} - Función de ${fechaInicio} a ${fechaFin}`;
                        recintoSelect.appendChild(option);
                    });
                });
            } else {
                console.log("No se encontraron recintos");
            }
        })
        .catch(error => console.error("Error al cargar recintos:", error));
    }
});
document.getElementById("recinto").addEventListener("change", function() {
    let selectedOption = this.options[this.selectedIndex];
    let recintoId = selectedOption.value;
    let funcionId = selectedOption.getAttribute("data-funcion-id"); 

    fetch("./apis/apib.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ recintoId: recintoId, funcionId: funcionId })
    })
    .then(response => response.json()) // Convertimos la respuesta a JSON
    .then(data => {
        console.log(data); // Para ver los datos en la consola

        if (data.success) {
            let zonaSelect = document.getElementById("zona");
            zonaSelect.innerHTML = '<option value="">Seleccionar Asiento</option>'; // Limpiar opciones anteriores

            data.asientos.forEach(asiento => {
                let option = document.createElement("option");
                option.value=asiento.zona;
                option.setAttribute("data-precio", asiento.precio);
                option.textContent = `Tipo de Asiento: ${asiento.nombre} - Precio de asiento: ${asiento.precio} - Zona: ${asiento.zona}`;
                zonaSelect.appendChild(option);
            });
        } else {
            console.error("Error en la respuesta del servidor");
        }
    })
    .catch(error => console.error("Error al cargar asientos:", error));
});
        const eventoSelect = document.getElementById("evento");
        const recintoSelect = document.getElementById("recinto");
        const zonaSelect = document.getElementById("zona");

        // Cargar recintos según el evento seleccionado
      
       
       // Manejo del formulario
document.getElementById("boletoForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const eventoSelect = document.getElementById("evento");
    const recintoSelect = document.getElementById("recinto");
    const zonaSelect = document.getElementById("zona");
    const cantidadInput = document.getElementById("cantidad");
    const metodoSelect = document.getElementById("metodo");
    const mensajeDiv = document.getElementById("mensaje");

    const evento = eventoSelect.value;
    const recinto = recintoSelect.value;
    const zona = zonaSelect.value;
    const cantidad = parseInt(cantidadInput.value, 10);
    const metodo = metodoSelect.value;

    const selectedOption = zonaSelect.options[zonaSelect.selectedIndex];
    const precio = selectedOption.getAttribute("data-precio");

    console.log(precio)
    if (!evento || !recinto || !zona || !cantidad || cantidad <= 0) {
        mensajeDiv.textContent = "⚠️ Todos los campos son obligatorios y la cantidad debe ser mayor a 0.";
        mensajeDiv.style.color = "red";
        return;
    }
    const boletos = {
        evento_id: evento,
        recinto_id: recinto,
        zona: zona,
        metodo: metodo,
        cantidad: cantidad,
        precio: precio // Agregar el precio del asiento seleccionado
    };

    fetch("./apis/apic.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ boletos })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            mensajeDiv.textContent = "✅ Boletos registrados.";
            mensajeDiv.style.color = "green";
            document.getElementById("boletoForm").reset();
            recintoSelect.innerHTML = '<option value="">Seleccione un recinto</option>';
            zonaSelect.innerHTML = '<option value="">Seleccionar Asiento</option>';
        } else {
            mensajeDiv.textContent = "⚠️ Error al registrar boletos.";
            mensajeDiv.style.color = "red";
        }
    })
    .catch(error => {
        console.error("Error:", error);
        mensajeDiv.textContent = "❌ Ocurrió un error al procesar la solicitud.";
        mensajeDiv.style.color = "red";
    });
});



// Función para formatear la fecha en Año-Mes-Día Hora:Minutos
function formatFecha(fechaISO) {
    let fecha = new Date(fechaISO);
    let anio = fecha.getFullYear();
    let mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Agregar 0 si es necesario
    let dia = String(fecha.getDate()).padStart(2, "0");
    let horas = String(fecha.getHours()).padStart(2, "0");
    let minutos = String(fecha.getMinutes()).padStart(2, "0");

    return `${anio}-${mes}-${dia} ${horas}:${minutos}`;
}
