<?php
require_once dirname(__DIR__) . '/conexiones/ConexionDB.php';

class TipoAsiento {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = ConexionDB::conectar();
        $this->collection = $this->db->selectCollection("tipos_asientos");
    }

    public function obtenerTodos() {
        return $this->collection->find()->toArray();
    }

    public function obtenerPorId($id) {
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }

    public function crear($data) {
        return $this->collection->insertOne($data);
    }

    public function actualizar($id, $data) {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $data]
        );
    }

    public function eliminar($id) {
        return $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }
}
?>
