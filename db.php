<?php
// db.php
// Líneas temporales para DEBUGGING:
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php'
// Parámetros de conexión
$host = "localhost";
$user = "root"; 
$password = ""; 
$dbname = "sportrulescenter";
$port = 3305; // ¡IMPORTANTE! El puerto 3305 debe ir aquí

// Inicializa la conexión
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Manejo de errores de conexión
if ($conn->connect_error) { 
    // Si la conexión falla, se devuelve un error JSON
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error', 
        'message' => '❌ Error de conexión a la base de datos: ' . $conn->connect_error
    ]);
    exit(); // Detener el script si falla la conexión
} 
// Opcional: Establecer juego de caracteres para evitar problemas de acentos (utf8mb4)
$conn->set_charset("utf8mb4");

// Si la conexión es exitosa, el script simplemente continúa (no imprime nada).

?>