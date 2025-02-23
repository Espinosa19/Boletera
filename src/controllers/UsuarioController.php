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

    
}
?>