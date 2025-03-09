<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
require_once dirname(__DIR__, 3) . '/src/controllers/CompraController.php';
require_once dirname(__DIR__, 3) . '/src/controllers/TokenController.php';
require_once dirname(__DIR__, 3) . '/src/controllers/AsientoController.php';

$tokenController = new TokenController();
$boletoController = new CompraController();
$asientoController = new AsientoController();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos"]);
            exit;
        }
        foreach($data as $da){
            $reservado="Taquilla";
            $estatus = $boletoController->crearBoleto($da,$reservado);
            if($estatus){
                echo json_encode(["status" =>$estatus]);
                exit();
            }else{
                echo json_encode(["status" =>false]);
                exit();
            }
        }
        break;

    case 'GET':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID requerido"]);
            exit;
        }
        $boleto = $boletoModel->obtenerBoleto($_GET['id']);
        if ($boleto) {
            echo json_encode($boleto);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Boleto no encontrado"]);
        }
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $data);
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID requerido"]);
            exit;
        }
        if ($boletoModel->actualizarBoleto($_GET['id'], $data)) {
            echo json_encode(["message" => "Boleto actualizado"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar"]);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID requerido"]);
            exit;
        }
        if ($boletoModel->eliminarBoleto($_GET['id'])) {
            echo json_encode(["message" => "Boleto eliminado"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
