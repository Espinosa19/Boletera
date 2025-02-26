<?php
require_once dirname(__DIR__).'/models/Pago.php';
require_once dirname(__DIR__) . '/models/Usuario.php';

class PagoController {
    private $usuarioModel;
    private $pagoModel;
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->pagoModel = new Pago();
    }
    public function index() {
        
        $pagos = $this->pagoModel->getAll();
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
    public function create($datas, $id, $cantidad_b, $evento, $precio, $taqui) {
        if (!is_array($datas) || count($datas) === 0) {
            throw new InvalidArgumentException("No hay datos de boletos para procesar.");
        }

        if (!is_numeric($cantidad_b) || $cantidad_b <= 0 || !is_numeric($precio) || $precio <= 0) {
            throw new InvalidArgumentException("Cantidad y precio deben ser valores numéricos positivos.");
        }

        if (!is_string($evento) || strlen($evento) > 255) {
            throw new InvalidArgumentException("El nombre del evento no es válido.");
        }

        $total = $this->generarTotal($cantidad_b, $precio);
        $filas = [];
        $numeros_asiento = [];

        foreach ($datas as $data) {
            if (!isset($data['fila'], $data['asiento'])) {
                throw new InvalidArgumentException("Faltan datos de fila o asiento en un boleto.");
            }
            $filas[] = $data['fila'];
            $numeros_asiento[] = $data['asiento'];
        }

        $filasStr = !empty($filas) ? implode(", ", $filas) : "N/A";
        $asientosStr = !empty($numeros_asiento) ? implode(", ", $numeros_asiento) : "N/A";

        $descripcion = "Compra de {$cantidad_b} boletos para el evento {$evento} en la(s) fila(s) {$filasStr}, asiento(s) {$asientosStr}.";

        // Obtener la fecha más reciente
        $fechas = array_filter(array_map(fn($d) => isset($d['fecha']) ? strtotime($d['fecha']) * 1000 : null, $datas));
        $fecha = !empty($fechas) ? max($fechas) : time() * 1000;

        // Crear array con los datos de la transacción
        $transaccionData = [
            'usuario_id' => $id,
            'tipo_transaccion' => is_string($taqui) ? $taqui : 'desconocido',
            'descripcion' => $descripcion,
            'total' => $total,
            'fecha' => new MongoDB\BSON\UTCDateTime($fecha)
        ];

        // Insertar en la colección de pagos
        $insertResult = $this->pagoModel->insert($transaccionData);

        return [
            'status' => 'create',
            'inserted_id' => (string) $insertResult->getInsertedId()
        ];
    }

    
    
    private function generarTotal($cantidad_b,$precio){
        $total=$cantidad_b*$precio;
        return $total;
    }

    
    public function delete($id) {
        $this->pagoModel->delete($id);
        return ['status' => 'delete'];
    }
}
