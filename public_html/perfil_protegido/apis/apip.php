<?php
require_once dirname(__DIR__, 3) . '/src/controllers/PagoController.php';
header("Content-Type: application/json"); // Asegura respuesta en JSON

$request_method = $_SERVER['REQUEST_METHOD'];
$json = file_get_contents('php://input');
$datos = json_decode($json, true); // Decodifica el cuerpo JSON en un array
$pagos = new PagoController();
$respuesta="";

switch ($request_method) {
    case "GET":
        $respuesta = $pagos->listarPagos();
        echo json_encode($respuesta);
        break;
    case "POST":
        if (isset($datos['id'])) { // Si ID está presente, obtener el pago
            $respuesta = $pagos->verPago($datos['id']);
        } else { 
            if (!isset($datos['usuario_id'], $datos['tipo_transaccion'], $datos['descripcion'], $datos['fecha'])) {
                echo json_encode(['error' => 'Faltan datos']);
                exit();
            }

            // Llama al controlador para crear el pago con los datos decodificados
            $respuesta = $pagos->crear($datos['usuario_id'], $datos['tipo_transaccion'], $datos['descripcion'], $datos['fecha']);
        }
        echo json_encode($respuesta);
        break;

    case "PUT":
        // Asegúrate de que los datos estén en formato JSON
        if (!isset($datos['_id'], $datos['usuario_id'], $datos['tipo_transaccion'], $datos['descripcion'], $datos['fecha'])) {
            echo json_encode(['error' => 'Faltan datos']);
            http_response_code(400);
            exit();
        }
        if (isset($datos['_id']) && isset($datos['_id']['$oid'])) {
            $id = $datos['_id']['$oid'];  // Extraemos el valor del ObjectId
        } else {
            $id = null;  // Si no está presente, establecemos null
        }
        
        // Llama al controlador para actualizar el pago
        $respuesta = $pagos->actualizarPago($id, $datos['usuario_id'], $datos['tipo_transaccion'], $datos['descripcion'], $datos['fecha']);
        echo json_encode($respuesta);
        break;

    case "DELETE":
        // Para DELETE, se espera que el ID esté presente
        if (!isset($datos['id'])) {
            echo json_encode(['error' => 'Falta el ID']);
            http_response_code(400);
            exit();
        }

        // Llama al controlador para eliminar el pago
        $respuesta = $pagos->eliminarPago($datos['id']);
        echo json_encode($respuesta);
        break;

    default:
        echo json_encode(['error' => 'Método no permitido']);
        http_response_code(405);
        break;
}
?>
