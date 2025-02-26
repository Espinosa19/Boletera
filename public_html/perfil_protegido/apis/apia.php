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
        }else if(isset($_GET['id_eli'])){
            $id=$_GET['id_eli'];
            $controller->eliminarAsiento($id);
            header('Location: ../asientos-tabla.php'); // Redirige a la página de asientos

            exit();
        } 
        else {
            $asientos = $controller->getAsientos();
            echo json_encode($asientos);
        }
        exit();

        break;

        case 'POST':
            $datas = json_decode(file_get_contents("php://input"), true);
        
            if (isset($_POST['id']) && isset($_POST['nuevo_estado'])) {
                
                $id = htmlspecialchars($_POST['id']);
                $estado = htmlspecialchars($_POST['nuevo_estado']);
            
                // Verifica si el id y el estado son válidos antes de llamar a la función
                if (!empty($id) && !empty($estado)) {
                    $controller->modificarEstado($id, $estado);
                    header('Location: ../asientos-tabla.php'); // Redirige a la página de asientos
                    exit();  // Asegúrate de que el script termine aquí
                } else {
                    echo "Error: id o estado no son válidos.";
                }
            } else {
                foreach ($datas as $data) {
                    if (isset($data['tiposAsientos'])) {
                        echo json_encode($controller->insertarAsientosPorTipo($data));
                    } else {
                        echo json_encode($controller->insertarAsiento($data));
                    }
                }
            }
            exit();
            break;
        
    case 'PUT':
        $controller->updateAsiento();
        exit();
        break;

    default:
        echo json_encode(["error" => "Método no permitido"]);
        exit();

        break;
}
?>
