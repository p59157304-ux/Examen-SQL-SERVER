<?php
require_once 'conexion.php'; 
$mostrar_lista = true;
$mensaje = "";

// Lógica de Actualización de Producto
if (isset($_POST['guardar'])) {
    try {
        $id_producto = intval($_POST['id_producto']);
        $nombre      = $_POST['nombre_producto'];
        $descripcion = $_POST['descripcion'];
        $num_lote    = $_POST['num_lote'];
        $cantidad    = intval($_POST['cantidad']);
        $precio      = floatval($_POST['precio']);

        $sql = "{call ActualizarProducto(?, ?, ?, ?, ?, ?)}";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt->execute([$id_producto, $nombre, $descripcion, $num_lote, $cantidad, $precio])) {
            $mensaje = "✅ Producto actualizado correctamente.";
            $mostrar_lista = true;
        }
    } catch (PDOException $e) {
        $mensaje = "❌ Error al actualizar: " . $e->getMessage();
    }
}

// Lógica para mostrar el formulario de edición
if (isset($_POST['modificar'])) {
    if (isset($_POST['ids']) && count($_POST['ids']) == 1) {
        try {
            $id_editar = intval($_POST['ids'][0]);
            $sql_buscar = "SELECT * FROM Productos WHERE id_producto = ?";
            $stmt = $conexion->prepare($sql_buscar);
            $stmt->execute([$id_editar]);
            
            if ($producto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mostrar_lista = false; 
            } else {
                $mensaje = "❌ Error al recuperar los datos del producto.";
            }
        } catch (PDOException $e) {
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    } else {
        $mensaje = "⚠️ Por favor, seleccione exactamente un producto para modificar.";
    }
}

// Lógica del buscador y listado
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : "";
try {
    if (!empty($busqueda)) {
        $sql_lista = "SELECT * FROM Productos WHERE nombre_producto LIKE ? OR num_lote LIKE ? ORDER BY id_producto DESC";
        $consulta = $conexion->prepare($sql_lista);
        $termino = "%$busqueda%";
        $consulta->execute([$termino, $termino]);
    } else {
        $sql_lista = "SELECT * FROM Productos ORDER BY id_producto DESC";
        $consulta = $conexion->query($sql_lista);
    }
} catch (PDOException $e) {
    $mensaje = "Error en la consulta: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Inventario</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="container">
        <h2>Gestión de Inventario: Modificar</h2>

        <?php if ($mensaje): ?>
            <p style="color:blue; font-weight: bold;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <?php if (!$mostrar_lista && isset($producto)): ?>
            <h3>Editar Datos del Producto</h3>
            <form method="post" action="modificarProductos.php">
                <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">

                <label>Nombre del Producto</label><br>
                <input type="text" name="nombre_producto" value="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" required><br><br>

                <label>Descripción</label><br>
                <textarea name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea><br><br>

                <label>Número de Lote</label><br>
                <input type="text" name="num_lote" value="<?php echo htmlspecialchars($producto['num_lote']); ?>" required><br><br>

                <label>Cantidad (Stock)</label><br>
                <input type="number" name="cantidad" value="<?php echo $producto['cantidad']; ?>" required><br><br>

                <label>Precio (Bs)</label><br>
                <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required><br><br>

                <input type="submit" name="guardar" value="Guardar Cambios">
                <a href="modificarProductos.php"><button type="button">Cancelar</button></a>
            </form>

        <?php else: ?>
            <form method="get" action="modificarProductos.php" class="search-form">
                <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por nombre o lote...">
                <input type="submit" value="Buscar">
                <a href="modificarProductos.php"><button type="button">Ver Todos</button></a>
            </form>
            <br>

            <form method="post" action="modificarProductos.php">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Sel</th>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Lote</th>
                            <th>Stock</th>
                            <th>Precio (Bs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($columna = $consulta->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $columna['id_producto']; ?>"></td>
                                <td><?php echo $columna['id_producto']; ?></td>
                                <td><?php echo htmlspecialchars($columna['nombre_producto']); ?></td>
                                <td><?php echo htmlspecialchars($columna['num_lote']); ?></td>
                                <td><?php echo $columna['cantidad']; ?></td>
                                <td><?php echo number_format($columna['precio'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <br>
                <input type="submit" name="modificar" value="Modificar Seleccionado">
            </form>
        <?php endif; ?>

        <br>
        <div class="footer-links">
            <a href="mostrarProductos.php">Ver Inventario</a> | 
            <a href="adicionarProducto.php">Adicionar</a> | 
            <a href="eliminarProductos.php">Eliminar</a>
        </div>
    </div>
</body>
</html>