<?php
require_once 'conexion.php'; 

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $nombre_usuario = $_POST['nombre_usuario'];
        $password       = $_POST['contrasena']; 
        $email          = $_POST['email'];
        $tipo_usuario   = $_POST['tipo_usuario'];

        $sql = "{call InsertarUsuario(?, ?, ?, ?)}";
        $stmt = $conexion->prepare($sql);

        $resultado = $stmt->execute([
            $nombre_usuario, 
            $password, 
            $email, 
            $tipo_usuario
        ]);

        if ($resultado) {
            $mensaje = "<p class='success'>✅ Usuario creado exitosamente.</p>";
        }
    } catch (PDOException $e) {
        $mensaje = "<p class='error'>❌ Error al registrar usuario: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Usuario</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="container">
        <h2>Adicionar Nuevo Usuario</h2>
        
        <?php echo $mensaje; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="nombre_usuario">Nombre de Usuario:</label><br>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required><br><br>

            <label for="contrasena">Contraseña:</label><br>
            <input type="password" id="contrasena" name="contrasena" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <label for="tipo_usuario">Tipo de Usuario:</label><br>
            <select id="tipo_usuario" name="tipo_usuario" required>
                <option value="" disabled selected>Seleccione una opción</option> 
                <option value="Administrador">Administrador</option>
                <option value="Lector">Lector</option>
                <option value="Editor">Editor</option>
            </select><br><br>

            <input type="submit" value="Adicionar Usuario">
        </form>
        <br>
        <a href="mostrarUsuarios.php">Ver lista de Usuarios</a>
    </div>
</body>
</html>