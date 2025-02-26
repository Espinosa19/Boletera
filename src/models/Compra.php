<?php
require_once dirname(__DIR__) . '/conexiones/ConexionDB.php';


class Compra {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = ConexionDB::conectar();
        $this->collection = $this->db->selectCollection("compras");
    }

    public function insertarBoleto($datos) {
        return $this->collection->insertOne($datos);
    }

    public function obtenerBoletos() {
        return $this->collection->find()->toArray();
    }

    public function obtenerBoletoPorId($id) {
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }

    public function actualizarBoleto($id, $datos) {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => $datos]
        );
    }
 
    public function eliminarBoleto($id) {
        return $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    }
}
?>
