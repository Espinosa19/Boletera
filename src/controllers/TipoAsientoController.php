<?php
require_once dirname(__DIR__) . '/models/TipoAsiento.php';

class TipoAsientoController {
    private $tipoAsientoModel;

    public function __construct() {
        $this->tipoAsientoModel = new TipoAsiento();
    }

    public function obtenerTiposAsientos() {
        return $this->tipoAsientoModel->obtenerTodos();
    }

    public function obtenerTipoAsientoPorId($id) {
        return $this->tipoAsientoModel->obtenerPorId($id);
    }
 
    public function crearTipoAsiento($data) {
        $recintoData = [
            "nombre" => $data['nombre'],
            "precio" => $data['precio'],
            "creado_por" => $data['creado_por'],
            "activo" => $data['activo']
        ];
        $this->tipoAsientoModel->crear($recintoData);
        return ['status'=>"create"];
    }

    public function actualizarTipoAsiento($id, $data) {
        $recintoData = [
            "nombre" => $data['nombre'],
            "precio" => $data['precio'],
            "creado_por" => $data['creado_por'],
            "activo" => $data['activo']
        ];
        $this->tipoAsientoModel->actualizar($id, $data);
        return ['status'=>"update"];
    }

    public function eliminarTipoAsiento($id) {
        $this->tipoAsientoModel->eliminar($id);
        return ['status'=>"Delete"];
    }
}
?>
