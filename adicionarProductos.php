<?php
require_once 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $nombre      = $_POST['nombre_producto'];
        $descripcion = $_POST['descripcion'];
        $num_lote    = $_POST['num_lote'];
        $cantidad    = $_POST['cantidad'];
        $precio      = $_POST['precio'];

        // Llamamos al procedimiento almacenado de SQL Server
        // El SP InsertarProducto recibe: nombre, descripcion, num_lote, cantidad, precio
        $sql = "{call InsertarProducto(?, ?, ?, ?, ?)}";
        $stmt = $conexion->prepare($sql);

        $resultado = $stmt->execute([
            $nombre, 
            $descripcion, 
            $num_lote, 
            $cantidad, 
            $precio
        ]);

        if ($resultado) {
            $mensaje = "<p style='color: green; font-weight: bold;'>✅ Producto registrado exitosamente en el inventario.</p>";
        }
    } catch (PDOException $e) {
        $mensaje = "<p style='color: red; font-weight: bold;'>❌ Error al registrar: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario - Adicionar Producto</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="container">
        <h2>Registrar Nuevo Producto en Almacén</h2>
        
        <?php echo $mensaje; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="nombre_producto">Nombre del Producto:</label><br>
            <input type="text" id="nombre_producto" name="nombre_producto" placeholder="Ej. Candado Inteligente" required><br><br>

            <label for="descripcion">Descripción:</label><br>
            <textarea id="descripcion" name="descripcion" rows="3" placeholder="Detalles del producto..."></textarea><br><br>

            <label for="num_lote">Número de Lote:</label><br>
            <input type="text" id="num_lote" name="num_lote" placeholder="Ej. LOTE-2024-001" required><br><br>

            <label for="cantidad">Cantidad (Stock):</label><br>
            <input type="number" id="cantidad" name="cantidad" min="0" required><br><br>

            <label for="precio">Precio Unitario (Bs):</label><br>
            <input type="number" step="0.01" id="precio" name="precio" min="0" required><br><br>

            <input type="submit" value="Guardar Producto">
        </form>
        
        <hr>
        <a href="mostrarProductos.php">Ver Inventario Completo</a>
    </div>
</body>
</html>