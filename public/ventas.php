<?php
require_once __DIR__ . '/../controllers/VentaController.php';

$controller = new VentaController();
$mensaje = '';
$errores = [];

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datos = [
        'producto_id' => $_POST['producto_id'] ?? '',
        'cantidad' => $_POST['cantidad'] ?? ''
    ];

    $resultado = $controller->registrar($datos);

    if ($resultado['exito']) {
        $mensaje = $resultado['mensaje'];
    } else {
        $errores = $resultado['errores'];
    }
}

$productos = $controller->obtenerProductos();
$ventas = $controller->index();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-primary">← Volver al Inicio</a>
    </div>

    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Nueva Venta</h4>
        </div>
        <div class="card-body">
            <?php if ($mensaje): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errores as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Producto</label>
                    <select name="producto_id" class="form-select">
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['nombre']) ?> - $<?= number_format($p['precio'], 2) ?> (Stock: <?= $p['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" min="1">
                </div>

                <button class="btn btn-success" type="submit">Registrar Venta</button>
            </form>
        </div>
    </div>

    <div class="card shadow-lg mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Historial de Ventas</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td><?= htmlspecialchars($v['id']) ?></td>
                            <td><?= htmlspecialchars($v['producto_nombre']) ?></td>
                            <td><?= htmlspecialchars($v['cantidad']) ?></td>
                            <td>$<?= number_format($v['precio_unitario'], 2) ?></td>
                            <td>$<?= number_format($v['total'], 2) ?></td>
                            <td><?= htmlspecialchars($v['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
