<?php
// Configuración de conexión para InfinityFree
$host = "sql208.infinityfree.com";
$user = "if0_41220283";
$pass = "uP7OXwKITLH";
$dbname = "if0_41220283_cuba_ticket";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Establecer el conjunto de caracteres a utf8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    echo "Error en el servidor: " . $e->getMessage();
}
?>
