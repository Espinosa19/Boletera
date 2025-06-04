<?php
require_once dirname(__DIR__) . '/models/Usuario.php';

class UsuarioController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function obtenerUsuarios() {
        return $this->usuarioModel->obtenerUsuarios();
    }
    public function listarUsuariosConAcceso(){
        $usuarios = $this->usuarioModel->listarUsuariosConAcceso();
        if ($usuarios) {
            return $usuarios;
        }
        return json_encode(["error" => "No se encontraron usuarios"]);
    }
    public function obtenerUsuarioPorId($id) {
        $usuario = $this->usuarioModel->obtenerUsuarioPorId($id);
        if ($usuario) {
            return $usuario;
        }
        return json_encode(["error" => "Usuario no encontrado"]);
    }
    
    public function obtenerUsuarioPorEmail($email) {
        $usuario = $this->usuarioModel->obtenerUsuarioPorEmail($email);
        if ($usuario) {
            return $usuario;
        }
        return json_encode(["error" => "Usuario no encontrado"]);
    }
    public function agregarUsuario($nombre, $email, $telefono, $role) {
        return $this->usuarioModel->agregarUsuario($nombre, $email, $telefono, $role);
    }
    public function actualizarUsuario($id, $nombre, $email, $telefono, $role) {
        return $this->usuarioModel->actualizarUsuario($id, $nombre, $email, $telefono, $role);
    }
    public function eliminarUsuario($id) {
        return $this->usuarioModel->eliminarUsuario($id);
    }
    
}
?>