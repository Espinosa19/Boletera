<?php
require_once dirname(__DIR__) . '/conexiones/ConexionDB.php';
use MongoDB\BSON\ObjectId;

class Categoria
{
    private $collection;
    private $db;

    public function __construct()
    {
        $this->db = ConexionDB::conectar();
        $this->collection = $this->db->selectCollection("categorias");
    }
    public function buscarPorSubcategoria($subcategoria) {
        return $this->collection->findOne([
            'subcategorias' => $subcategoria
        ]);
    }
    public function obtenerTodas()
    {
        return $this->collection->find([])->toArray();
    }

    public function obtenerPorId($id)
    {
        return $this->collection->findOne(['_id' => new ObjectId($id)]);
    }
    
    public function insertarCategoria($nombre, $descripcion, $estado, $subcategorias = [])
    {
        try {
            $nuevaCategoria = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'estado' => $estado,
                'subcategorias' => $subcategorias
            ];

            if ($this->collection->insertOne($nuevaCategoria)) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new Exception('Error al insertar la categoría: ' . $e->getMessage());
        }
    }
    public function actualizarCategoria($id, $nombre, $descripcion, $estado, $subcategorias = [])
    {
        try {
            $filtro = ['_id' => new ObjectId($id)];
            $actualizacion = [
                '$set' => [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'estado' => $estado,
                    'subcategorias' => $subcategorias
                ]
            ];

            return $this->collection->updateOne($filtro, $actualizacion);
        } catch (Exception $e) {
            throw new Exception('Error al actualizar la categoría: ' . $e->getMessage());
        }
    }
    public function eliminarCategoria($id)
    {
        try {
            $filtro = ['_id' => new ObjectId($id)];
            return $this->collection->deleteOne($filtro);
        } catch (Exception $e) {
            throw new Exception('Error al eliminar la categoría: ' . $e->getMessage());
        }
    }
}