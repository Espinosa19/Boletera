<?php
require_once dirname(__DIR__) . '/conexiones/ConexionDB.php';

class Usuario {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = ConexionDB::conectar();
        $this->collection = $this->db->selectCollection("usuarios");
    }

    public function obtenerUsuarios() {
        return $this->collection->find()->toArray();
    }
    public function obtenerUsuarioPorId($id) {
        return $this->collection->findOne(['_id' => $id]);
    }
    
    public function obtenerUsuarioPorEmail($email) {
        return $this->collection->findOne(['email' => $email]);
    }
    
}
?>
