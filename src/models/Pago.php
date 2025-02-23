<?php
require_once dirname(__DIR__) . '/conexiones/ConexionDB.php';

class Pago {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = ConexionDB::conectar();
        $this->collection = $this->db->selectCollection("pagos");
    }

    public function getAll() {
        return $this->collection->find()->toArray();
    }

    public function getById($id) {
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }

    public function insert($data) {
        $this->collection->insertOne($data);
    }

    public function update($id, $data) {
        $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $data]
        );
    }

    public function delete($id) {
        $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }
}


?>