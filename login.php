<?php
session_start();
require_once 'conexion.php';

$error = "";
$exito = "";

if (isset($_GET['logout'])) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    $exito = "Sesión cerrada correctamente.";
    header("Refresh: 2; url=login.php");
}

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['acceder'])) {
    $nombre_usuario = isset($_POST['nombre_usuario']) ? trim($_POST['nombre_usuario']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

    if (empty($nombre_usuario) || empty($contrasena)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        try {
            $sql = "{call ValidarUsuario(?, ?)}";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre_usuario, $contrasena]);

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $_SESSION['usuario_id'] = $usuario['ID_Usuario'];
                $_SESSION['nombre_usuario'] = $usuario['Nombre_Usuario'];
                $_SESSION['tipo_usuario'] = $usuario['Tipo_Usuario'];
                $_SESSION['fecha_login'] = date('Y-m-d H:i:s');

                header("Location: index.php");
                exit;
            } else {
                $error = "Nombre de usuario o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            $error = "Error de conexión: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - UDES</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .contenedor_login {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .formulario {
            display: flex;
            flex-direction: column;
        }

        .grupo_formulario {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #d32f2f;
        }

        .exito {
            color: #388e3c;
            background-color: #e8f5e9;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #388e3c;
        }

        .boton_acceder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .boton_acceder:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .enlace_ayuda {
            text-align: center;
            margin-top: 20px;
        }

        .enlace_ayuda a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .info_sistema {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="contenedor_login">
        <h1>Acceso al Sistema</h1>

        <?php if($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="exito"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>    

        <form method="POST" action="login.php" class="formulario">
            <div class="grupo_formulario">
                <label for="nombre_usuario">Nombre de usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Ingrese su usuario" autofocus required>
            </div>

            <div class="grupo_formulario">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="Ingrese su contraseña" required>
            </div>

            <button type="submit" name="acceder" value="1" class="boton_acceder">Acceder</button>
        </form>

        <div class="enlace_ayuda">
            <p>¿Olvidó su contraseña? <a href="#">Recuperar acceso</a></p>
        </div>

        <div class="info_sistema">
            <p>Sistema de Gestión UDES</p>
            <p>© 2026 - Todos los derechos reservados</p>
        </div>
    </div>

</body>
</html>