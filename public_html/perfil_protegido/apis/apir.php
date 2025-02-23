<?php 
require_once dirname(__DIR__, 3) . '/src/controllers/RecintoController.php';

header("Content-Type: application/json"); // Asegura respuesta en JSON

$request_method = $_SERVER['REQUEST_METHOD'];
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

$recintos = new RecintoController();
$respuesta = "";

switch ($request_method) {
    case "POST":
        if (isset($datos['id'])) { // Si ID está presente, obtener el recinto
            $respuesta = $recintos->obtenerRecintoPorId($datos['id']);
        } else { 
            // Validar datos requeridos para crear un recinto
            if (!isset($datos['nombre'], $datos['ciudad'], $datos['estado'], $datos['capacidad'], $datos['activo'], $datos['mapa_svg_url'], $datos['zonas'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Faltan datos']);
                exit();
            }
            // Crear recinto
            $respuesta = $recintos->crearRecinto($datos);
        }
        http_response_code(200);
        echo json_encode($respuesta);
        break;
    
    case "PUT":
        if (!isset($datos['id'], $datos['nombre'], $datos['ciudad'], $datos['estado'], $datos['capacidad'], $datos['activo'], $datos['mapa_svg_url'], $datos['zonas'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos']);
            exit();
        }

        $respuesta = $recintos->actualizarRecinto($datos['id'], $datos);
        http_response_code(200);
        echo json_encode($respuesta);
        break;

    case "DELETE":
        if (!isset($datos['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta el ID']);
            exit();
        }
        // Eliminar recinto
        $respuesta = $recintos->eliminarRecinto($datos['id']);
        http_response_code(200);
        echo json_encode($respuesta);
        break;
    case "GET":
        $respuesta=$recintos->obtenerRecintos();
        echo json_encode($respuesta);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>
