<?php
require_once dirname(__DIR__) . '/conexiones/ConexionDB.php';
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Evento {
    private $db;
    private $collection;
    public function __construct() {
        $this->db = ConexionDB::conectar();
        $this->collection = $this->db->selectCollection("eventos");
    }

    public function obtenerEventos() {
        return $this->collection->find()->toArray();
    }
    public function obtenerEventosPorId($id){
        return $this->collection->find(['recintos.id' => (string) $id]);

    } 
    public function obtenerEventoPorId($id) {
        return $this->collection->findOne(['_id' => new ObjectId($id)]);
    }

    public function agregarEvento($datos) {
        return $this->collection->insertOne($datos);
    }

    public function actualizarEvento($id, $datos) {
        return $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => $datos]
        );
    }

    public function eliminarEvento($id) {
        return $this->collection->deleteOne(['_id' => new ObjectId($id)]);
    }
}
?>
