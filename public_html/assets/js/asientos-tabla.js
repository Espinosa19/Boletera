document.getElementById('searchInput').addEventListener('keyup', function() {
    let searchValue = this.value.toLowerCase();
    let rows = document.querySelectorAll('#asientosTable tr');

    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
    // Seleccionar todos los botones de editar
    document.querySelectorAll("#editar-asiento").forEach(button => {
        button.addEventListener("click", function () {
            let asientoId = this.value;

            fetch("./apis/apia.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ id: asientoId }),
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message); // Mostrar mensaje de respuesta
            })
            .catch(error => console.error("Error:", error));
        });
    });

    // Seleccionar todos los botones de eliminar
    document.querySelectorAll("#eliminar-asiento").forEach(button => {
        button.addEventListener("click", function () {
            let asientoId = this.value;

            if (confirm("¿Estás seguro de eliminar este asiento?")) {
                fetch("./apis/apia.php.php", {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({  id: asientoId }),
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); // Recargar la página para actualizar la lista
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });

