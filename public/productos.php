<?php
require_once __DIR__ . '/../controllers/ProductoController.php';

$controller = new ProductoController();
$mensaje = '';
$errores = [];
$productoEditar = null;

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $resultado = $controller->eliminar($id);
    if ($resultado['exito']) {
        $mensaje = $resultado['mensaje'];
    } else {
        $errores = $resultado['errores'];
    }
}

// Cargar producto para editar
if (isset($_GET['editar'])) {
    $id = (int) $_GET['editar'];
    $productoEditar = $controller->obtener($id);
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datos = [
        'nombre' => $_POST['nombre'] ?? '',
        'descripcion' => $_POST['descripcion'] ?? '',
        'precio' => $_POST['precio'] ?? '',
        'stock' => $_POST['stock'] ?? ''
    ];

    if (isset($_POST['id']) && $_POST['id'] !== '') {
        $resultado = $controller->actualizar((int) $_POST['id'], $datos);
    } else {
        $resultado = $controller->crear($datos);
    }

    if ($resultado['exito']) {
        $mensaje = $resultado['mensaje'];
        $productoEditar = null;
    } else {
        $errores = $resultado['errores'];
        $productoEditar = $datos;
        if (isset($_POST['id'])) {
            $productoEditar['id'] = $_POST['id'];
        }
    }
}

$productos = $controller->index();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?= $productoEditar ? 'Editar Producto' : 'Nuevo Producto' ?></h4>
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
                <input type="hidden" name="id" value="<?= htmlspecialchars($productoEditar['id'] ?? '') ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($productoEditar['nombre'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($productoEditar['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control"
                               value="<?= htmlspecialchars($productoEditar['precio'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control"
                               value="<?= htmlspecialchars($productoEditar['stock'] ?? '') ?>">
                    </div>
                </div>

                <button class="btn btn-success" type="submit">
                    <?= $productoEditar ? 'Actualizar' : 'Guardar' ?>
                </button>
                <?php if ($productoEditar): ?>
                    <a href="productos.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card shadow-lg mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Lista de Productos</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']) ?></td>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><?= htmlspecialchars($p['descripcion']) ?></td>
                            <td>$<?= htmlspecialchars(number_format($p['precio'], 2)) ?></td>
                            <td><?= htmlspecialchars($p['stock']) ?></td>
                            <td>
                                <a href="?editar=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="?eliminar=<?= $p['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
