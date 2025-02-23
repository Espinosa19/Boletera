<?php
require_once dirname(__DIR__, 3) . '/src/controllers/TipoAsientoController.php';
require_once dirname(__DIR__, 3) . '/src/controllers/RecintoController.php';
header('Content-Type: application/json');

$controller = new TipoAsientoController();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($controller->obtenerTipoAsientoPorId($_GET['id']));
        } else {
            echo json_encode($controller->obtenerTiposAsientos());
        }
        break;
    
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        echo json_encode($controller->crearTipoAsiento($data));
        break;

        case 'PUT':
            $data = json_decode(file_get_contents("php://input"), true);
        
            if (!$data || !isset($data['id'])) {
                echo json_encode(['error' => 'ID no proporcionado']);
                exit; // Detener ejecución para evitar respuestas adicionales
            }
        
            $resultado = $controller->actualizarTipoAsiento($data['id'], $data);
            echo json_encode($resultado); // Solo una respuesta JSON
            break;
        

        case 'DELETE':
            $data = json_decode(file_get_contents("php://input"), true); // Leer JSON
            if (isset($data['id'])) {
                echo json_encode($controller->eliminarTipoAsiento($data['id']));
            } else {
                echo json_encode(['error' => 'ID no proporcionado']);
            }
            break;
        
    default:
        echo json_encode(['error' => 'Método no soportado']);
}
?>
