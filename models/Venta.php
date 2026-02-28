<?php
class Venta
{
    public $id;
    public $producto_id;
    public $cantidad;
    public $precio_unitario;
    public $total;
    public $errores = [];

    public function __construct($producto_id, $cantidad)
    {
        $this->producto_id = trim($producto_id);
        $this->cantidad = trim($cantidad);
    }

    public function validar()
    {
        if (!ctype_digit($this->producto_id) || $this->producto_id <= 0) {
            $this->errores[] = "Debe seleccionar un producto";
        }

        if (!ctype_digit($this->cantidad) || $this->cantidad <= 0) {
            $this->errores[] = "La cantidad debe ser mayor a 0";
        }

        return empty($this->errores);
    }

    public function guardar($conn)
    {
        $sql = "INSERT INTO ventas(producto_id, cantidad, precio_unitario, total) VALUES(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidd", $this->producto_id, $this->cantidad, $this->precio_unitario, $this->total);
        return $stmt->execute();
    }

    public static function obtenerTodas($conn)
    {
        $sql = "SELECT v.*, p.nombre as producto_nombre
                FROM ventas v
                INNER JOIN productos p ON v.producto_id = p.id
                ORDER BY v.id DESC";
        $resultado = $conn->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public static function obtenerPorId($conn, $id)
    {
        $sql = "SELECT v.*, p.nombre as producto_nombre
                FROM ventas v
                INNER JOIN productos p ON v.producto_id = p.id
                WHERE v.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>
