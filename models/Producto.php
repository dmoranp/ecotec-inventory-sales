<?php
class Producto
{
    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $stock;
    public $errores = [];

    public function __construct($nombre, $descripcion, $precio, $stock)
    {
        $this->nombre = trim($nombre);
        $this->descripcion = trim($descripcion);
        $this->precio = trim($precio);
        $this->stock = trim($stock);
    }

    public function validar()
    {
        if ($this->nombre === '' || strlen($this->nombre) < 2) {
            $this->errores[] = "El nombre es obligatorio y debe tener al menos 2 caracteres";
        }

        if (!is_numeric($this->precio) || $this->precio <= 0) {
            $this->errores[] = "El precio debe ser mayor a 0";
        }

        if (!ctype_digit($this->stock) && $this->stock !== '0') {
            $this->errores[] = "El stock debe ser un número entero";
        } else {
            $stockInt = (int) $this->stock;
            if ($stockInt < 0) {
                $this->errores[] = "El stock no puede ser negativo";
            }
        }

        return empty($this->errores);
    }

    public function guardar($conn)
    {
        $sql = "INSERT INTO productos(nombre, descripcion, precio, stock) VALUES(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $this->nombre, $this->descripcion, $this->precio, $this->stock);
        return $stmt->execute();
    }

    public static function obtenerTodos($conn)
    {
        $sql = "SELECT * FROM productos ORDER BY id DESC";
        $resultado = $conn->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public static function obtenerPorId($conn, $id)
    {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function actualizar($conn, $id)
    {
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdii", $this->nombre, $this->descripcion, $this->precio, $this->stock, $id);
        return $stmt->execute();
    }

    public static function eliminar($conn, $id)
    {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
