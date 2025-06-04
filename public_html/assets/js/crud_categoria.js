document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.getElementById("add-categoria");
    const form = document.getElementById("categoria-form");
    const cancelBtn = document.getElementById("cancel");
    const container = document.querySelector("#categorias-container");
    const submitBtn = document.getElementById("submit-button");
    const containerSub = document.getElementById('subcategorias-container');
    const addButton = document.getElementById('agregar-subcategoria');
    const delete_categoria = document.querySelectorAll(".delete-categoria");
    let editandoId = null; // Saber si estamos editando

    // Agregar nueva subcategoría
    addButton.addEventListener('click', () => {
        const nuevaSub = document.createElement('div');
        nuevaSub.classList.add('subcategoria');
        nuevaSub.innerHTML = `
            <label>Nombre de la Subcategoría:</label>
            <input type="text" name="subnombre[]" required>
            <button type="button" class="remove-subcategoria">Eliminar Subcategoría</button>
        `;
        containerSub.appendChild(nuevaSub);
    });

    containerSub.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-subcategoria')) {
            const subcategorias = containerSub.querySelectorAll('.subcategoria');
            if (subcategorias.length > 1) {
                e.target.parentElement.remove();
            } else {
                alert('Debe quedar al menos una subcategoría.');
            }
        }
    });

    addBtn.addEventListener("click", function () {
        form.reset();
        containerSub.innerHTML = '';
        addButton.click(); // Añadir al menos una subcategoría
        editandoId = null; // Modo crear
        form.style.display = "block";
        container.style.display = "none";
        addBtn.style.display = "none";
    });

    cancelBtn.addEventListener("click", function () {
        form.reset();
        editandoId = null;
        form.style.display = "none";
        addBtn.style.display = "block";
        container.style.display = "block";
    });

    // Eliminar categoría
    delete_categoria.forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            if (confirm("¿Estás seguro de que deseas eliminar esta categoría?")) {
                fetch(`./apis/apicat.php`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert("Categoría eliminada correctamente.");
                        location.reload(); // Recargar la página para reflejar los cambios
                    } else {
                        alert("Error al eliminar la categoría: " + (result.message || "Error desconocido."));
                    }
                })
                .catch(error => {
                    console.error("Error al eliminar:", error);
                    alert("Ocurrió un error al eliminar la categoría.");
                });
            }
        });
    });
    // Guardar (crear o editar)
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const nombre = document.getElementById("nombre").value.trim();
        const descripcion = document.getElementById("descripcion").value.trim();
        const estado = document.getElementById("estado").value;

        const subcategoriasInputs = document.querySelectorAll('input[name="subnombre[]"]');
        const subcategorias = Array.from(subcategoriasInputs)
            .map(input => input.value.trim())
            .filter(val => val !== '');

        if (!nombre || !descripcion || !estado || subcategorias.length === 0) {
            alert("Por favor, completa todos los campos y agrega al menos una subcategoría.");
            return;
        }

        const datos = {
            id: editandoId, // Si es null, el backend lo puede ignorar
            nombre: nombre,
            descripcion: descripcion,
            estado: estado,
            subcategoria: subcategorias
        };
        metodo= editandoId ? "PUT" : "POST"; // Determinar el método según si estamos editando o creando

        fetch("./apis/apicat.php", {
            method: metodo,
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(editandoId ? "Categoría actualizada correctamente." : "Categoría guardada correctamente.");
                form.reset();
                editandoId = null;
                form.style.display = "none";
                addBtn.style.display = "block";
                container.style.display = "block";
                location.reload();
            } else {
                alert("Error: " + (result.message || "Error desconocido."));
            }
        })
        .catch(error => {
            console.error("Error al enviar:", error);
            alert("Ocurrió un error al enviar los datos.");
        });
    });

    // Editar categoría
    document.querySelectorAll(".edit-categoria").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const id = this.dataset.id;

            fetch(`./apis/apicat.php`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ id: id}) // Enviar el ID para obtener los datos
                }
            )
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const cat = data.categoria;
                        document.getElementById("nombre").value = cat.nombre;
                        document.getElementById("descripcion").value = cat.descripcion;
                        document.getElementById("estado").value = cat.estado;

                        containerSub.innerHTML = '';
                        (cat.subcategorias || []).forEach(sub => {
                            const nuevaSub = document.createElement('div');
                            nuevaSub.classList.add('subcategoria');
                            nuevaSub.innerHTML = `
                                <label>Nombre de la Subcategoría:</label>
                                <input type="text" name="subnombre[]" value="${sub}" required>
                                <button type="button" class="remove-subcategoria">Eliminar Subcategoría</button>
                            `;
                            containerSub.appendChild(nuevaSub);
                        });

                        if (containerSub.children.length === 0) {
                            addButton.click();
                        }

                        editandoId = id;

                        form.style.display = "block";
                        container.style.display = "none";
                        addBtn.style.display = "none";
                    } else {
                        alert("Error al obtener los datos: " + (data.message || "Desconocido"));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Ocurrió un error al cargar la categoría.");
                });
        });
    });

});
