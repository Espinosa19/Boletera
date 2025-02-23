<?php
require_once dirname(__DIR__) . '/models/Asiento.php';
require_once dirname(__DIR__) . '/models/TipoAsiento.php';
require_once dirname(__DIR__).'/models/Recinto.php';

use MongoDB\BSON\ObjectId;

class AsientoController
{
    private $asientoModel;
    private $tipoAsientoModel;
    public function __construct()
    {
        $this->asientoModel = new Asiento();
        $this->tipoAsientoModel=new TipoAsiento();
        $this->recintoModel=new Recinto();
    }

    public function obtenerTodos()
    {
        try {
            $asientos = $this->asientoModel->obtenerTodos(); 
            $recintoConNombre = [];
    
        foreach ($asientos as $asiento) {
            try {
                $mongoId = new MongoDB\BSON\ObjectId($asiento['recinto_id']);
                $recinto = $this->recintoModel->obtenerRecintoPorId($mongoId);
    
                // Agregar el nombre del usuario si se encontró
                $asiento['nombre_recinto'] = $recinto ? $recinto['nombre'] : 'Desconocido';
            } catch (Exception $e) {
                $asiento['nombre_recinto'] = 'ID inválido';
            }
    
            $recintoConNombre[] = $asiento;
        }
    
            return $recintoConNombre;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function obtenerPorId($id){
        return $this->asientoModel->obtenerPorId($id);
    }
    public function modificarAsiento($asiento)
    {
        try {
            $this->validarAsiento($asiento);
            $recintoId = new ObjectId($asiento['recintoId']);
            $tipoAsientoId = new ObjectId($asiento['tipoAsientoId']);
            $funcion_even = new ObjectId($asiento['funcion_even']);
            $zona = $asiento['zona'] ?? null;

            // Llamada al modelo para actualizar el asiento
            $this->asientoModel->modificarAsiento($recintoId, $tipoAsientoId, $funcion_even, $zona, $asiento);
            return ['status' => 'update'];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    private function validarAsiento($asiento)
    {
        if (empty($asiento['tipoAsientoId']) || empty($asiento['recintoId'])) {
            throw new Exception('Faltan el tipo de asiento o el ID de recinto en un asiento.');
        }
    }

    public function insertarAsiento($asiento)
    {
        $recintoId = new ObjectId($asiento['recintoId']);
        $tipoAsientoId = new ObjectId($asiento['tipoAsientoId']);
        $funcion_even = new ObjectId($asiento['funcion_even']);
        $zona = $asiento['zona'] ?? null;
        $tipoAsiento = $this->tipoAsientoModel->obtenerPorId($tipoAsientoId);

        $this->asientoModel->insertarAsiento($recintoId, $tipoAsiento, $funcion_even, $zona, $asiento);
        return ['status' => 'create'];
    }

    private function validarDatosGenerales($data)
    {
        if (empty($data['tiposAsientos']) || empty($data['recintoId']) || empty($data['funcionId'])) {
            throw new Exception('Datos incompletos: falta tipo de asiento, ID de recinto o ID de función.');
        }
    }

    public function insertarAsientosPorTipo($data, $tipoAsiento)
    {
        $recintoId = new ObjectId($data['recintoId']);
        $funcion_event = new ObjectId($data['funcionId']);
        $zona = $data['zona'] ?? null;
        $tipoAsientoId = new ObjectId($tipoAsiento['tipoAsientoId']);

        $this->asientoModel->insertarAsientosPorTipo($recintoId, $funcion_event, $zona, $tipoAsientoId, $tipoAsiento);
        return ['status' => 'create'];

    }
    public function eliminarAsiento($id)
    {
        try {
            $this->asientoModel->eliminarAsiento($id);
            return ['status' => 'delete'];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
