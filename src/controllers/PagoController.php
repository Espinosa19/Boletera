<?php
require_once dirname(__DIR__).'/models/Pago.php';
require_once dirname(__DIR__) . '/models/Usuario.php';

class PagoController {
    private $usuarioModel;
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    public function index() {
        $pago = new Pago();
        $pagos = $pago->getAll();
        $pagosConNombre = [];
    
        foreach ($pagos as $pago) {
            try {
                $mongoId = new MongoDB\BSON\ObjectId($pago['usuario_id']);
                $usuario = $this->usuarioModel->obtenerUsuarioPorId($mongoId);
    
                // Agregar el nombre del usuario si se encontró
                $pago['nombre_usuario'] = $usuario ? $usuario['nombre'] : 'Desconocido';
            } catch (Exception $e) {
                $pago['nombre_usuario'] = 'ID inválido';
            }
    
            $pagosConNombre[] = $pago;
        }
    
        return $pagosConNombre;
    }
    
    public function create() {
        return ['insertedId' => (string)$result->getInsertedId()];

    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'usuario_id' => $_POST['usuario_id'],
                'tipo_transaccion' => $_POST['tipo_transaccion'],
                'descripcion' => $_POST['descripcion'],
                'fecha' => new MongoDB\BSON\UTCDateTime(strtotime($_POST['fecha']) * 1000)
            ];
            $pago = new Pago();
            $pago->insert($data);
            return ['status' => 'create'];
        }
    }

    public function edit($id) {
        $pago = new Pago();
        $pagoData = $pago->getById($id);
        require_once 'views/pago/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['_id'];
            $data = [
                'usuario_id' => $_POST['usuario_id'],
                'tipo_transaccion' => $_POST['tipo_transaccion'],
                'descripcion' => $_POST['descripcion'],
                'fecha' => new MongoDB\BSON\UTCDateTime(strtotime($_POST['fecha']) * 1000)
            ];
            $pago = new Pago();
            $pago->update($id, $data);
            return ['status' => 'update'];
        }
    }
    public function delete($id) {
        $pago = new Pago();
        $pago->delete($id);
        return ['status' => 'delete'];
    }
}
