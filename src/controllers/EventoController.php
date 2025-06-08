<?php
require_once dirname(__DIR__) . '/models/Evento.php';
require_once dirname(__DIR__) . '/models/Recinto.php';
require_once dirname(__DIR__) . '/models/Categoria.php';

class EventoController {
    private $eventoModel;
    private $recintoModel;
    private $categoriaModel;

    public function __construct() {
        $this->eventoModel = new Evento();
        $this->recintoModel = new Recinto(); // ← Corrección aquí
        $this->categoriaModel = new Categoria();
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
    public function obtenerEventosPorId($id_evento, $id) {
        // Validar que el ID sea un string de MongoDB
        if ($id_evento instanceof MongoDB\BSON\ObjectId) {
            $id_evento = (string) $id_evento;
        }
        if ($id instanceof MongoDB\BSON\ObjectId) {
            $id = (string) $id;
        }
    
        try {
            // Buscar eventos por ID de recinto
            $evento = $this->eventoModel->obtenerEventoPorId($id_evento);
    
            // Si el evento no se encuentra, retornamos error
            if (!$evento) {
                return $this->responseError('No se encontró el evento con el ID proporcionado.', 404);
            }
    
            // Convertir ObjectId en los recintos a string
            foreach ($evento['recintos'] as &$recinto) {
                if ($recinto['id'] instanceof MongoDB\BSON\ObjectId) {
                    $recinto['id'] = (string) $recinto['id'];
                }
    
                // Convertir ObjectId en funciones a string
              
            }
    
            $respuesta = [];
            if (!$id) {

                foreach ($evento['recintos'] as $recinto) {
                    $recinto_id = $recinto['id'];
                    foreach ($recinto['funciones'] as $funcion) {
                        $fecha_inicio = isset($funcion['fecha_inicio']) ? $funcion['fecha_inicio'] : null;
                        $fecha_fin = isset($funcion['fecha_fin']) ? $funcion['fecha_fin'] : null;
                
                        // Convertir UTCDateTime a formato legible
                        if ($fecha_inicio instanceof MongoDB\BSON\UTCDateTime) {
                            $fecha_inicio = $fecha_inicio->toDateTime()->format('Y-m-d H:i');
                        }
                
                        if ($fecha_fin instanceof MongoDB\BSON\UTCDateTime) {
                            $fecha_fin = $fecha_fin->toDateTime()->format('Y-m-d H:i');
                        }
                
                        // Verificar si el elemento ya existe en la respuesta
                        if (!array_filter($respuesta, fn($item) =>
                            $item['_id'] === $funcion['id'] &&
                            $item['fecha_inicio'] === $fecha_inicio &&
                            $item['fecha_fin'] === $fecha_fin
                        )) {
                            // Agregar la función a la respuesta si no existe
                            $respuesta[] = [
                                "_id" => $funcion['id'],
                                'recinto' => $recinto_id,
                                'nombre' => $evento['nombre'],
                                'fecha_inicio' => $fecha_inicio,
                                'fecha_fin' => $fecha_fin,
                            ];
                        }
                    }
                }
        }
            else{
                foreach ($evento['recintos'] as $recinto) {
                    if ($recinto['id'] === $id) {
                        foreach ($recinto['funciones'] as $funcion) {
                            $fecha_inicio = isset($funcion['fecha_inicio']) ? $funcion['fecha_inicio'] : null;
                            $fecha_fin = isset($funcion['fecha_fin']) ? $funcion['fecha_fin'] : null;

                            // Convertir UTCDateTime a formato legible
                            if ($fecha_inicio instanceof MongoDB\BSON\UTCDateTime) {
                                $fecha_inicio = $fecha_inicio->toDateTime()->format('Y-m-d H:i');
                            }

                            if ($fecha_fin instanceof MongoDB\BSON\UTCDateTime) {
                                $fecha_fin = $fecha_fin->toDateTime()->format('Y-m-d H:i');
                            }

                            // Agregar la función a la respuesta
                            $respuesta[] = [
                                "_id" => $funcion['id'],
                                'nombre' => $evento['nombre'],
                            ];
                        } 
                    }
                }
            }
    
            return $respuesta;
        } catch (MongoDB\Exception\Exception $e) {
            return $this->responseError('Error en la conexión con la base de datos o consulta', 500, $e->getMessage());
        }
    }
    
    
    
    /**
     * Crear un nuevo evento
     */public function crear($nombre, $subcategoria, $descripcion, $imagen, $recintos, $reco) {
  if (empty(trim($nombre)) || empty(trim($descripcion)) || empty(trim($subcategoria))) {
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    return;
}

    foreach ($recintos as $recinto) {
        $idRecinto = $recinto['id'] ?? null;
        if (empty($idRecinto)) {
            echo json_encode(['error' => 'Falta el ID del recinto']);
            return;
        }
        $resultado= $this->recintoModel->obtenerRecintoPorId($idRecinto);
        $recinto['ciudad'] = $resultado['ciudad'] ?? '';
    }
    // Conexión a la colección de categorías
    $categoria = $this->categoriaModel->buscarPorSubcategoria($subcategoria);

    if (!$categoria) {
        echo json_encode(['error' => 'Categoría no encontrada para esa subcategoría']);
        return;
    }

    $categoriaId = $categoria['_id']; // ObjectId

    $datos = [
        'nombre' => $nombre,
        'categoria' => $categoriaId,
        'subcategoria' => $subcategoria,
        'descripcion' => $descripcion,
        'imagen' => $imagen ?? '',
        'recomendado' => $reco,
        'recintos' => $recintos ?? []
    ];

    $resultado = $this->eventoModel->agregarEvento($datos);

    return ['status' => 'create'];
}


    /**
     * Actualizar un evento
     */
    public function actualizarEvento($id, $nombre, $cate,$descripcion, $imagen, $recintos,$reco) {
        if (empty($id) || empty($nombre) || empty($descripcion)) {
            echo json_encode(['error' => 'Faltan datos obligatorios']);
            http_response_code(400);
            return;
        }
        foreach ($recintos as $recinto) {
            $idRecinto = $recinto['id'] ?? null;
            if (empty($idRecinto)) {
                echo json_encode(['error' => 'Falta el ID del recinto']);
                return;
        }
        $resultado= $this->recintoModel->obtenerRecintoPorId($idRecinto);
        $recinto['ciudad'] = $resultado['ciudad'] ?? '';
    }
        
        $datos = [
            'nombre' => $nombre,
            'categoria'=>$cate,
            'descripcion' => $descripcion,
            'recomendado'=>$reco,
            'imagen' => $imagen ?? '',
            'recintos' => $recintos ?? []
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
