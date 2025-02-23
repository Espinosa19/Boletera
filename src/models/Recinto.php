<?php
require_once dirname(__DIR__) . '/conexiones/ConexionDB.php';
use MongoDB\BSON\ObjectId;

class Recinto {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = ConexionDB::conectar();
        $this->collection = $this->db->selectCollection("recintos");
    }

    public function crearRecinto($recintoData) {
        return $this->collection->insertOne($recintoData);
    }

    public function obtenerRecintos() {
        return $this->collection->find()->toArray();
    }

    public function obtenerRecintoPorId($id) {
        return $this->collection->findOne(['_id' => new ObjectId($id)]);
    }

    public function actualizarRecinto($id, $recintoData) {
        return $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => $recintoData]
        );
    }

    public function eliminarRecinto($id) {
        return $this->collection->deleteOne(['_id' => new ObjectId($id)]);
    }
}
?>
