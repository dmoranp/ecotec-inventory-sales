<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/VentaService.php';

class VentaController
{
    private $ventaService;

    public function __construct()
    {
        global $conn;
        $this->ventaService = new VentaService($conn);
    }

    public function index()
    {
        return $this->ventaService->obtenerVentas();
    }

    public function registrar($datos)
    {
        $producto_id = $datos['producto_id'] ?? '';
        $cantidad = $datos['cantidad'] ?? '';

        return $this->ventaService->registrarVenta($producto_id, $cantidad);
    }

    public function obtenerProductos()
    {
        global $conn;
        return Producto::obtenerTodos($conn);
    }
}
?>
