<?php
$servidor = "localhost\SQLEXPRESS"; 
$baseDatos = "procealmacsql";     
$usuario = "";                      
$contraseña = "";                   

try {
    // Incluimos TrustServerCertificate para evitar errores de SSL en local
    $dsn = "sqlsrv:Server=$servidor;Database=$baseDatos;Encrypt=yes;TrustServerCertificate=yes";

    // Crear la conexión usando PDO (equivalente a 'new mysqli')
    $conexion = new PDO($dsn, $usuario, $contraseña);

    // Configurar el manejo de errores para que lance excepciones
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // En SQL Server/PDO el charset se suele manejar en el DSN o con este atributo:
    $conexion->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);

    // Opcional: Mostrar mensaje de éxito
    // echo "Conexión a SQL Server realizada correctamente";

} catch (PDOException $e) {
    // Equivalente al connect_error
    die("Error en la conexión: " . $e->getMessage());
}
?>