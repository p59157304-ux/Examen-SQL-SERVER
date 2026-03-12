<?php
require_once 'conexion.php'; 
$mensaje = "";

//Lógica para eliminar pedidos seleccionados
if (isset($_POST['eliminar']) && isset($_POST['id'])) {
    try {
        $ids_a_eliminar = $_POST['id'];
        if (!empty($ids_a_eliminar)) {
            // Preparamos el procedimiento almacenado
            $stmt = $conexion->prepare("{call EliminarPedido(?)}");
            
            foreach ($ids_a_eliminar as $id_p) {
                $stmt->execute([intval($id_p)]);
            }
            $mensaje = "<p style='color: green; font-weight: bold;'>✅ Pedidos eliminados correctamente.</p>";
        }
    } catch (PDOException $e) {
        $mensaje = "<p style='color: red;'>❌ Error al eliminar: " . $e->getMessage() . "</p>";
    }
}

//Lógica para el buscador (ID o Fecha)
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : "";
try {
    if (!empty($busqueda)) {
        $sql = "SELECT * FROM Pedidos 
                WHERE CAST(ID_Pedido AS VARCHAR) LIKE ? 
                OR CAST(Fecha AS VARCHAR) LIKE ? 
                ORDER BY Fecha DESC";
        $consulta = $conexion->prepare($sql);
        $termino = "%$busqueda%";
        $consulta->execute([$termino, $termino]);
    } else {
        $sql = "SELECT * FROM Pedidos ORDER BY Fecha DESC";
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
    <title>Eliminar Pedidos</title>
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
        <h2>Eliminar Pedidos / Ventas</h2>

        <?php echo $mensaje; ?>

        <form method="get" action="eliminarPedidos.php">
            <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por ID o fecha (YYYY-MM-DD)">
            <input type="submit" value="Buscar">
            <a href="eliminarPedidos.php"><button type="button">Ver Todos</button></a>
        </form>
        <br>

        <form method="post" action="eliminarPedidos.php">
            <table border="1">
                <thead>
                    <tr>
                        <th><input type="checkbox" onClick="seleccionarTodos(this)"></th>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Total (Bs)</th>
                        <th>ID Producto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hayRegistros = false;
                    while ($columna = $consulta->fetch(PDO::FETCH_ASSOC)) {
                        $hayRegistros = true;
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='id[]' value='" . $columna['ID_Pedido'] . "'></td>";
                        echo "<td>" . $columna['ID_Pedido'] . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($columna['Fecha'])) . "</td>";
                        echo "<td>" . number_format($columna['Total'], 2) . "</td>";
                        echo "<td>" . $columna['id_producto'] . "</td>";
                        echo "</tr>";
                    }

                    if (!$hayRegistros) {
                        echo "<tr><td colspan='5'>No se encontraron pedidos registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <input type="submit" name="eliminar" value="Eliminar Seleccionados" 
                   onclick="return confirm('¿Está seguro de que desea eliminar los pedidos seleccionados?');"
                   style="background-color: #d9534f; color: white; padding: 10px; border: none; cursor: pointer;">
        </form>
        <br>
        <a href="mostrarPedidos.php">Volver a lista de pedidos</a> | 
        <a href="adicionarPedidos.php">Registrar Nueva Venta</a>
    </div>
</body>
</html>