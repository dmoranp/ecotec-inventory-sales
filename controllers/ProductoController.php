<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';

class ProductoController
{
    public function index()
    {
        global $conn;
        return Producto::obtenerTodos($conn);
    }

    public function crear($datos)
    {
        global $conn;
        $producto = new Producto(
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['precio'] ?? '',
            $datos['stock'] ?? ''
        );

        if ($producto->validar()) {
            if ($producto->guardar($conn)) {
                return ['exito' => true, 'mensaje' => 'Producto creado correctamente'];
            }
            return ['exito' => false, 'errores' => ['Error al guardar en la base de datos']];
        }

        return ['exito' => false, 'errores' => $producto->errores];
    }

    public function obtener($id)
    {
        global $conn;
        return Producto::obtenerPorId($conn, $id);
    }

    public function actualizar($id, $datos)
    {
        global $conn;
        $producto = new Producto(
            $datos['nombre'] ?? '',
            $datos['descripcion'] ?? '',
            $datos['precio'] ?? '',
            $datos['stock'] ?? ''
        );

        if ($producto->validar()) {
            if ($producto->actualizar($conn, $id)) {
                return ['exito' => true, 'mensaje' => 'Producto actualizado correctamente'];
            }
            return ['exito' => false, 'errores' => ['Error al actualizar en la base de datos']];
        }

        return ['exito' => false, 'errores' => $producto->errores];
    }

    public function eliminar($id)
    {
        global $conn;
        if (Producto::eliminar($conn, $id)) {
            return ['exito' => true, 'mensaje' => 'Producto eliminado correctamente'];
        }
        return ['exito' => false, 'errores' => ['Error al eliminar el producto']];
    }
}
?>
