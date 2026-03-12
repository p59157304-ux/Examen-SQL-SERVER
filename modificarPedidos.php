<?php
require_once 'conexion.php'; 
$mostrar_lista = true;
$mensaje = "";

if (isset($_POST['guardar'])) {
    try {
        $id_pedido   = intval($_POST['id_pedido']);
        $fecha       = $_POST['fecha'];
        $total       = floatval($_POST['total']);
        $id_producto = intval($_POST['id_producto']);

        $sql = "{call ActualizarPedido(?, ?, ?, ?)}";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt->execute([$id_pedido, $fecha, $total, $id_producto])) {
            $mensaje = "✅ Pedido actualizado correctamente.";
            $mostrar_lista = true; 
        }
    } catch (PDOException $e) {
        $mensaje = "❌ Error al actualizar: " . $e->getMessage();
    }
}

//Lógica para mostrar el formulario de edición
if (isset($_POST['modificar'])) {
    if (isset($_POST['ids']) && count($_POST['ids']) == 1) {
        try {
            $id_editar = intval($_POST['ids'][0]);
            $sql_buscar = "SELECT * FROM Pedidos WHERE ID_Pedido = ?";
            $stmt = $conexion->prepare($sql_buscar);
            $stmt->execute([$id_editar]);
            
            if ($pedido = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mostrar_lista = false; 
            } else {
                $mensaje = "❌ Error al recuperar los datos del pedido.";
            }
        } catch (PDOException $e) {
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    } else {
        $mensaje = "⚠️ Por favor, seleccione exactamente un pedido para modificar.";
    }           
}

//Lógica del buscador y listado
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : "";
try {
    if (!empty($busqueda)) {
        $sql_lista = "SELECT * FROM Pedidos 
                      WHERE CAST(ID_Pedido AS VARCHAR) LIKE ? 
                      OR CAST(Fecha AS VARCHAR) LIKE ? 
                      ORDER BY ID_Pedido DESC";
        $consulta = $conexion->prepare($sql_lista);
        $termino = "%$busqueda%";
        $consulta->execute([$termino, $termino]);
    } else {
        $sql_lista = "SELECT * FROM Pedidos ORDER BY ID_Pedido DESC";
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
    <title>Modificar Pedido</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="container">
        <h2>Gestión de Ventas: Modificar Pedido</h2>

        <?php if ($mensaje): ?>
            <p style="color:blue; font-weight: bold;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <?php if (!$mostrar_lista && isset($pedido)): ?>
            <h3>Editar Datos del Pedido #<?php echo $pedido['ID_Pedido']; ?></h3>
            <form method="post" action="modificarPedidos.php">
                <input type="hidden" name="id_pedido" value="<?php echo $pedido['ID_Pedido']; ?>">

                <label>Fecha de Venta</label><br>
                <input type="date" name="fecha" value="<?php echo date('Y-m-d', strtotime($pedido['Fecha'])); ?>" required><br><br>

                <label>Total (Bs)</label><br>
                <input type="number" step="0.01" name="total" value="<?php echo $pedido['Total']; ?>" required><br><br>

                <label>ID Producto</label><br>
                <input type="number" name="id_producto" value="<?php echo $pedido['id_producto']; ?>" required><br><br>

                <input type="submit" name="guardar" value="Guardar Cambios">
                <a href="modificarPedidos.php"><button type="button">Cancelar</button></a>
            </form>

        <?php else: ?>
            <form method="get" action="modificarPedidos.php">
                <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por ID o fecha">
                <input type="submit" value="Buscar">
                <a href="modificarPedidos.php"><button type="button">Ver Todos</button></a>
            </form>
            <br>

            <form method="post" action="modificarPedidos.php">
            <table border="1">
                <thead>
                    <tr>
                        <th>Sel</th>   
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Total (Bs)</th>
                        <th>ID Producto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($columna = $consulta->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?php echo $columna['ID_Pedido']; ?>"></td>
                            <td><?php echo $columna['ID_Pedido']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($columna['Fecha'])); ?></td>
                            <td><?php echo number_format($columna['Total'], 2); ?></td>
                            <td><?php echo $columna['id_producto']; ?></td>
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
            <a href="mostrarPedidos.php">Volver a lista</a> | 
            <a href="adicionarPedidos.php">Adicionar</a> | 
            <a href="eliminarPedidos.php">Eliminar</a>
        </div>
    </div>
</body>
</html>