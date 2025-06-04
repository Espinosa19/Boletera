<?php

require_once dirname(__DIR__) . '/models/Categoria.php';
class CategoriaController
{
    private $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new Categoria();
    }

    public function listarCategorias()
    {
        return $this->categoriaModel->obtenerTodas();
    }

    public function obtenerCategoriaPorId($id)
    {
        return $this->categoriaModel->obtenerPorId($id);
    }

    public function agregarCategoria($nombre, $descripcion, $estado, $subcategorias)
{
    $nombre = trim($nombre);
    $descripcion = trim($descripcion);
    $estado = trim($estado);
    $subcategorias = is_array($subcategorias) ? array_map('trim', $subcategorias) : [];

    $resultado =$this->categoriaModel->insertarCategoria($nombre, $descripcion, $estado, $subcategorias);
    if ($resultado) {
        return true;
    } else {
        return false;
    }
}


    public function actualizarCategoria($id, $nombre, $descripcion, $estado, $subcategorias)
    {
        $nombre = trim($nombre);
        $descripcion = trim($descripcion);
        $estado = trim($estado);
        $subcategorias = is_array($subcategorias) ? array_map('trim', $subcategorias) : [];

        return $this->categoriaModel->actualizarCategoria($id, $nombre, $descripcion, $estado, $subcategorias);
    }

    public function eliminarCategoria($id)
    {
        return $this->categoriaModel->eliminarCategoria($id);
    }
}