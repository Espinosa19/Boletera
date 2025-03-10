<?php
require_once dirname(__DIR__, 3) . '/src/controllers/obtenerFuncionController.php';
require_once dirname(__DIR__, 3) . '/src/controllers/EventoController.php';
require_once dirname(__DIR__, 3) . '/vendor/autoload.php'; // Asegúrate de que tienes MongoDB en Composer

use MongoDB\BSON\ObjectId;

$funcion = new obtenerFuncionController();
$evento = new EventoController();

// Verificar que la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Método no permitido
    echo json_encode(['error' => 'Método no permitido. Solo se permiten solicitudes GET.']);
    exit;
}

// Validar y sanitizar parámetros
$recinto_id = isset($_GET['recinto_id']) ? trim($_GET['recinto_id']) : null;
$evento_id = isset($_GET['evento_id']) ? trim($_GET['evento_id']) : null;

// Función para validar ObjectId de MongoDB
function esObjectIdValido($id) {
    try {
        new ObjectId($id); // Intenta crear un ObjectId, si falla lanza excepción
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Si se proporciona recinto_id y evento_id, obtener funciones y zonas
if ($recinto_id && $evento_id) {
    if (!esObjectIdValido($recinto_id) || !esObjectIdValido($evento_id)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'ID de recinto o evento no válido.']);
        exit;
    }
    echo json_encode($funcion->getFuncionesYZonas(new ObjectId($recinto_id), new ObjectId($evento_id)));
    exit;
}

// Si solo se proporciona evento_id, obtener eventos por ID
if ($evento_id) {
    if (!esObjectIdValido($evento_id)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'ID de evento no válido.']);
        exit;
    }
    echo json_encode($evento->obtenerEventosPorId(new ObjectId($evento_id), false));
    exit;
}

// Si no se cumplen las condiciones anteriores, devolver error
http_response_code(400); // Bad Request
echo json_encode(['error' => 'Faltan parámetros requeridos (recinto_id y/o evento_id).']);
exit;
?>
