<?php
require_once dirname(__DIR__) . '/models/Evento.php';
require_once dirname(__DIR__) . '/models/Recinto.php';

class EventoController {
    private $eventoModel;
    private $recintoModel;

    public function __construct() {
        $this->eventoModel = new Evento();
        $this->recintoModel = new Recinto(); // ← Corrección aquí
    }

    /**
     * Listar todos los eventos
     */
    public function listarEventos() {
        $eventos = $this->eventoModel->obtenerEventos();
        $recintos = $this->recintoModel->obtenerRecintos();

        return ['eventos'=>$eventos,'recintos'=>$recintos];
    }

    /**
     * Ver un evento por su ID
     */
    public function verEvento($id) {
        $evento = $this->eventoModel->obtenerEventoPorId($id);
        if (!$evento) {
            echo json_encode(['error' => 'Evento no encontrado']);
            http_response_code(404);
            return;
        }
        return $evento;
    }
    public function listar() {
        return $this->eventoModel->obtenerEventos();
        
    }
    /**
     * Obtener eventos por ID de recinto
     */
    public function obtenerEventosPorId($id) {
        // Validar que el ID sea un ID válido de MongoDB
        if (!preg_match('/^[a-f0-9]{24}$/', $id)) {
            return $this->responseError('ID no válido.', 400);
        }
    
        try {
            // Buscar eventos por ID de recinto
            $eventos = $this->eventoModel->obtenerEventosPorId($id);
            
            // Convertir el cursor a array
            $eventos = iterator_to_array($eventos); // Utilizar iterator_to_array() para convertir el cursor en un array
    
            if (empty($eventos)) {
                return $this->responseError('No se encontraron eventos con el ID proporcionado.', 404);
            }
    
            $eventosC = []; // Crear un array para las funciones
    
            foreach ($eventos as $evento) {
                foreach ($evento['recintos'] as $recinto) {
                    if ((string)$recinto['id'] === $id) { // Usamos $id aquí
                        foreach ($recinto['funciones'] as $funcion) {
                            // Verificar si los campos fecha_inicio y fecha_fin son instancias de UTCDateTime
                            $fecha_inicio = $funcion['fecha_inicio'];
                            $fecha_fin = $funcion['fecha_fin'];
                            $id=$funcion['id'];
                            // Convertir a DateTime solo si es un objeto UTCDateTime
                            if ($fecha_inicio instanceof MongoDB\BSON\UTCDateTime) {
                                $fecha_inicio = $fecha_inicio->toDateTime()->format('Y-m-d H:i');
                            }
    
                            if ($fecha_fin instanceof MongoDB\BSON\UTCDateTime) {
                                $fecha_fin = $fecha_fin->toDateTime()->format('Y-m-d H:i');
                            }
    
                            // Agregar la función al array de resultados
                            $eventosC[] = [
                                "_id"=>$id,
                                'nombre' => $evento['nombre'],
                                'fecha_inicio' => $fecha_inicio,
                                'fecha_fin' => $fecha_fin,
                            ];
                        }
                    }
                }
            }
            return $eventosC;
    
        } catch (MongoDB\Exception\Exception $e) {
            return $this->responseError('Error en la conexión con la base de datos o consulta', 500, $e->getMessage());
        }
    }
    
    /**
     * Crear un nuevo evento
     */
    public function crear($nombre,$cate, $descripcion, $imagen, $recinto,$reco) {
        if (empty($nombre) || empty($descripcion)) {
            echo json_encode(['error' => 'Faltan datos obligatorios']);
            return;
        }

        $datos = [
            'nombre' => $nombre,
            'categoria'=>$cate,
            'descripcion' => $descripcion,
            'imagen' => $imagen ?? '',
            'recomendado'=>$reco,
            'recintos' => $recinto ?? []
            
        ];

        $resultado = $this->eventoModel->agregarEvento($datos);

        return ['status' => 'create'];
    }

    /**
     * Actualizar un evento
     */
    public function actualizarEvento($id, $nombre, $cate,$descripcion, $imagen, $recinto,$reco) {
        if (empty($id) || empty($nombre) || empty($descripcion)) {
            echo json_encode(['error' => 'Faltan datos obligatorios']);
            http_response_code(400);
            return;
        }

        $datos = [
            'nombre' => $nombre,
            'categoria'=>$cate,
            'descripcion' => $descripcion,
            'recomendado'=>$reco,
            'imagen' => $imagen ?? '',
            'recintos' => $recinto ?? []
        ];

        $resultado = $this->eventoModel->actualizarEvento($id, $datos);
        return ['status' => 'updated'];
    }

    /**
     * Eliminar un evento
     */
    public function eliminarEvento($id) {
        if (empty($id)) {
            echo json_encode(['error' => 'ID no proporcionado']);
            http_response_code(400);
            return;
        }

        $resultado = $this->eventoModel->eliminarEvento($id);
        return ['status' => 'delete'];
    }

    /**
     * Manejo de errores
     */
    private function responseError($message, $code = 500, $debugMessage = '') {
        http_response_code($code);
        return json_encode(['error' => $message, 'debug' => $debugMessage]);
    }
}
?>
