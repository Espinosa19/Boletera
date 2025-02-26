<?php
require_once dirname(__DIR__, 3) . '/src/controllers/EventoController.php';
require_once dirname(__DIR__, 3) . '/src/controllers/RecintoController.php';
require_once dirname(__DIR__, 3) . '/src/controllers/AsientoController.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $eventoController = new EventoController();
    $recintoController=new RecintoController();
    $asientoController=new AsientoController();

    $data = json_decode(file_get_contents("php://input"), true);

   
    if(!empty($data['evento_id'])){
        $eventoId = $data['evento_id'];
        $evento = $eventoController->verEvento($eventoId);
        if (!$evento) {
            echo json_encode(["success" => false, "message" => "Evento no encontrado."]);
            exit;
        }
        echo json_encode([
            "success" => true,
            "recintos" => $evento['recintos']
        ]);

    }else if(!empty($data['recintoId'])&&!empty($data['funcionId']) ){
        $asientos_nuevos=[];
        $asientos=$asientoController->obtenerRecintoFuncion($data['recintoId'],$data['funcionId']);
        if (!$asientos) {
            echo json_encode(["success" => false, "message" => "Asientos no encontrados."]);
            exit;
        }
        $asientos_nuevos = [];
        $tipos_asientos_unicos = [];
        
        foreach ($asientos as $asiento) {
            if ($asiento['ocupado'] == false) {
                $clave = $asiento['tipo_asiento']['nombre'] . '-' . $asiento['zona'];
        
                if (!isset($tipos_asientos_unicos[$clave])) {
                    $tipos_asientos_unicos[$clave] = [
                        'nombre' => $asiento['tipo_asiento']['nombre'],
                        'precio' => $asiento['tipo_asiento']['precio'],
                        'zona' => $asiento['zona']
                    ];
                }
            }
        }
        
        // Convertir a un array indexado y enviarlo en formato JSON
        echo json_encode([
            "success" => true,
            "asientos" => array_values($tipos_asientos_unicos)
        ]);
        
    }
    
}
?>
