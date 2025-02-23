<?php
require_once dirname(__DIR__).'/models/Recinto.php';

class RecintoController {

    private $recintoModel;

    public function __construct() {
        $this->recintoModel = new Recinto();
    }

    public function crearRecinto($data) {
        // Validación de datos
        if (empty($data['mapa_svg_url']) && empty($data['mapa_svg_data'])) {
            return ['error' => 'Se requiere una URL de SVG o datos en base64 del SVG.'];
        }

        // Preparación de los datos del recinto
        $recintoData = [
            'nombre' => $data['nombre'],
            'ciudad' => $data['ciudad'],
            'estado' => $data['estado'],
            'capacidad' => (int)$data['capacidad'],
            'activo' => (bool)$data['activo'],
            'mapa_svg_url' => $data['mapa_svg_url'] ?? null,
            'mapa_svg_data' => $data['mapa_svg_data'] ?? null,
            'zonas' => []
        ];

        foreach ($data['zonas'] as $zona) {
            $recintoData['zonas'][] = [
                'nombre_zona' => $zona['nombre_zona'],
                'tipo' => $zona['tipo'],
                'capacidad' => (int)$zona['capacidad'],
                'precio_default' => (float)$zona['precio_default'],
                'descripcion' => $zona['descripcion']
            ];
        }

        // Insertar el recinto en la base de datos
        $result = $this->recintoModel->crearRecinto($recintoData);
        return ['insertedId' => (string)$result->getInsertedId()];
    }

    public function obtenerRecintos() {
        return $this->recintoModel->obtenerRecintos();
    }

    public function obtenerRecintoPorId($id) {
        return $this->recintoModel->obtenerRecintoPorId($id);
    }

    public function actualizarRecinto($id, $data) {
        // Validación y procesamiento de los datos antes de la actualización
        $recintoData = [
            'nombre' => $data['nombre'],
            'ciudad' => $data['ciudad'],
            'estado' => $data['estado'],
            'capacidad' => (int)$data['capacidad'],
            'activo' => (bool)$data['activo'],
            'mapa_svg_url' => $data['mapa_svg_url'],
            'zonas' => []
        ];

        foreach ($data['zonas'] as $zona) {
            $recintoData['zonas'][] = [
                'nombre_zona' => $zona['nombre_zona'],
                'tipo' => $zona['tipo'],
                'capacidad' => (int)$zona['capacidad'],
                'precio_default' => (float)$zona['precio_default'],
                'descripcion' => $zona['descripcion']
            ];
        }

        // Actualizar el recinto en la base de datos
        $this->recintoModel->actualizarRecinto($id, $recintoData);
        return ['status' => 'updated'];
    }

    public function eliminarRecinto($id) {
        // Eliminar el recinto en la base de datos
        $this->recintoModel->eliminarRecinto($id);
        return ['status' => 'deleted'];
    }
}
?>
