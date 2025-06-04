document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.getElementById("add-user");
    const form = document.getElementById("user-form");
    const cancelBtn = document.getElementById("cancel-user");
    const container = document.getElementById("usuarios-container");
    const submitBtn = document.getElementById("guardar-usuario");

    let editandoId = null;

    // Mostrar formulario para agregar
    addBtn.addEventListener("click", function () {
        document.querySelector("#user-form h2").textContent = "Agregar Usuario";

        form.reset();
        editandoId = null;
        form.style.display = "block";
        container.style.display = "none";
        addBtn.style.display = "none";
    });

    // Cancelar
    cancelBtn.addEventListener("click", function () {
        form.reset();
        editandoId = null;
        form.style.display = "none";
        container.style.display = "block";
        addBtn.style.display = "block";
    });

    // Guardar usuario
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const nombre = document.getElementById("nombre-usuario").value.trim();
        const telefono = document.getElementById("telefono-usuario").value.trim();
        const email = document.getElementById("email-usuario").value.trim();
        const password = document.getElementById("password-usuario").value;
        const role = document.getElementById("role").value;

        if (!nombre || !telefono || !email || (!editandoId && !password) || !role) {
            alert("Por favor, completa todos los campos.");
            return;
        }

        const datos = {
            id: editandoId,
            nombre,
            telefono,
            email,
            password: password || null,
            role
        };

        const metodo = editandoId ? "PUT" : "POST";

        fetch("./apis/apiusuarios.php", {
            method: metodo,
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datos)
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert(editandoId ? "Usuario actualizado." : "Usuario agregado.");
                    location.reload();
                } else {
                    alert("Error: " + (result.message || "Desconocido"));
                }
            })
            .catch(error => {
                console.error("Error al enviar:", error);
                alert("Error al guardar usuario.");
            });
    });

    // Eliminar usuario
    document.querySelectorAll(".delete-usuario").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            if (confirm("¿Deseas eliminar este usuario?")) {
                fetch("./apis/apiusuarios.php", {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ id })
                })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            alert("Usuario eliminado.");
                            location.reload();
                        } else {
                            alert("Error al eliminar usuario: " + (result.message || "Desconocido"));
                        }
                    })
                    .catch(err => {
                        console.error("Error:", err);
                        alert("Ocurrió un error al eliminar.");
                    });
            }
        });
    });

    // Editar usuario
    document.querySelectorAll(".edit-usuario").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;

            fetch("./apis/apiusuarios.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const user = data.usuario;
document.querySelector("#user-form h2").textContent = "Editar Usuario";
                        document.getElementById("nombre-usuario").value = user.nombre;
                        document.getElementById("telefono-usuario").value = user.telefono;
                        document.getElementById("email-usuario").value = user.email;
                        document.getElementById("role").value = user.role;

                        // No rellenamos password por seguridad
                        editandoId = id;

                        form.style.display = "block";
                        container.style.display = "none";
                        addBtn.style.display = "none";
                    } else {
                        alert("Error al obtener datos: " + (data.message || "Desconocido"));
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    alert("Error al cargar usuario.");
                });
        });
    });
});
