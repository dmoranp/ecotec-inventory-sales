<?php
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Producto.php';

class VentaService
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function registrarVenta($producto_id, $cantidad)
    {
        $venta = new Venta($producto_id, $cantidad);

        if (!$venta->validar()) {
            return ['exito' => false, 'errores' => $venta->errores];
        }

        // Obtener producto
        $producto = Producto::obtenerPorId($this->conn, $producto_id);

        if (!$producto) {
            return ['exito' => false, 'errores' => ['El producto no existe']];
        }

        // Validar stock disponible
        if ($producto['stock'] < $cantidad) {
            return ['exito' => false, 'errores' => ['Stock insuficiente. Disponible: ' . $producto['stock']]];
        }

        // Calcular total
        $venta->precio_unitario = $producto['precio'];
        $venta->total = $producto['precio'] * $cantidad;

        // Iniciar transacción
        $this->conn->begin_transaction();

        try {
            // Guardar venta
            if (!$venta->guardar($this->conn)) {
                throw new Exception('Error al guardar la venta');
            }

            // Descontar stock
            $nuevoStock = $producto['stock'] - $cantidad;
            $sql = "UPDATE productos SET stock = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $nuevoStock, $producto_id);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar el stock');
            }

            $this->conn->commit();
            return ['exito' => true, 'mensaje' => 'Venta registrada correctamente'];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ['exito' => false, 'errores' => [$e->getMessage()]];
        }
    }

    public function obtenerVentas()
    {
        return Venta::obtenerTodas($this->conn);
    }
}
?>
