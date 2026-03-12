<?php
require_once 'conexion.php'; // Conexión PDO a SQL Server
$mensaje = "";

// 1. Lógica para eliminar registros seleccionados
if (isset($_POST['eliminar']) && isset($_POST['id'])) {
    try {
        $ids_a_eliminar = $_POST['id'];
        if (!empty($ids_a_eliminar)) {
            // Preparamos el procedimiento una sola vez por eficiencia
            $stmt = $conexion->prepare("{call EliminarProducto(?)}");
            
            foreach ($ids_a_eliminar as $id_prod) {
                $stmt->execute([intval($id_prod)]);
            }
            $mensaje = "<p class='success'>✅ Productos eliminados correctamente.</p>";
        }
    } catch (PDOException $e) {
        $mensaje = "<p class='error'>❌ Error al eliminar: " . $e->getMessage() . "</p>";
    }
}

// 2. Lógica para el buscador (Adaptado a Productos)
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : "";
try {
    if (!empty($busqueda)) {
        // Usamos parámetros para evitar inyección SQL
        $sql = "SELECT * FROM Productos WHERE nombre_producto LIKE ? OR num_lote LIKE ? ORDER BY id_producto DESC";
        $consulta = $conexion->prepare($sql);
        $termino = "%$busqueda%";
        $consulta->execute([$termino, $termino]);
    } else {
        $sql = "SELECT * FROM Productos ORDER BY id_producto DESC";
        $consulta = $conexion->query($sql);
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Inventario - Eliminar</title>
    <link rel="stylesheet" href="css/misestilos.css">
    <script>
        function seleccionarTodos(source) {
            checkboxes = document.getElementsByName('id[]');
            for(var i=0, n=checkboxes.length; i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Gestión de Inventario: Eliminar Productos</h2>
        
        <?php echo $mensaje; ?>

        <form method="get" action="eliminarProductos.php" class="search-form">
            <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por nombre o lote...">
            <input type="submit" value="Buscar">
            <a href="eliminarProductos.php"><button type="button">Ver Todos</button></a>
        </form>
        <br>

        <form method="post" action="eliminarProductos.php">
            <table border="1">
                <thead>
                    <tr>
                        <th><input type="checkbox" onClick="seleccionarTodos(this)"></th>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Lote</th>
                        <th>Stock</th>
                        <th>Precio (Bs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hayRegistros = false;
                    while ($columna = $consulta->fetch(PDO::FETCH_ASSOC)) {
                        $hayRegistros = true;
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='id[]' value='" . $columna['id_producto'] . "'></td>";
                        echo "<td>" . $columna['id_producto'] . "</td>";
                        echo "<td>" . htmlspecialchars($columna['nombre_producto']) . "</td>";
                        echo "<td>" . htmlspecialchars($columna['num_lote']) . "</td>";
                        echo "<td>" . $columna['cantidad'] . "</td>";
                        echo "<td>" . number_format($columna['precio'], 2) . "</td>";
                        echo "</tr>";
                    }

                    if (!$hayRegistros) {
                        echo "<tr><td colspan='6'>No se encontraron productos en el inventario.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <input type="submit" name="eliminar" value="Eliminar Seleccionados" 
                   class="btn-danger"
                   onclick="return confirm('¿Está seguro de que desea eliminar los productos seleccionados?');">
        </form>

        <br>
        <div class="footer-links">
            <a href="mostrarProductos.php">Volver al Inventario</a> | 
            <a href="adicionarProductos.php">Adicionar Nuevo Producto</a>
        </div>
    </div>
</body>
</html>