<?php
require_once 'conexion.php'; 

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $fecha       = $_POST['fecha'];
        $total       = $_POST['total'];
        $id_producto = $_POST['id_producto'];

        $sql = "{call InsertarPedido(?, ?, ?)}";
        $stmt = $conexion->prepare($sql);

        // Ejecutamos pasando los parámetros: fecha, total, id_producto
        $resultado = $stmt->execute([$fecha, $total, $id_producto]);

        if ($resultado) {
            $mensaje = "<p class='success'>✅ Pedido registrado con éxito.</p>";
        }
    } catch (PDOException $e) {
        $mensaje = "<p class='error'>❌ Error al registrar pedido: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Venta - Pedidos</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="container">
        <h2>Registrar Nueva Venta (Pedido)</h2>
        
        <?php echo $mensaje; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="fecha">Fecha de Venta:</label><br>
            <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required><br><br>

            <label for="total">Total de la Venta (Bs):</label><br>
            <input type="number" step="0.01" id="total" name="total" placeholder="0.00" required><br><br>

            <label for="id_producto">ID del Producto Vendido:</label><br>
            <input type="number" id="id_producto" name="id_producto" placeholder="Ej. 1" required><br><br>

            <input type="submit" value="Guardar Pedido">
        </form>
        
        <br>
        <a href="mostrarPedidos.php">Ver Historial de Pedidos</a> | 
        <a href="mostrarProductos.php">Ver Stock de Productos</a>
    </div>
</body>
</html>