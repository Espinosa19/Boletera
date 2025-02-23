<?php
require_once dirname(__DIR__, 3) . '/src/controllers/EventoController.php';
header("Content-Type: application/json"); // Asegura respuesta en JSON

$request_method = $_SERVER['REQUEST_METHOD'];
$json = file_get_contents('php://input');
$datos = json_decode($json, true); // Decodifica el cuerpo JSON en un array
$eventos = new EventoController();
$respuesta="";

switch ($request_method) {
    case "GET":
        $respuesta = $eventos->listarEventos();
        echo json_encode($respuesta);
        break;

    case "POST": 
        if (isset($datos['id'])) { // Si ID está presente, obtener el recinto
            $respuesta = $eventos->verEvento($datos['id']);
        } else { 
            if (!isset($datos['nombre'], $datos['descripcion'], $datos['recintos'])) {
                echo json_encode(['error' => 'Faltan datos']);
                exit();
            }

            // Si no hay imagen, asigna un valor predeterminado o null
            $imagen = isset($datos['imagen']) ? $datos['imagen'] : null;

            // Verifica si existen funciones dentro del recinto y agrega IDs
            if (isset($datos['recintos']) && is_array($datos['recintos'])) {
                foreach ($datos['recintos'] as $recintoKey => $recinto) {
                    if (isset($recinto['funciones']) && is_array($recinto['funciones'])) {
                        foreach ($recinto['funciones'] as $funcionKey => $funcion) {
                            if (!isset($funcion['id'])) {
                                $datos['recintos'][$recintoKey]['funciones'][$funcionKey]['id'] = generarIdUnico();
                            }
                        }
                    }
                }
            }

            // Llama al controlador para crear el evento con los datos actualizados
            $respuesta = $eventos->crear($datos['nombre'], $datos['descripcion'], $imagen, $datos['recintos']);
        }
        echo json_encode($respuesta);
        break;

    case "PUT":
        // Asegúrate de que los datos estén en formato JSON
        if (!isset($datos['_id'], $datos['nombre'], $datos['descripcion'], $datos['recintos'])) {
            echo json_encode(['error' => 'Faltan datos']);
            http_response_code(400);
            exit();
        }

        if (isset($datos['_id']) && isset($datos['_id']['$oid'])) {
            $id = $datos['_id']['$oid'];  // Extraemos el valor del ObjectId
        } else {
            $id = null;  // Si no está presente, establecemos null
        }
        
        if (isset($datos['recintos']) && is_array($datos['recintos'])) {
            foreach ($datos['recintos'] as $recintoKey => $recinto) {
                if (isset($recinto['funciones']) && is_array($recinto['funciones'])) {
                    foreach ($recinto['funciones'] as $funcionKey => $funcion) {
                        if (!isset($funcion['id'])) {
                            $datos['recintos'][$recintoKey]['funciones'][$funcionKey]['id'] = generarIdUnico();
                        }
                    }
                }
            }
        }
        $imagen = isset($datos['imagen']) ? $datos['imagen'] : null;

        // Llama al controlador para actualizar el evento
        $respuesta = $eventos->actualizarEvento($id, $datos['nombre'], $datos['descripcion'], $imagen, $datos['recintos']);
        echo json_encode($respuesta);
        break;

    case "DELETE":
        // Para DELETE, se espera que el ID esté presente
        if (!isset($datos['id'])) {
            echo json_encode(['error' => 'Falta el ID']);
            http_response_code(400);
            exit();
        }

        // Llama al controlador para eliminar el evento
        $respuesta = $eventos->eliminarEvento($datos['id']);
        echo json_encode($respuesta);
        break;

    default:
        echo json_encode(['error' => 'Método no permitido']);
        http_response_code(405);
        break;
}

// Función para generar un ID único de 24 caracteres hexadecimales
function generarIdUnico() {
    return bin2hex(random_bytes(12));
}

?>
