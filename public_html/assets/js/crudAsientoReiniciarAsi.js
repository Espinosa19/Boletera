
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

function obtenerDatosAsientos() {
    const datos = [];

    const recintoId = document.getElementById("recinto2")?.value.trim();
    const select = document.getElementById("recinto2"); // Reemplaza "miSelect" con el ID real
    const opcionSeleccionada = select.options[select.selectedIndex];
    const funcion = opcionSeleccionada.getAttribute("data-funcion");
    const e = document.getElementById("evento").value.trim();
    if ( !recintoId || !funcion) {
        alert("Por favor, completa todos los campos obligatorios (tipo de asiento, recinto y función).");
        return false;
    }
    // Si todo es válido, agregar los datos al arreglo final
    datos.push({
        evento: e,
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
        const response = await fetch('./apis/apiare.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dataAsientos),
        });

        const resultado = await response.json();
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