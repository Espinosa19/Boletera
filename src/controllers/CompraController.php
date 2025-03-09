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
        try {
            $id = new MongoDB\BSON\ObjectId();
            $evento = new MongoDB\BSON\ObjectId($data['evento_id']);
            $precio = $data['precio'] ?? null;
            $zona = $data['zona'] ?? null;
            $recintoId = $data['recinto_id'] ?? null;
            $cantidad = $data['cantidad'] ?? 0;
            $funcion = $data['funcion'] ?? null;
    
            if (!$precio || !$zona || !$recintoId || !$cantidad || !$funcion) {
                throw new InvalidArgumentException("Faltan datos requeridos para la creación del boleto.");
            }
    
            // Reservar asientos
            $resultados = $this->asientoController->reserveSeats($zona, $recintoId, $cantidad, $funcion, $id);
            if (!$resultados['status']) {
                throw new RuntimeException("No se pudieron reservar los asientos.");
            }
    
            $asientos = $resultados['asientos'] ?? [];
            
            // Generar claves y códigos de barras
            $token = $this->tokenController->generarClave($asientos, $evento, $id);
            $codigoBarras = $this->tokenController->generarCodigoBarras($asientos, $evento, $id);
            if(!$token['status']){
                throw new RuntimeException("No se genero correctamente el token.");
            }
            if(!$codigoBarras['status']){
                throw new RuntimeException("No se genero correctamente el token.");
            }
            $token=$token['data'];
            $codigoBarras=$codigoBarras['data'];
            // Crear transacción de pago
            $informacion_trans = $this->pagoController->create($asientos, $id, $cantidad, $evento, $precio, $taquilla);
            $transaccion = $informacion_trans['inserted_id'] ?? null;
    
            if (!$transaccion) {
                throw new RuntimeException("No se pudo crear la transacción de pago.");
            }
    
            // Crear boletos para cada asiento reservado
            $boletos = [];
            foreach ($asientos as $i => $asiento) {
                   
                $boletoData = [
                    "usuario_id" => $id,
                    "evento_id" => $evento,
                    "transaccion_id" => $transaccion,
                    "recinto" => [
                        "recinto_id" => $asiento['recinto_id'] ?? null,
                        "funcion_id" => $asiento['funcion'] ?? null,
                        "fila" => $asiento['fila'] ?? null,  
                        "zona" => $asiento['zona'] ?? null,
                        "asiento" => (string) $asiento['asiento'],
                        "tipo" => $asiento['tipo'] ?? null,    
                    ],
                    "metodo_pago" => $data['metodo'] ?? null,
                    "clave_unica" => $token[$i] ?? null,
                    "fecha_expiracion" => null, // Genera fecha en formato MongoDB
                    "fecha_compra" => new MongoDB\BSON\UTCDateTime(),
                    "codigo_barras" => $codigoBarras[$i] ?? null,
                    "fecha_exp_cb" =>null
                ];
    
                // Guardar el boleto en la base de datos
                $this->boletoModel->insertarBoleto($boletoData);
                $boletos[] = $boletoData;
            }
    
            return ["status" => true, "message" => "Boletos creados exitosamente", "data" => $boletos];
    
        } catch (InvalidArgumentException $e) {
            return ["status" => false, "message" => "Error en los datos: " . $e->getMessage()];
        } catch (RuntimeException $e) {
            return ["status" => false, "message" => "Error en el proceso de boletos: " . $e->getMessage()];
        } catch (Exception $e) {
            return ["status" => false, "message" => "Error inesperado: " . $e->getMessage()];
        }
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
