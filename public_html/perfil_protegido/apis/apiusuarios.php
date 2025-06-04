<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // CORS
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
require_once dirname(__DIR__, 3) . '/src/controllers/UsuarioController.php'; 

$usuarioController = new UsuarioController();

// Conexión a la base de datos (ajusta según tu configuración)
// Método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Leer datos JSON si no es GET
$input = json_decode(file_get_contents("php://input"), true);

// Función para responder
function respond($success, $message = '', $data= []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Función para agregar categoría (simulación, debes ajustar según tu lógica)


try {
    switch ($method) {
        case 'POST':
            if (isset($input['id'])) {
                // Si se proporciona un ID, se asume que es una actualización
                $id = $input['id'];
                if (!$id) {
                    respond(false, "ID no especificado para agregar");
                }
                $respuesta = $usuarioController->obtenerUsuarioPorId($id);
                if (!$respuesta) {
                    respond(false, "Usuario no encontrado");
                }
                echo json_encode([
                    'success' => true,
                    'message' => "Usuario encontrado",
                    'usuario' => $respuesta
                ]);
            } else {
               
                if (
                    empty($input['nombre']) ||
                    empty($input['email']) ||
                    empty($input['telefono']) ||
                    empty($input['role'])
                ) {
                    respond(false, "Datos incompletos o subcategorías inválidas");
                }

                $nombre = htmlspecialchars(trim($input['nombre']));
                $email = htmlspecialchars(trim($input['email']));
                $telefono = htmlspecialchars(trim($input['telefono']));
                $role = htmlspecialchars(trim($input['role']));

                $respuesta = $usuarioController->agregarUsuario($nombre, $email, $telefono, $role);

                if ($respuesta) {
                    respond(true, "Usuario agregado correctamente");
                } else {
                    respond(false, "Error al agregar el usuario");
                }
            }
            break;

        case 'PUT':
            

            if (!$input) {
                respond(false, "Faltan datos para actualizar");
            }

            if (
                !isset($input['id']) || empty($input['id']) ||
                empty($input['nombre']) ||
                empty($input['email']) ||
                empty($input['telefono']) ||
                empty($input['role'])
            ) {
                respond(false, "Datos incompletos o subcategorías inválidas");
            }
            $id = $input['id'];

            $nombre = htmlspecialchars(trim($input['nombre']));
            $email = htmlspecialchars(trim($input['email']));
            $telefono = htmlspecialchars(trim($input['telefono']));
            $role = htmlspecialchars(trim($input['role']));
            $actualizado =$usuarioController->actualizarUsuario($id, $nombre, $email, $telefono, $role);

            $actualizado =$usuarioController->actualizarUsuario($id, $nombre, $email, $telefono, $role);

            if ($actualizado) {
                respond(true, "Usuario actualizado correctamente");
            } else {
                respond(false, "Error al actualizar el usuario");
            }
            break;

        case 'DELETE':
            // Eliminar un usuario

            if (!$input || !isset($input['id']) || empty($input['id'])) {
                respond(false, "ID no especificado para eliminar");
            }
            $id = $input['id'];
            $stmt = $usuarioController->eliminarUsuario($id);
            if (!$stmt) {
                respond(false, "Error al eliminar el usuario");
            }
            respond(true, "Usuario eliminado correctamente");
            break;

       
        case 'OPTIONS':
            // Pre-flight CORS check
            http_response_code(200);
            exit;

        default:
            respond(false, "Método HTTP no permitido");
    }
} catch (Exception $e) {
    respond(false, "Error: " . $e->getMessage());
}
