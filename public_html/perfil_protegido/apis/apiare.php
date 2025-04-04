<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__,3) . '/src/controllers/AsientoController.php';


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$controller = new AsientoController(); 

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
        case 'POST':
            $resultado;
            $datas = json_decode(file_get_contents("php://input"), true);
        
           
            foreach ($datas as $data) {
                if (!is_array($data)) {
                    continue; // Si $data no es un array, evitar errores
                }          
                $resultado=$controller->reiniciarAsientosRecinto($data['evento'],$data['funcion_even'],$data['recintoId']);
                if ($resultado) {
                    $resultado = ['success' => true, 'message' => 'Asientos reiniciados correctamente.'];
                } else {
                    $resultado = ['success' => false, 'message' => 'Error al reiniciar los asientos.'];
                }
                echo json_encode($resultado);
                exit();
            }
           

            break;
    
}
?>
