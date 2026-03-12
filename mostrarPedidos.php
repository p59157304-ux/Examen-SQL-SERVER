<?php
require_once 'conexion.php'; 

try {
    //Llamar al procedimiento almacenado
    $sql = "{call ObtenerPedidosConProducto()}";
    $consulta = $conexion->query($sql);

    if (!$consulta) {
        die("Error al ejecutar el procedimiento.");
    }
} catch (PDOException $e) {
    die("Error de conexión o consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Ventas - Pedidos</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="container">
        <h2>Listado de Pedidos y Detalles de Productos</h2>
        
        <table border="1">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha de Venta</th>         
                    <th>Total (Bs)</th>
                    <th>ID Producto</th>
                    <th>Nombre del Producto</th>
                    <th>Nro. Lote</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Recorrer los resultados con PDO
            while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){
                echo "<tr>";
                echo "<td>" . $fila['ID_Pedido'] . "</td>";
                echo "<td>" . date('d/m/Y H:i', strtotime($fila['Fecha'])) . "</td>";
                echo "<td>" . number_format($fila['Total'], 2) . " Bs</td>";
                echo "<td>" . $fila['id_producto'] . "</td>";
                echo "<td>" . htmlspecialchars($fila['nombre_producto']) . "</td>";
                echo "<td>" . htmlspecialchars($fila['num_lote']) . "</td>";
                echo "</tr>";
            }
            ?> 
            </tbody>
        </table> 

        <br>
        <div class="footer-links">
            <a href="index.php">Volver al Dashboard</a> | 
            <a href="adicionarProductos.php">Nuevo Producto</a>
        </div>
    </div>
</body> 
</html>