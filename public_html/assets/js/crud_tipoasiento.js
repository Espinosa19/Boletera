document.getElementById('nuevoAsientoBtn').addEventListener('click', function() {
    console.log("Abriendo modal de nuevo asiento");
    document.querySelector('.modal-overlay').classList.add('open');
    nuevoAsiento(); // Limpiar campos para nuevo asiento
});

// Cerrar el modal
document.getElementById('cerrarModal').addEventListener('click', function() {
    document.querySelector('.modal-overlay').classList.remove('open');
});

// Guardar o editar asiento
document.getElementById("guardarBtn").addEventListener("click", async function() {
    const id = document.getElementById('editarBtn').value;
    const method = id ? 'PUT' : 'POST';
    const url = `./apis/apita.php`;

    const response = await fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json", // Enviar como JSON
        },
        body: JSON.stringify({
            id :id ? id : '',
            nombre: document.getElementById('nombre').value,
            precio: parseFloat(document.getElementById('precio').value),
            creado_por: parseInt(document.getElementById('creadoPor').value),
            activo: document.getElementById('activo').value === 'true'
        })
    });

    if (!response.ok) throw new Error('Error al cargar el evento');
    const tipo = await response.json();
    // Mostrar el objeto recibido
    console.log(tipo);

    // Cerrar modal y recargar los datos
    document.querySelector('.modal-overlay').classList.remove('open');
});

// Editar asiento
document.querySelectorAll("#editarBtn").forEach(element => {
    element.addEventListener("click", async function() {
        const id = this.getAttribute("data-id"); // Obtén el id desde el atributo data-id
        const res = await fetch(`./apis/apita.php?id=${id}`);
        const asiento = await res.json();
        console.log(asiento);

        document.getElementById('asientoId').value = asiento._id.$oid;
        document.getElementById('nombre').value = asiento.nombre;
        document.getElementById('precio').value = asiento.precio;
        document.getElementById('creadoPor').value = asiento.creado_por;
        document.getElementById('activo').value = asiento.activo ? 'true' : 'false';

        document.querySelector('.modal-overlay').classList.add('open');
    });
});

// Eliminar asiento
document.querySelectorAll("#eliminarBtn").forEach(item => {
    item.addEventListener("click", async function() {
        const id = this.getAttribute("data-id"); // Obtén el id desde el atributo data-id
        console.log(id)
        const confirmDelete = confirm('¿Estás seguro de que deseas eliminar este asiento?');
        if (confirmDelete) {
            const response=await fetch('./apis/apita.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({id})
            });
            if (!response.ok) throw new Error('Error al cargar el evento');
            const tipo = await response.text();
            // Mostrar el objeto recibido
            console.log(tipo);
        }
    });
});

// Función para limpiar los campos y preparar el modal para un nuevo asiento
function nuevoAsiento() {
    document.getElementById('asientoId').value = '';
    document.getElementById('nombre').value = '';
    document.getElementById('precio').value = '';
    document.getElementById('creadoPor').value = '';
    document.getElementById('activo').value = 'true';
}
