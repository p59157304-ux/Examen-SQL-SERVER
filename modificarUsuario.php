<?php
require_once 'conexion.php'; 
$mostrar_lista = true;
$mensaje = "";

if (isset($_POST['guardar'])) {
    try {
        $id_usuario     = intval($_POST['id_usuario']);
        $nombre_usuario = $_POST['nombre_usuario'];
        $email          = $_POST['email'];
        $tipo_usuario   = $_POST['tipo_usuario'];

        $sql = "{call ActualizarUsuario(?, ?, ?, ?)}";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt->execute([$id_usuario, $nombre_usuario, $email, $tipo_usuario])) {
            $mensaje = "✅ Usuario actualizado correctamente.";
            $mostrar_lista = true; 
        }
    } catch (PDOException $e) {
        $mensaje = "❌ Error al actualizar: " . $e->getMessage();
    }
}

if (isset($_POST['modificar'])) {
    if (isset($_POST['ids']) && count($_POST['ids']) == 1) {
        try {
            $id_editar = intval($_POST['ids'][0]);
            $sql_buscar = "SELECT * FROM Usuarios WHERE ID_Usuario = ?";
            $stmt = $conexion->prepare($sql_buscar);
            $stmt->execute([$id_editar]);
            
            if ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mostrar_lista = false; 
            } else {
                $mensaje = "❌ Error al recuperar los datos del usuario.";
            }
        } catch (PDOException $e) {
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    } else {
        $mensaje = "⚠️ Por favor, seleccione exactamente un usuario para modificar.";
    }           
}

//Lógica del buscador y listado
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : "";
try {
    if (!empty($busqueda)) {
        $sql_lista = "SELECT * FROM Usuarios 
                      WHERE Nombre_Usuario LIKE ? 
                      OR Email LIKE ? 
                      OR Tipo_Usuario LIKE ? 
                      ORDER BY ID_Usuario DESC";
        $consulta = $conexion->prepare($sql_lista);
        $termino = "%$busqueda%";
        $consulta->execute([$termino, $termino, $termino]);
    } else {
        $sql_lista = "SELECT * FROM Usuarios ORDER BY ID_Usuario DESC";
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
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="container">
        <h2>Gestión de Cuentas: Modificar Usuario</h2>

        <?php if ($mensaje): ?>
            <p style="color:blue; font-weight: bold;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <?php if (!$mostrar_lista && isset($usuario)): ?>
            <h3>Editar Datos de: <?php echo htmlspecialchars($usuario['Nombre_Usuario']); ?></h3>
            <form method="post" action="modificarUsuario.php">
                <input type="hidden" name="id_usuario" value="<?php echo $usuario['ID_Usuario']; ?>">

                <label>Nombre de Usuario</label><br>
                <input type="text" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['Nombre_Usuario']); ?>" required><br><br>

                <label>Correo Electrónico</label><br>
                <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['Email']); ?>" required><br><br>

                <label>Tipo de Usuario (Rol)</label><br>
                <select name="tipo_usuario" required>
                    <option value="Administrador" <?php echo ($usuario['Tipo_Usuario'] == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="Lector" <?php echo ($usuario['Tipo_Usuario'] == 'Lector') ? 'selected' : ''; ?>>Lector</option>
                    <option value="Editor" <?php echo ($usuario['Tipo_Usuario'] == 'Editor') ? 'selected' : ''; ?>>Editor</option>
                </select><br><br>

                <input type="submit" name="guardar" value="Guardar Cambios">
                <a href="modificarUsuario.php"><button type="button">Cancelar</button></a>
            </form>

        <?php else: ?>
            <form method="get" action="modificarUsuario.php">
                <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por nombre, email o rol">
                <input type="submit" value="Buscar">
                <a href="modificarUsuario.php"><button type="button">Ver Todos</button></a>
            </form>
            <br>

            <form method="post" action="modificarUsuario.php">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Sel</th>   
                            <th>ID</th>
                            <th>Nombre Usuario</th>
                            <th>Email</th>
                            <th>Tipo Usuario</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($columna = $consulta->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $columna['ID_Usuario']; ?>"></td>
                                <td><?php echo $columna['ID_Usuario']; ?></td>
                                <td><?php echo htmlspecialchars($columna['Nombre_Usuario']); ?></td>
                                <td><?php echo htmlspecialchars($columna['Email']); ?></td>
                                <td><?php echo $columna['Tipo_Usuario']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($columna['Fecha_Registro'])); ?></td>
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
            <a href="mostrarUsuarios.php">Volver a la lista</a> | 
            <a href="adicionarUsuario.php">Nuevo Usuario</a> | 
            <a href="eliminarUsuario.php">Eliminar</a>
        </div>
    </div>
</body>
</html>