<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // CORS
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
require_once dirname(__DIR__, 3) . '/src/controllers/CategoriaController.php'; 

$categoriaController = new CategoriaController();

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
                $respuesta = $categoriaController->obtenerCategoriaPorId($id);
                if (!$respuesta) {
                    respond(false, "Categoría no encontrada");
                }
                echo json_encode([
                    'success' => true,
                    'message' => "Categoría encontrada",
                    'categoria' => $respuesta
                ]);
            } else {
               
                if (
                    empty($input['nombre']) ||
                    empty($input['descripcion']) ||
                    empty($input['estado']) ||
                    !isset($input['subcategoria']) || !is_array($input['subcategoria']) || count($input['subcategoria']) === 0
                ) {
                    respond(false, "Datos incompletos o subcategorías inválidas");
                }

                $nombre = htmlspecialchars(trim($input['nombre']));
                $descripcion = htmlspecialchars(trim($input['descripcion']));
                $estado = htmlspecialchars(trim($input['estado']));
                // Limpiamos cada subcategoría
                $subcategorias = array_map(function($sub){
                    return htmlspecialchars(trim($sub));
                }, $input['subcategoria']);

                $respuesta = $categoriaController->agregarCategoria($nombre, $descripcion, $estado, $subcategorias);

                if ($respuesta) {
                    respond(true, "Categoría agregada correctamente");
                } else {
                    respond(false, "Error al agregar la categoría");
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
                empty($input['descripcion']) ||
                empty($input['estado']) ||
                !isset($input['subcategoria']) || !is_array($input['subcategoria']) || count($input['subcategoria']) === 0
            ) {
                respond(false, "Datos incompletos o subcategorías inválidas");
            }
            $id = $input['id'];

            $nombre = htmlspecialchars(trim($input['nombre']));
            $descripcion = htmlspecialchars(trim($input['descripcion']));
            $estado = htmlspecialchars(trim($input['estado']));
            $subcategorias = array_map(function($sub){
                return htmlspecialchars(trim($sub));
            }, $input['subcategoria']);

            $actualizado =$categoriaController->actualizarCategoria($id, $nombre, $descripcion, $estado, $subcategorias);

            if ($actualizado) {
                respond(true, "Categoría actualizada correctamente");
            } else {
                respond(false, "Error al actualizar la categoría");
            }
            break;

        case 'DELETE':
            // Eliminar una categoría
            
            if (!$input || !isset($input['id']) || empty($input['id'])) {
                respond(false, "ID no especificado para eliminar");
            }
            $id = $input['id'];
            $stmt = $categoriaController->eliminarCategoria($id);
            if (!$stmt) {
                respond(false, "Error al eliminar la categoría");
            }
            respond(true, "Categoría eliminada correctamente");
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
