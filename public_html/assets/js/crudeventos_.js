
let eventoEnEdicion = null;

const selectContainer = document.querySelector('select[name="recinto_id[]"]');
const selectOriginal = selectContainer.innerHTML;
function mostrarFormularioCrear() {
     document.querySelector('#titulo-re').hidden = false;
    
         document.getElementById('formulario').style.display = 'block';
         document.getElementById('titulo-formulario').innerText = 'Agregar Evento';
         document.getElementById('nombre').value = '';
         document.getElementById('descripcion').value = '';
         document.getElementById('eventos').style.display = 'none';
         const re_hea = document.querySelector('.recinto-header');
         
         re_hea.hidden=false;
         
          const nuevoSelect = document.querySelector('#sele_recinto');
        if (!nuevoSelect) {
             const sel = document.createElement('select');
             sel.id = 'sele_recinto'; // Agregarle un id
             sel.name = 'recinto_id[]'; // Agregar el atributo name
             sel.innerHTML = selectOriginal;
             re_hea.append(sel);
         }
         // Limpiar funciones existentes
         const funcionesContainer = document.querySelector('.funciones-container');
         funcionesContainer.innerHTML = `
             <div class="funcion">
                 <label>Fecha de Inicio:</label>
                 <input type="datetime-local" name="fecha_inicio[]" required>
                 <label>Fecha de Fin:</label>
                 <input type="datetime-local" name="fecha_fin[]" required>
                 <button type="button" class="eliminar-funcion">Eliminar Función</button>
             </div>
         `;
         eventoEnEdicion = null;



         // Añadir eventos a los botones de eliminar función
     }function mostrarFormularioActualizar(id, nombre,categoria,descripcion, recintos,recomendado) {
        document.getElementById("primer-button").style.display="none"
 document.getElementById('formulario').hidden = false;
 document.getElementById("formulario").style.display = 'flex';
 document.querySelector('.recinto-header').hidden = true;
 document.querySelector('#titulo-re').hidden = true;
 document.getElementById('titulo-formulario').innerText = 'Modificar Evento';
 document.getElementById('nombre').value = nombre;
 document.getElementById('categoria').value=categoria;
 document.getElementById('descripcion').value = descripcion;
 document.getElementById("recomendado").checked = recomendado ;
 const funcionesContainer = document.querySelector('.funciones-container');
 funcionesContainer.innerHTML = '';

 
 // Limpiar el select original, si existe
 const selectEstatico = document.querySelector('select[name="recinto_id[]"]');
 if (selectEstatico) {
     selectEstatico.remove(); 
 }

 recintos.forEach(recinto => {
     const recintoId = recinto.id;
     const funciones = recinto.funciones;
     // Comprobar si el recinto es el que debe estar seleccionado

     // Crear un select clonado para el recinto
     const selectClonado = document.createElement('select');
     selectClonado.name = "recinto_id[]"; // Cambiar el nombre del select clonado
     selectClonado.required = true; // Hacer el select requerido

     // Agregar opciones al select clonado
     selectClonado.innerHTML = `
         <option data-name="${recinto.nombre}" value="${recintoId}">${recinto.nombre}</option>
         ${selectOriginal}
     `;

     // Crear el contenedor del recinto
     const recintoContainer = document.createElement('div');
     recintoContainer.classList.add('funciones');

     recintoContainer.innerHTML = `
         <label>Seleccione un Recinto:</label>
         <button type="button" class="eliminar-recinto" onclick="eliminarRecinto(this)">Eliminar Recinto</button>
         <h3>Funciones del Recinto</h3>
         <div class="funciones-container"></div>
         <button type="button" onclick="agregarFuncion2(this)">Agregar Función</button>
     `;

     // Agregar el select clonado al contenedor del recinto
     recintoContainer.insertAdjacentElement('afterbegin', selectClonado);

     // Agregar funciones al contenedor del recinto
     const funcionesContainerRecinto = recintoContainer.querySelector('.funciones-container');
     funciones.forEach(funcion => {
        
        const fechaInicio = new Date(funcion.fecha_inicio);
        const fechaFin = new Date(funcion.fecha_fin);

         
         if (isNaN(fechaInicio.getTime()) || isNaN(fechaFin.getTime())) {
             console.error('Fecha de inicio o fin no válidas', fechaInicio, fechaFin);
             return '';
         }
         
         const fechaInicioISO = fechaInicio.toISOString().slice(0, 16);
         const fechaFinISO = fechaFin.toISOString().slice(0, 16);
         
         funcionesContainerRecinto.innerHTML += `
             <div class="funcion">
                 <div>
                 <label>Fecha de Inicio:</label>
                 <input type="datetime-local" name="fecha_inicio[]" value="${fechaInicioISO}" required>
                 </div>
                 <div>

                 <label>Fecha de Fin:</label>
                 <input type="datetime-local" name="fecha_fin[]" value="${fechaFinISO}" required>
                 </div>

                 <button type="button" class="eliminar-funcion" onclick="eliminarFuncion(this)">Eliminar Función</button>
             </div>
         `;
     });

     funcionesContainer.appendChild(recintoContainer);
 });

 eventoEnEdicion = id;
 document.getElementById('eventos').style.display = 'none';
}

async function editarEvento(id) {
 document.getElementById('formulario').style.display = 'block';
 document.getElementById('label-principal').style.display = 'none';
 document.getElementById('button-delete-principal').style.display = 'none';

 const response = await fetch('./apis/apie.php',{
    method:"POST",
    headers: {
        "Content-Type": "application/json", // Enviar como JSON
    },
    body: JSON.stringify({id})

 });
 if (!response.ok) throw new Error('Error al cargar el evento');
    const evento = await response.json();
    // Mostrar el objeto recibido
 mostrarFormularioActualizar(evento._id, evento.nombre,evento.categoria, evento.descripcion, evento.recintos,evento.recomendado|| false);
}
function agregarFuncion(button) {
 const funcionesContainer = button.previousElementSibling;
 const nuevaFuncion = document.createElement('div');
 nuevaFuncion.classList.add('funcion');

 nuevaFuncion.innerHTML = `
     <label>Fecha de Inicio:</label>
     <input type="datetime-local" name="fecha_inicio[]" required>
     <label>Fecha de Fin:</label>
     <input type="datetime-local" name="fecha_fin[]" required>
     <button type="button" class="eliminar-funcion" onclick="eliminarFuncion(this)">Eliminar Función</button>
 `;
 
 funcionesContainer.appendChild(nuevaFuncion);

}async function guardarEvento(event) {
    event.preventDefault();

    // Obtener los valores de los campos del formulario
    const nombre = document.getElementById('nombre').value;
    const categoria = document.getElementById('categoria').value;
    const descripcion = document.getElementById('descripcion').value;
    const imagen = document.getElementById('imagen').files[0]; // Imagen seleccionada
    const recintos = document.querySelectorAll('.recinto-container');
    const re=document.getElementById("recomendado").checked;
    console.log(re)
    const recintosArray = Array.from(recintos).map(recinto => {
        const selectRecinto = recinto.querySelector('select[name="recinto_id[]"]');
        const selectedOption = selectRecinto.options[selectRecinto.selectedIndex]; // Opción seleccionada
        const recintoName = selectedOption.getAttribute("data-name"); // Captura el data-name

        const funciones = recinto.querySelectorAll('.funcion');
        const funcionesArray = Array.from(funciones).map(funcion => ({
            fecha_inicio: funcion.querySelector('input[name="fecha_inicio[]"]').value,
            fecha_fin: funcion.querySelector('input[name="fecha_fin[]"]').value
        }));

        return {
            id: selectRecinto.value,
            nombre: recintoName,
            funciones: funcionesArray
        };
    });

    // Validar los datos antes de enviarlos
    const validacion = validarDatosEvento(nombre, categoria, descripcion, imagen, recintosArray);
    if (validacion !== true) {
        alert(validacion);
        return;
    }

    // Determina si estamos creando o actualizando un evento (POST o PUT)
    const metodo = eventoEnEdicion ? 'PUT' : 'POST';

    const url = './apis/apie.php';

    try {
        const response = await fetch(url, {
            method: metodo,
            headers: {
                "Content-Type": "application/json", // Enviar como JSON
            },
            body: JSON.stringify({
                nombre,
                descripcion,
                imagen: imagen ? imagen.name : null, // Solo enviamos el nombre de la imagen
                recintos: recintosArray,
                categoria,
                recomendado: document.getElementById("recomendado").checked,
                _id: eventoEnEdicion || null // Si estamos editando un evento, añadimos el ID
            })
        });

        if (!response.ok) throw new Error('Error al guardar evento');

        const result = await response.json(); // Asume que el servidor responde con JSON
        console.log('Resultado del servidor:', result);
        if( result.status=="create"){
            alert('Evento creado exitosamente');
            location.reload(); // Recargar la página para ver el nuevo evento
        }else if(result.status=="update"){
            alert('Evento actualizado exitosamente');
            location.reload(); // Recargar la página para ver los cambios
        }
        else{
            throw new Error('Error al guardar evento');
        }
        cancelarFormulario();
    } catch (error) {
        console.error('Error al guardar evento:', error);
        alert('Error al guardar el evento');
    }
}

// 🔹 Método de Validación
function validarDatosEvento(nombre, categoria, descripcion, imagen, recintos) {
    if (!nombre.trim()) return 'El nombre del evento es obligatorio.';
    if (!categoria) return 'Debes seleccionar una categoría.';
    if (!descripcion.trim()) return 'La descripción no puede estar vacía.';
    if (!imagen) return 'Debes seleccionar una imagen para el evento.';
    if (recintos.length === 0) return 'Debes agregar al menos un recinto con funciones.';

    for (let recinto of recintos) {
        for (let funcion of recinto.funciones) {
            const fechaInicio = new Date(funcion.fecha_inicio);
            const fechaFin = new Date(funcion.fecha_fin);
            if (!funcion.fecha_inicio || !funcion.fecha_fin) {
                return `Las fechas de inicio y fin son obligatorias en el recinto "${recinto.nombre}".`;
            }
            if (fechaFin < fechaInicio) {
                return `La fecha de fin en el recinto "${recinto.nombre}" debe ser posterior a la de inicio.`;
            }
        }
    }

    return true; // Datos válidos
}

async function eliminarEvento(id) {
    if (confirm('¿Está seguro de que desea eliminar este evento?')) {
        try {
            const response = await fetch('./apis/apie.php', { 
                method: "DELETE", 
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id }) // 🔹 Enviar el ID como objeto
            });

            if (!response.ok) throw new Error('Error al eliminar el evento');

            alert('Evento eliminado exitosamente');
            cargarEventos(); // 🔹 Llamar la función para actualizar la vista
        } catch (error) {
            console.error('Error al eliminar evento:', error);
            alert('Error al eliminar el evento');
        }
    }
}


 function eliminarFuncion(button) {
     button.parentElement.remove();
 }
 
function agregarRecinto() {
 const container = document.getElementById('recintos-container');

 // Crear el contenedor para el recinto y las funciones
 const conta_fun = document.createElement('div');
 conta_fun.className = "recinto-container"; // Asignar clase

 // Crear el select del recinto (usando el select creado dinámicamente)
 const sel = document.createElement('select');
 sel.name = 'recinto_id[]'; // Atributo name
 sel.innerHTML = selectOriginal; // Usar el contenido del select original
 
 // Construir el encabezado del recinto con plantilla
 const header = `
     <div class="recinto-header">
         <label>Seleccione un Recinto:</label>
         <button type="button" class="eliminar-recinto" onclick="eliminarRecinto(this)">Eliminar Recinto</button>
     </div>
 `;
 
 // Asignar el HTML al contenedor
 conta_fun.innerHTML = header;


 conta_fun.appendChild(sel); // Añadir el select dinámicamente

 // Crear el contenedor para las funciones (fechas de inicio y fin)
 const funcionesContainer = document.createElement('div');
 funcionesContainer.classList.add('funciones-container'); // Añadir clase para estilo

 // Crear la primera función en el nuevo recinto
 const funcionHTML = `
     <div class="funcion">
         <label>Fecha de Inicio:</label>
         <input type="datetime-local" name="fecha_inicio[]" required>
         <label>Fecha de Fin:</label>
         <input type="datetime-local" name="fecha_fin[]" required>
         <button type="button" class="eliminar-funcion" onclick="eliminarFuncion(this)">Eliminar Función</button>
     </div>
 `;
 funcionesContainer.innerHTML = funcionHTML; // Añadir la función inicial

 // Añadir el contenedor de funciones al recinto
 conta_fun.appendChild(funcionesContainer);

 // Botón para añadir nuevas funciones dentro de este recinto
 const agregarFuncionBtn = document.createElement('button');
 agregarFuncionBtn.type = 'button';
 agregarFuncionBtn.innerText = 'Agregar Función';
 agregarFuncionBtn.onclick = function() {
     // Crear y añadir una nueva función
     const nuevaFuncion = document.createElement('div');
     nuevaFuncion.classList.add('funcion');
     nuevaFuncion.innerHTML = `
         <label>Fecha de Inicio:</label>
         <input type="datetime-local" name="fecha_inicio[]" required>
         <label>Fecha de Fin:</label>
         <input type="datetime-local" name="fecha_fin[]" required>
         <button type="button" class="eliminar-funcion" onclick="eliminarFuncion(this)">Eliminar Función</button>
     `;
     funcionesContainer.appendChild(nuevaFuncion); // Añadir la nueva función
 };

 conta_fun.appendChild(agregarFuncionBtn); // Aadir el botón de agregar función

 // Añadir el contenedor completo al contenedor principal
 container.appendChild(conta_fun);
}


function eliminarRecinto(button) {
 // Buscar el contenedor más cercano con la clase 'recinto-container' en lugar de 'funciones-container'
 const recintoContainer = button.closest('.recinto-container');

 if (recintoContainer) {
     recintoContainer.remove();
 } else {
     console.error('Contenedor no encontrado. Asegúrate de que el botón esté dentro de un contenedor con la clase "recinto-container".');
 }
}
function agregarFuncion2(button) {
 // Encuentra el contenedor de recinto correspondiente al botón
 const recintoContainer = button.closest('.recinto-container');
 
 // Verificar que recintoContainer se haya encontrado
 if (!recintoContainer) {
     console.error("Contenedor de recinto no encontrado");
     return;
 }

 // Encuentra el contenedor de funciones dentro del recinto
 const funcionesContainer = recintoContainer.querySelector('.funciones-container');
 
 // Verificar que funcionesContainer se haya encontrado
 if (!funcionesContainer) {
     console.error("Contenedor de funciones no encontrado dentro del recinto");
     return;
 }

 // Crear un nuevo div para la función
 const nuevaFuncion = document.createElement('div');
 nuevaFuncion.classList.add('funcion');

 // Crear el contenido de la nueva función
 nuevaFuncion.innerHTML = `
     <label>Fecha de Inicio:</label>
     <input type="datetime-local" name="fecha_inicio[]" required>
     <label>Fecha de Fin:</label>
     <input type="datetime-local" name="fecha_fin[]" required>
     <button type="button" class="eliminar-funcion" onclick="eliminarFuncion(this)">Eliminar Función</button>
 `;

 // Agregar la nueva función al contenedor de funciones
 funcionesContainer.appendChild(nuevaFuncion);
}

function eliminarFuncion(button) {
 // Elimina la función correspondiente
 button.closest('.funcion').remove();
}

function agregarFuncion(funcionesContainer) {
 const nuevaFuncion = document.createElement('div');
 nuevaFuncion.classList.add('funcion');

 // Crear el HTML para la nueva función
 nuevaFuncion.innerHTML = `
     <label>Fecha de Inicio:</label>
     <input type="datetime-local" name="fecha_inicio[]" required>
     <label>Fecha de Fin:</label>
     <input type="datetime-local" name="fecha_fin[]" required>
     <button type="button" class="eliminar-funcion" onclick="eliminarFuncion(this)">Eliminar Función</button>
 `;

 // Añadir la nueva función al contenedor de funciones
 funcionesContainer.appendChild(nuevaFuncion);
}

function cancelarFormulario() {
 const formulario = document.getElementById('formulario');
 formulario.style.display = 'none'; // Oculta el formulario
 document.getElementById('eventos').style.display = 'block'; // Muestra la lista de eventos
 
 // Limpiar todos los inputs y selects dentro del div formulario
 formulario.querySelectorAll('input, select, textarea').forEach(input => {
     if (input.type === 'checkbox' || input.type === 'radio') {
         input.checked = false;
     } else {
         input.value = '';
     }
 });
 eventoEnEdicion = null;
}


