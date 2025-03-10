<?php
// Mostrar todos los errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . '/controllers/EventoController.php';
require_once dirname(__DIR__) . '/models/Recinto.php';


header('Content-Type: application/json');

class obtenerFuncionController
{
    private $recintoModel;
    private $eventoController;

    public function __construct()
    {
        // Conectar a la base de datos y obtener las colecciones necesarias
        $this->recintoModel = new Recinto();
        $this->eventoController = new EventoController();

    }
    public function getRecintos($evento){
        return $this->eventoController->obtenerEventosPorId($evento);
    }
    public function getFuncionesYZonas($recintoId,$evento)
{
    try {
        // Buscar el recinto
        $recintosCursor = $this->recintoModel->obtenerRecintoPorId($recintoId);
        // Buscar los eventos asociados al recinto
        $funcion = $this->eventoController->obtenerEventosPorId($evento,$recintoId);

        // Convertir los cursores a arrays si es necesario
        if ($funcion instanceof MongoDB\Driver\Cursor) {
            $funcion = iterator_to_array($funcion); // Usar iterator_to_array si es un cursor
        } else {
            $funcion = $funcion; // Si ya es un array, asignarlo directamente
        }

        $zonas = [];
        foreach ($recintosCursor['zonas'] as $zona) {

            $zonas[] = [
                'nombre_zona' => $zona['nombre_zona'],
                
            ];
                
            
        }

        return ['zonas' => $zonas];

    } catch (MongoDB\Exception\Exception $e) {
        return $this->responseError('Error en la conexión con la base de datos o consulta', 500, $e->getMessage());
    }
}

    private function responseSuccess($data)
    {
        http_response_code(200);
        return ['status' => 'success', 'data' => $data];
    }

    private function responseError($message, $code = 500, $debugMessage = '')
    {
        http_response_code($code);
        return ['error' => $message, 'debug' => $debugMessage];
    }
}


?>
