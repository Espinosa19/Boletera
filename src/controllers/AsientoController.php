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

    public function obtenerTodos($pagina = 1, $limite = 10)
{
    try {
        $salto = ($pagina - 1) * $limite; // Calcular el desplazamiento

        // Obtener asientos con paginación
        $asientos = $this->asientoModel->obtenerTodos($limite, $salto);

        $recintoConNombre = [];
        foreach ($asientos as $asiento) {
            try {
                if (!isset($asiento['recinto_id'])) {
                    $asiento['nombre_recinto'] = 'Sin recinto';
                } else {
                    $mongoId = (string)$asiento['recinto_id'];
                    $recinto = $this->recintoModel->obtenerRecintoPorId($mongoId);
                    $asiento['nombre_recinto'] = $recinto ? $recinto['nombre'] : 'Desconocido';
                }
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
    
    public function obtenerRecintoFuncion($recinto,$funcion){
        return $this->asientoModel->obtenerRecintoFuncion($recinto,$funcion);
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

    public function insertarAsiento($asientos)
    {
        // Verificar si $asientos es un array y si contiene 'datos'
        if (!isset($asientos['datos']) || !is_array($asientos['datos'])) {
            return ['error' => 'Datos inválidos o estructura incorrecta'];
        }
        $recintoId = new ObjectId($asientos['recintoId']);
        $evento=new ObjectId($asientos['evento']);
        $tipoAsientoId = new ObjectId($asientos['tipoAsientoId']);
        $funcion_even = new ObjectId($asientos['funcion_even']);
        $tipoAsiento = $this->tipoAsientoModel->obtenerPorId($tipoAsientoId);
    
        // Recorrer los asientos dentro de 'datos'
        foreach ($asientos['datos'] as $asiento) {
            // Validar que 'filas' y 'rango' existan

            

            foreach ($asiento['caracteristicas'] as $caracteristica) {
                
                // Validar que 'fila' exista y no sea un array vacío
                if (!isset($caracteristica['fila']) || !is_string($caracteristica['fila']) || empty($caracteristica['fila'])) {
                    continue; // Saltar si no hay filas definidas
                }
            
                    // Validar rangoInicio y rangoFin
                    if (!isset($caracteristica['rangoInicio']) || !isset($caracteristica['rangoFin'])) {
                        continue;
                    }
                    $fila = $caracteristica['fila'];
                    for ($i = intval($caracteristica['rangoInicio']); $i <= intval($caracteristica['rangoFin']); $i++) {
                        $asi = $i;
            
                        // Verificar si el asiento está limitado
                        $estaLimitado = false;
            
                        foreach ($asiento['limitaciones'] as $limitacion) {
                            if (
                                isset($limitacion['letra'], $limitacion['limitaciones']) &&
                                is_array($limitacion['limitaciones']) &&
                                $limitacion['letra'] === $fila
                            ) {
                                // Asegurar que $limitacion['limitaciones'] sea un array válido antes de usar in_array()
                                if (in_array($asi, $limitacion['limitaciones'])) {
                                    $estaLimitado = true;
                                    break;
                                }
                            }
                        }
            
                        if ($estaLimitado) {
                            continue; // Saltar si el asiento está limitado
                        }
            
                        // Insertar el asiento si no está limitado
                        $this->asientoModel->insertarAsiento(
                            $recintoId,
                            $evento,
                            $tipoAsiento,
                            $funcion_even,
                            $asiento['zona'],
                            $fila,
                            $asi
                        );
                    
                }
            }
            
            
            
        }
    
        // Después de procesar todos los asientos, retornar el estado
        return ['status' => true];
    }
    
    public function reiniciarAsientosRecinto($evento, $funcion, $recintoId)
    {
        $resultado = $this->asientoModel->reiniciarAsientosRecinto($evento, $funcion, $recintoId);
    
        // Verificar el resultado de la operación
        if ($resultado) {
            $matchedCount = $resultado['matched'];
            $modifiedCount = $resultado['modified'];
    
            if ($matchedCount > 0) {
                return [
                    'status' => true,
                    'message' => 'Asientos reiniciados correctamente.',
                    'matched' => $matchedCount,
                    'modified' => $modifiedCount,
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'No se encontraron asientos para reiniciar.',
                    'matched' => $matchedCount,
                    'modified' => $modifiedCount,
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Error al intentar reiniciar los asientos.',
            ];
        }
    }
    public function modificarEstado($id,$estado){
        $this->asientoModel->modificarEstado($id,$estado);
        return ['status'=>'modificar'];
    }
    private function validarDatosGenerales($data)
    {
        if (empty($data['tiposAsientos']) || empty($data['recintoId']) || empty($data['funcionId'])) {
            throw new Exception('Datos incompletos: falta tipo de asiento, ID de recinto o ID de función.');
        }
    }

    public function insertarAsientosPorTipo($datas, $tipoAsientoId,$recinto_id,$funcion,$evento)
    {
        $recintoId = new ObjectId($recinto_id);
        $funcion_event = new ObjectId($funcion);
        $evento=new ObjectId($evento);
        $tipoAsientoId = new ObjectId($tipoAsientoId);
        $tipoAsiento = $this->tipoAsientoModel->obtenerPorId($tipoAsientoId);
        foreach($datas as $data){
            $zona=$data['nombre_zona'];
            $cantidad=$data['cantidad'];
            $this->asientoModel->insertarAsientosPorTipo($recintoId, $funcion_event, $zona, $tipoAsientoId, $tipoAsiento,$cantidad,$evento);
        }
        return ['status' => true];

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
    public function findAvailableSeatsInZone($chosenZone, $recintoId,$funcion) {
        
        return $this->asientoModel->findAvailableSeatsInZone($chosenZone,$recintoId,$funcion);
    }

    /**
     * Reservar múltiples asientos para un usuario
     */
    public function reserveSeats($chosenZone, $recintoId, $requestedSeats,$funcion , $userId) {
        $availableSeats = $this->findAvailableSeatsInZone($chosenZone, $recintoId,$funcion);

        $availableSeatsArray = $availableSeats;

        if (count($availableSeatsArray) < $requestedSeats) {
            return ["success" => false, "message" => "No hay suficientes asientos disponibles."];
        }

        $seatIdsToReserve = [];
        foreach ($availableSeatsArray as $asiento) {
            $seatIdsToReserve[] = $asiento['_id'];
            if (count($seatIdsToReserve) == $requestedSeats) {
                break;
            }
        }

        return $this->reserveMultipleSeats($seatIdsToReserve, $userId);
    }

    /**
     * Lógica para marcar los asientos como reservados con tiempo de expiración
     */
    private function reserveMultipleSeats($seatIdsToReserve, $userId) {
        try {
            $reservasExitosas = 0;
            $asientosModificados = [];
    
            foreach ($seatIdsToReserve as $seatId) {
                try {
                    $resultado = $this->asientoModel->buscarModificar($seatId, $userId);
    
                    if ($resultado) {
                        $reservasExitosas++;
                        $asientosModificados[] = [
                            '_id' => !empty($resultado['_id']) ? (string) $resultado['_id'] : null,
                            'zona' => !empty($resultado['zona']) ? $resultado['zona'] : null,
                            'fila' => !empty($resultado['fila']) ? $resultado['fila'] : null,
                            'recinto_id' => $resultado['recinto_id'] ?? null,
                            'funcion' => $resultado['funcion'] ?? null,
                            'asiento' => !empty($resultado['numero']) ? (string) $resultado['numero'] : null,
                            'tipo' => !empty($resultado['tipo_asiento']['nombre']) ? $resultado['tipo_asiento']['nombre'] : null
                        ];
                    }
                } catch (Exception $e) {
                    // Registrar error y continuar con el siguiente asiento
                    error_log("Error al reservar asiento ID: $seatId - " . $e->getMessage());
                }
            }
    
            return [
                "success" => $reservasExitosas === count($seatIdsToReserve),
                "reservados" => $reservasExitosas,
                "asientos" => $asientosModificados
            ];
    
        } catch (Exception $e) {
            // Capturar errores generales y devolver mensaje
            return [
                "success" => false,
                "message" => "Error en la reserva de asientos: " . $e->getMessage()
            ];
        }
    }
    
    

}
