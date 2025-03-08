<?php
require_once dirname(__DIR__) . '/models/Compra.php';
require_once dirname(__DIR__) . '/controllers/TokenController.php';
require_once dirname(__DIR__) . '/controllers/PagoController.php';
require_once dirname(__DIR__) . '/controllers/AsientoController.php';

class CompraController {
    private $boletoModel;
    private $tokenController;
    private $pagoController;
    private $asientoController;
    public function __construct() {
        $this->boletoModel = new Compra();
        $this->tokenController=new TokenController();
        $this->asientoController=new AsientoController();
        $this->pagoController=new PagoController();
    }

    public function obtenerBoletos() {
        return $this->boletoModel->obtenerTodos();
    }

    public function obtenerBoletoPorId($id) {
        return $this->boletoModel->obtenerPorId($id);
    }
    public function crearBoleto($data, $taquilla) {
        $id = new MongoDB\BSON\ObjectId(); // ID único para la compra
        $precio=$data['precio'];
        $zona = $data['zona'];
        $recintoId = $data['recinto_id'];
        $cantidad = $data['cantidad'];
        
        // Reservar asientos
        $resultados = $this->asientoController->reserveSeats($zona, $recintoId, $cantidad, $id);
        $asientos = $resultados['asientos'] ?? [];
        
        if (empty($asientos)) {
            throw new RuntimeException("No se pudieron reservar asientos.");
        }
    
        // Generar claves y códigos de barras
        $token = $this->tokenController->generarClave($asientos, $data['evento_id'], $id);
        $codigoBarras = $this->tokenController->generarCodigoBarras($asientos, $data['evento_id'], $id);
    
        // Crear transacción de pago
        $informacion_trans = $this->pagoController->create($asientos, $id, $cantidad,$data['evento_id'],$precio,$taquilla);
        $transaccion = $informacion_trans['inserted_id'] ?? null;
    
        if (!$transaccion) {
            throw new RuntimeException("No se pudo crear la transacción de pago.");
        }
    
        // Crear boletos para cada asiento reservado
        $boletos = [];
        foreach ($asientos as $i => $asiento) {
            $boletoData = [
                "usuario_id" => $id,
                "evento_id" => new MongoDB\BSON\ObjectId($data['evento_id']),
                "transaccion_id" =>new MongoDB\BSON\ObjectId($transaccion),
                "recinto"=>[
                    "recinto_id"=>$asiento['recinto_id']??null,
                    "funcion_id"=>$asiento['funcion']??null,
                    "fila" => $asiento['fila'] ?? null,  
                    "zona"=>$asiento['zona']??null,
                    'asiento' => (string) $asiento['numero'],  // Asegura que 'asiento' sea un string
                    "tipo" => $asiento['tipo'] ?? null,    
                ],
                "metodo_pago" => $data['metodo'],
                "clave_unica" => $token[$i] ?? null,
                "fecha_expiracion" => null,
                "fecha_compra" => date('Y-m-d H:i:s'),
                "codigo_barras" => $codigoBarras[$i] ?? null,
                "fecha_exp_cb" => null
            ];
    
            // Guardar el boleto en la base de datos
            $this->boletoModel->insertarBoleto($boletoData);
            $boletos[] = $boletoData;
        }
    
        return true;
    }
    
    
    public function actualizarBoleto($id, $data) {
        $token = $this->tokenController->generarToken($data);
        $codigoBarras = $this->tokenController->generarCodigoBarras($data);
        
        $boletoData = [
            "usuario_id" => $data['usuario_id'],
            "evento_id" => $data['evento_id'],
            "transaccion_id" => $data['transaccion_id'],
            "fila" => $data['fila'],
            "asiento" => $data['asiento'],
            "tipo" => $data['tipo'],
            "metodo_pago" => $data['metodo_pago'],
            "clave_unica" => $token,
            "fecha_expiracion" => $data['fecha_expiracion'],
            "es_regalo" => $data['es_regalo'],
            "primera_vez" => $data['primera_vez'],
            "fecha_compra" => $data['fecha_compra'],
            "codigo_barras" => $codigoBarras,
            "fecha_exp_cb" => $data['fecha_exp_cb']
        ];
        
        $this->boletoModel->actualizar($id, $boletoData);
        return ['status' => "update"];
    }

    public function eliminarBoleto($id) {
        $this->boletoModel->eliminar($id);
        return ['status' => "delete"];
    }
}
?>
