<?php
require_once 'conexion.php';
$mensaje = "";

//Lógica para eliminar usuarios seleccionados
if (isset($_POST['eliminar']) && isset($_POST['id'])) {
    try {
        $ids_a_eliminar = $_POST['id'];
        if (!empty($ids_a_eliminar)) {
            $stmt = $conexion->prepare("{call EliminarUsuario(?)}");
            
            foreach ($ids_a_eliminar as $id_usuario) {
                $stmt->execute([intval($id_usuario)]);
            }
            $mensaje = "<p style='color: green; font-weight: bold;'>✅ Usuarios eliminados correctamente.</p>";
        }
    } catch (PDOException $e) {
        $mensaje = "<p style='color: red;'>❌ Error al eliminar: " . $e->getMessage() . "</p>";
    }
}

$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : "";
try {
    if (!empty($busqueda)) {
        $sql = "SELECT * FROM Usuarios 
                WHERE Nombre_Usuario LIKE ? 
                OR Email LIKE ? 
                OR Tipo_Usuario LIKE ? 
                ORDER BY ID_Usuario DESC";
        $consulta = $conexion->prepare($sql);
        $termino = "%$busqueda%";
        $consulta->execute([$termino, $termino, $termino]);
    } else {
        $sql = "SELECT * FROM Usuarios ORDER BY ID_Usuario DESC";
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
    <title>Gestión de Usuarios - Eliminar</title>
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
        <h2>Eliminar Usuarios del Sistema</h2>

        <?php echo $mensaje; ?>

        <form method="get" action="eliminarUsuario.php">
            <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por nombre, email o rol">
            <input type="submit" value="Buscar">
            <a href="eliminarUsuario.php"><button type="button">Ver Todos</button></a>
        </form>
        <br>

        <form method="post" action="eliminarUsuario.php">
            <table border="1">
                <thead>
                    <tr>
                        <th><input type="checkbox" onClick="seleccionarTodos(this)"></th>
                        <th>ID</th>
                        <th>Nombre Usuario</th>
                        <th>Email</th>
                        <th>Tipo Usuario</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hayRegistros = false;
                    while ($columna = $consulta->fetch(PDO::FETCH_ASSOC)) {
                        $hayRegistros = true;
                        echo "<tr>";
                        echo "<td><input type=\"checkbox\" name=\"id[]\" value=\"" . $columna['ID_Usuario'] . "\"></td>";
                        echo "<td>" . $columna['ID_Usuario'] . "</td>";
                        echo "<td>" . htmlspecialchars($columna['Nombre_Usuario']) . "</td>";
                        echo "<td>" . htmlspecialchars($columna['Email']) . "</td>";
                        echo "<td>" . $columna['Tipo_Usuario'] . "</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($columna['Fecha_Registro'])) . "</td>";
                        echo "</tr>";
                    }

                    if (!$hayRegistros) {
                        echo "<tr><td colspan='6'>No se encontraron usuarios en el sistema.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <input type="submit" name="eliminar" value="Eliminar Seleccionados" 
                   onclick="return confirm('¿Está seguro de que desea eliminar los usuarios seleccionados?');"
                   style="background-color: #d9534f; color: white; padding: 8px 15px; border: none; cursor: pointer;">
        </form>
        <br>
        <div class="footer-links">
            <a href="mostrarUsuarios.php">Volver a la lista</a> | 
            <a href="adicionarUsuario.php">Nuevo Usuario</a>
        </div>
    </div>
</body>
</html>