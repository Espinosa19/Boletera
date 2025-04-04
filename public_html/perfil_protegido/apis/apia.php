<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
            $resultado;
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
                    if (!is_array($data)) {
                        continue; // Si $data no es un array, evitar errores
                    }
                
                    if (!empty($data['datosSin'])) { // Verifica si 'datosSin' no está vacío
                        $resultado = $controller->insertarAsientosPorTipo($data['datosSin'],$data['tipoAsientoId'],$data['recintoId'],$data['funcion_even'],$data['evento']);
                        if(!$resultado['status']){
                            echo json_encode(['status'=>false]);
                            exit();
                        }
                        
                    } 
                    if(!empty($data['datos'])) {
                        $resultado = $controller->insertarAsiento($data);
                        echo json_encode($resultado);
                        exit();
                        if(!$resultado['status']){
                            echo json_encode(['status'=>false]);
                        }

                    }
                    
                }
                
            }
            echo json_encode($resultado);
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
