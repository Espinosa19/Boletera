<?php
require dirname(__DIR__,3) . '/src/controllers/AsientoController.php';
require dirname(__DIR__,3) . '/src/controllers/RecintoController.php';
require dirname(__DIR__,3) . '/src/controllers/TipoAsientoController.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$controller = new AsientoController(); 
$recintoController= new RecintoController();
$tipoController=new TipoAsientoController();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $asiento = $controller->getAsientoById($_GET['id']);
            echo json_encode($asiento);
        } else {
            $asientos = $controller->getAsientos();
            echo json_encode($asientos);
        }
        break;

        case 'POST':
            $datas = json_decode(file_get_contents("php://input"), true);
        
            if (isset($_POST['id'])) {
                
               
            
            } else {
                foreach ($datas as $data) {
                    echo json_encode($data);
                    if (isset($data['tiposAsientos'])) {
                        echo json_encode($controller->insertarAsientosPorTipo($data));
                    } else {
                        echo json_encode($controller->insertarAsiento($data));
                    }
                }
            }
            break;
        
    case 'PUT':
        $controller->updateAsiento();
        break;

    case 'DELETE':
        $controller->deleteAsiento();
        break;

    default:
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
