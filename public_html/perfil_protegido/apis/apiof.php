<?php
require_once dirname(__DIR__, 3) . '/src/controllers/obtenerFuncionController.php';
$funcion=new obtenerFuncionController();
$json = file_get_contents('php://input');
$datos = json_decode($json, true); // Decodifica el cuerpo JSON en un array
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['recinto_id'])) {
    echo json_encode($funcion->getFuncionesYZonas($_GET['recinto_id']));
} else {
    // Respuesta de error si el método no es GET o no se proporcionó el ID
    echo json_encode(['error' => 'Método no permitido o falta parametro recinto_id']);
}
?>
