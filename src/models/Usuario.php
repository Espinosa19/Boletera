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
        return $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectID($id)]);
    }

    public function obtenerUsuarioPorEmail($email) {
        return $this->collection->findOne(['email' => $email]);
    }
    public function agregarUsuario($nombre, $email, $telefono, $role) {
        $usuario = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'role' => $role
        ];
        return $this->collection->insertOne($usuario);
    }
    
    public function actualizarUsuario($id, $nombre, $email, $telefono, $role) {
        $usuario = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'role' => $role
        ];
        return $this->collection->updateOne(['_id' => $id], ['$set' => $usuario]);
    }
    public function eliminarUsuario($id) {
        return $this->collection->deleteOne(['_id' => $id]);
    }
    public function listarUsuariosConAcceso(){
        $filter = [
            'role' => ['$in' => ['admin', 'organizer', 'validador']]
        ];
        return $this->collection->find($filter)->toArray();
    }


}
?>
