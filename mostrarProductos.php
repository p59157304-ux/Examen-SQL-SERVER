 <?php

require_once 'conexion.php';


try {

    $stmt = $conexion->query("EXEC ObtenerProductos");


} catch (PDOException $e) {

    die("Error en la consulta: " . $e->getMessage());

}

?>


<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Inventario de Productos</title>

    <link rel="stylesheet" href="css/misestilos.css">

</head>

<body>

    <h2>Listado de Productos</h2>

    <table>

        <tr>

            <th>ID</th>

            <th>Producto</th>

            <th>Descripción</th>

            <th>Lote</th>

            <th>Precio</th>

            <th>Cantidad</th>

            <th>Vencimiento</th>

        </tr>


    <?php

    // Usar fetch(PDO::FETCH_ASSOC) que es el estándar de PDO

    while($fila = $stmt->fetch(PDO::FETCH_ASSOC)){

        echo "<tr>";

        echo "<td>".$fila['id_producto']."</td>";

        echo "<td>".$fila['nombre_producto']."</td>";

        echo "<td>".$fila['descripcion']."</td>";

        echo "<td>".$fila['num_lote']."</td>";

        echo "<td>".number_format($fila['precio'], 2)." Bs.</td>"; 

        echo "<td>".$fila['cantidad']."</td>";

        echo "<td>".$fila['fecha_vencimiento']."</td>";

        echo "</tr>";

    }

    ?>

    </table>

</body>

</html>


<?php

$conexion = null; 