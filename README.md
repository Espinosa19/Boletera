Descripción del Repositorio — Sistema Administrativo de Boletera

ste repositorio contiene la implementación del módulo administrativo de un sistema para boletera, que permite gestionar todos los aspectos relacionados con la venta y administración de boletos para eventos. El sistema está diseñado con una arquitectura basada en el patrón MVC (Modelo-Vista-Controlador) y utiliza MongoDB como base de datos.

Funcionalidades Principales
El administrador puede realizar operaciones completas de CRUD (Crear, Leer, Actualizar, Eliminar) en varias entidades clave, entre ellas:

Eventos: Gestión de eventos disponibles, incluyendo detalles como fecha, hora, descripción, y estado.

Asientos: Control y administración de los asientos disponibles para cada evento, permitiendo asignar y liberar espacios.

Tipos de Asientos: Clasificación de asientos según características (por ejemplo, VIP, general, preferente) y sus costos asociados.

Recintos: Manejo de los lugares donde se realizan los eventos, con información detallada para su correcta identificación y logística.

Boletos: Registro y administración de los boletos emitidos, incluyendo control de estados como vendidos, reservados o cancelados.

Arquitectura y Comunicación
El sistema está desarrollado siguiendo el patrón MVC:

Modelo: Representa la estructura de los datos en MongoDB para cada entidad (Eventos, Asientos, etc.).

Vista: Interfaz administrativa (puede ser web o API REST) que permite a los usuarios administrar la boletera.

Controlador: Gestiona la lógica de negocio y coordina la interacción entre la vista y el modelo.

Cada módulo CRUD se expone a través de APIs RESTful, que permiten la comunicación entre el frontend (interfaz administrativa o cliente) y el backend (lógica de programación). Estas APIs gestionan las operaciones solicitadas y se comunican directamente con la base de datos MongoDB a través de su URI de conexión.
