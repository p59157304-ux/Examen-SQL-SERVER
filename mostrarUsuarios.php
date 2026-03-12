<?php
require_once 'conexion.php'; 

try {
    //Llamar al procedimiento almacenado en SQL Server
    $sql = "{call ObtenerUsuarios}";
    $consulta = $conexion->query($sql);

    if (!$consulta) {
        die("Error al ejecutar el procedimiento de usuarios.");
    }
} catch (PDOException $e) {
    die("Error de conexión o consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Listado</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="container">
        <h2>Listado de Usuarios Registrados</h2>
        
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>         
                    <th>Correo Electrónico</th>
                    <th>Rol / Tipo</th>
                    <th>Fecha de Registro</th>
                </tr>
            </thead>
            <tbody>
            <?php

            $hayUsuarios = false;
            while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){
                $hayUsuarios = true;
                echo "<tr>";
                echo "<td>" . $fila['ID_Usuario'] . "</td>";
                echo "<td>" . htmlspecialchars($fila['Nombre_Usuario']) . "</td>";
                echo "<td>" . htmlspecialchars($fila['Email']) . "</td>";
                
                // Aplicamos un estilo visual según el tipo de usuario
                $claseRol = ($fila['Tipo_Usuario'] == 'Administrador') ? 'bold-text' : '';
                echo "<td class='$claseRol'>" . $fila['Tipo_Usuario'] . "</td>";
                
                echo "<td>" . date('d/m/Y H:i', strtotime($fila['Fecha_Registro'])) . "</td>";
                echo "</tr>";
            }

            if (!$hayUsuarios) {
                echo "<tr><td colspan='5'>No hay usuarios registrados en el sistema.</td></tr>";
            }
            ?> 
            </tbody>
        </table> 

        <br>
        <div class="footer-links">
            <a href="index.php">Volver al Dashboard</a> | 
            <a href="adicionarUsuario.php">Registrar Nuevo Usuario</a>
        </div>
    </div>
</body> 
</html>