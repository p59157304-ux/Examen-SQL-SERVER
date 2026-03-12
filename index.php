<?php
session_start();

// Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Salida del sistema
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$nombre_usuario = $_SESSION['nombre_usuario'] ?? 'Usuario';
$tipo_usuario = $_SESSION['tipo_usuario'] ?? 'Lector';

require_once 'conexion.php';

// Inicializar contadores
$total_pedidos = 0;
$total_clientes = 0;
$total_usuarios = 0;
$pedidos_mes = 0;
$total_productos = 0;

if ($conexion) {
    try {
        // 1. Estadísticas de Pedidos
        $stmt1 = $conexion->query("{call ObtenerEstadisticasPedidos}");
        if ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            $total_pedidos = $row['total_pedidos'];
        }
        $stmt1->closeCursor(); 

        // 2. Estadísticas de Clientes
        $stmt2 = $conexion->query("{call ObtenerEstadisticasClientes}");
        if ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $total_clientes = $row['total_clientes'];
        }
        $stmt2->closeCursor();

        // 3. Estadísticas de Usuarios
        $stmt3 = $conexion->query("{call ObtenerEstadisticasUsuarios}");
        if ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            $total_usuarios = $row['total_usuarios'];
        }
        $stmt3->closeCursor();

        // 4. Pedidos del Mes
        $stmt4 = $conexion->query("{call ObtenerPedidosDelMes}");
        $pedidos_mes = $stmt4->rowCount();
        $stmt4->closeCursor();

        // 5. Estadísticas de Productos (NUEVO)
        $stmt5 = $conexion->query("{call ObtenerEstadisticasProductos}");
        if ($row = $stmt5->fetch(PDO::FETCH_ASSOC)) {
            $total_productos = $row['total_productos'];
        }
        $stmt5->closeCursor();

    } catch (PDOException $e) {
        // En producción podrías loguear el error: error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Gestión</title>
    <link rel="stylesheet" href="css/misestilos.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Menú Principal</h2>
            <p>Sistema de Gestión</p>
        </div>
        <nav>
            <ul class="sidebar-nav">
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                
                <li class="nav-section"><span class="nav-section-title"><i class="fas fa-user-circle"></i> Usuario</span></li>
                <li style="padding: 10px 20px; color: rgba(255,255,255,0.8); font-size: 13px;"><strong>User:</strong> <?php echo htmlspecialchars($nombre_usuario); ?></li>
                <li style="padding: 5px 20px; color: rgba(255,255,255,0.6); font-size: 12px;"><strong>Rol:</strong> <?php echo htmlspecialchars($tipo_usuario); ?></li>
                
                <li class="nav-section"><span class="nav-section-title"><i class="fas fa-shopping-cart"></i> Pedidos</span></li>
                <li><a href="mostrarPedidos.php"><i class="fas fa-list"></i> Ver Pedidos</a></li>
                <li><a href="adicionarPedidos.php"><i class="fas fa-plus"></i> Adicionar</a></li>
                <li><a href="modificarPedidos.php"><i class="fas fa-edit"></i> Modificar Pedido</a></li>
                <li><a href="eliminarPedidos.php"><i class="fas fa-trash-alt"></i> Eliminar Pedido</a></li>

                <li class="nav-section"><span class="nav-section-title"><i class="fas fa-boxes"></i> Productos</span></li>
                <li><a href="mostrarProductos.php"><i class="fas fa-layer-group"></i> Ver Productos</a></li>
                <li><a href="adicionarProductos.php"><i class="fas fa-plus-circle"></i> Adicionar Producto</a></li>
                <li><a href="modificarProductos.php"><i class="fas fa-edit"></i> Modificar Producto</a></li>
                <li><a href="eliminarProductos.php"><i class="fas fa-trash-alt"></i> Eliminar Producto</a></li>
                
                <li class="nav-section"><span class="nav-section-title"><i class="fas fa-user-shield"></i> Usuarios</span></li>
                <li><a href="mostrarUsuarios.php"><i class="fas fa-list"></i> Ver Usuarios</a></li>
                <li><a href="adicionarUsuario.php"><i class="fas fa-user-plus"></i> Nuevo Usuario</a></li>
                <li><a href="modificarUsuario.php"><i class="fas fa-user-edit"></i> Modificar Usuario</a></li>
                <li><a href="eliminarUsuario.php"><i class="fas fa-user-times"></i> Eliminar Usuario</a></li>
            </ul>
        </nav>
        <a href="index.php?logout=1" class="boton_salir" onclick="return confirm('¿Cerrar sesión?');">Cerrar Sesión</a>
    </aside>

    <main class="main-content">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Panel de Control</h1>
            <div class="header-info">
                <p>Bienvenido al Sistema de Gestión Integral</p>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-shopping-cart" style="color: #3498db;"></i>
                <h3>Total Pedidos</h3>
                <p><?php echo $total_pedidos; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users" style="color: #2ecc71;"></i>
                <h3>Clientes</h3>
                <p><?php echo $total_clientes; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-boxes" style="color: #e67e22;"></i>
                <h3>Productos</h3>
                <p><?php echo $total_productos; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-shield" style="color: #f1c40f;"></i>
                <h3>Usuarios</h3>
                <p><?php echo $total_usuarios; ?></p>
            </div>
        </div>

        <h2 class="cards-title">Acciones Rápidas</h2>
        <div class="cards-container">

            <a href="mostrarPedidos.php" class="card">
                <div class="card-icon"><i class="fas fa-list-alt"></i></div>
                <h3 class="card-title">Gestionar Pedidos</h3>
                <p class="card-description">Control de ventas y facturación del sistema.</p>
                <button class="card-button">Entrar</button>
            </a>

            <a href="mostrarProductos.php" class="card">
                <div class="card-icon"><i class="fas fa-inventory"></i></div>
                <h3 class="card-title">Inventario</h3>
                <p class="card-description">Administración de productos y existencias.</p>
                <button class="card-button">Entrar</button>
            </a>

            <a href="mostrarUsuarios.php" class="card">
                <div class="card-icon"><i class="fas fa-users-cog"></i></div>
                <h3 class="card-title">Control de Usuarios</h3>
                <p class="card-description">Administración de accesos y roles de personal.</p>
                <button class="card-button">Entrar</button>
            </a>
        </div>

        <div class="footer">
            <p>&copy; 2026 Sistema de Gestión Institucional. Todos los derechos reservados.</p>
        </div>
    </main>
</body>
</html>