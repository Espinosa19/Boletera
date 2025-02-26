<?php
require_once dirname(__DIR__) . '/conexiones/ConexionDB.php';
use MongoDB\BSON\ObjectId;
class Asiento
{
    private $collection;
    private $db;
    public function __construct()
    {
        $this->db = ConexionDB::conectar();
        $this->collection = $this->db->selectCollection("asientos");
    }

    public function obtenerTodos()
    {
        try {
            return $this->collection->find()->toArray();
        } catch (Exception $e) {
            throw new Exception('Error al obtener los asientos: ' . $e->getMessage());
        }
    }
    public function obtenerPorId($id){
        return $this->collection->findOne(['_id'=>new ObjectId($id)]);
    }
    public function obtenerRecintoFuncion($recinto,$funcion){
        return $this->collection->find(['recinto_id'=>new ObjectId($recinto),'funcion'=>new ObjectId($funcion)])->toArray();
    }
    public function insertarAsiento($recintoId, $tipoAsiento, $funcion_even, $zona, $asiento)
    {
        try {
            if (!$tipoAsiento) {
                throw new Exception('Tipo de asiento no encontrado para el ID: ' . $asiento['tipoAsientoId']);
            }

            $nuevoAsiento = [
                'recinto_id' => $recintoId,
                'tipo_asiento' => [
                    '_id' => $tipoAsiento['_id'],
                    'nombre' => $tipoAsiento['nombre'],
                    'precio' => (float) $tipoAsiento['precio']
                ],
                'funcion' => $funcion_even,
                'zona' => $zona,
                'fila' => $asiento['fila'] ?? null,
                'numero' => isset($asiento['asiento']) ? intval($asiento['asiento']) : null,
                'ocupado' => isset($asiento['estado']) && $asiento['estado'] !== 'Disponible',
                'reservado_por' => null,
                'reservado_en' => null,
                'vendido' => false,
                'vendido_en' => null,
                'activo' => false,
                'estado' => $asiento['estado'] ?? null
            ];

            $this->collection->insertOne($nuevoAsiento);
        } catch (Exception $e) {
            throw new Exception('Error al insertar el asiento: ' . $e->getMessage());
        }
    }
    public function findAvailableSeatsInZone($chosenZone, $recintoId) {
        return $this->collection->find([
            'zona' => $chosenZone,
            'recinto_id' =>new ObjectId($recintoId),
            'estado' => 'Disponible',
            'ocupado' => false,
            'vendido'=>false
        ])->toArray();
    }public function buscarModificar($seatId, $userId) {
        $resultado = $this->collection->findOneAndUpdate(
            ['_id' => new \MongoDB\BSON\ObjectId($seatId), 'estado' => 'Disponible', 'ocupado' => false],
            ['$set' => [
                'estado' => 'Vendido',
                'reservado_por' => $userId,
                'reservado_en' => new \MongoDB\BSON\UTCDateTime(),
                'ocupado' => true,
                'vendido' => true
            ]],
            ['returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );
    
        if ($resultado) {
            return $resultado; // Devuelve el documento actualizado
        } else {
            return ['error' => 'El asiento ya fue reservado o no existe'];
        }
    }
    
    public function modificarEstado($id, $estado) {
        // Asegúrate de que el valor de $estado sea el que deseas (por ejemplo, "Disponible")
        $updatedAsiento = [
            'estado' => $estado // Cambia el estado del asiento
        ];
    
        // Realiza la actualización del documento con el ID proporcionado
        $this->collection->updateOne(
            ['_id' => new ObjectId($id)],  // Filtra por el ID del asiento
            ['$set' => $updatedAsiento]     // Establece el nuevo valor para el campo "estado"
        );
    }
    
    public function modificarAsiento($recintoId, $tipoAsientoId, $funcion_even, $zona, $asiento)
    {
        try {
            $updatedAsiento = [
                'recinto_id' => $recintoId,
                'tipo_asiento' => [
                    '_id' => $tipoAsientoId,
                    'nombre' => $asiento['nombre'],
                    'precio' => (float) $asiento['precio']
                ],
                'funcion' => $funcion_even,
                'zona' => $zona,
                'fila' => $asiento['fila'] ?? null,
                'numero' => $asiento['asiento'] ?? null,
                'ocupado' => $asiento['estado'] !== 'Disponible',
                'estado' => $asiento['estado'] ?? null
            ];

            $this->collection->updateOne(
                ['_id' => new ObjectId($asiento['id'])],  // Se asume que el asiento tiene un ID
                ['$set' => $updatedAsiento]
            );
        } catch (Exception $e) {
            throw new Exception('Error al modificar el asiento: ' . $e->getMessage());
        }
    }
    public function eliminarAsiento($id)
    {
        try {
            $this->collection->deleteOne(['_id' => new ObjectId($id)]);
        } catch (Exception $e) {
            throw new Exception('Error al eliminar el asiento: ' . $e->getMessage());
        }
    }
    public function insertarAsientosPorTipo($recintoId, $funcion_event, $zona, $tipoAsientoId, $tipoAsiento)
    {
        try {
            $consulta_tipo = $this->collection->findOne(['_id' => $tipoAsientoId]);
            if (!$consulta_tipo) {
                throw new Exception('Tipo de asiento no encontrado para el ID: ' . (string) $tipoAsientoId);
            }

            for ($i = 0; $i < (int) $tipoAsiento['cantidadAsientos']; $i++) {
                $nuevoAsiento = [
                    'recinto_id' => $recintoId,
                    'tipo_asiento' => [
                        '_id' => $tipoAsientoId,
                        'nombre' => $consulta_tipo['nombre'],
                        'precio' => (float) $consulta_tipo['precio']
                    ],
                    'funcion' => $funcion_event,
                    'zona' => $zona,
                    'fila' => null,
                    'numero' => $i + 1,
                    'ocupado' => false,
                    'reservado_por' => null,
                    'reservado_en' => null,
                    'vendido' => false,
                    'vendido_en' => null,
                    'activo' => false,
                    'estado' => 'Disponible'
                ];
                $this->collection->insertOne($nuevoAsiento);
            }
        } catch (Exception $e) {
            throw new Exception('Error al insertar los asientos: ' . $e->getMessage());
        }
    }
}
