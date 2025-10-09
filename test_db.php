<?php
// test_db.php

$host = "localhost";
$user = "root"; 
$password = ""; // Si tienes contraseña, ponla aquí
$dbname = "sportrulescenter";
$port = 3305; // ASEGÚRATE DE QUE ESTE PUERTO SEA EL CORRECTO (3305 o 3306)

// Intento de conexión
$conn = new mysqli($host, $user, $password, $dbname, $port);

if ($conn->connect_error) { 
    // Si la conexión falla, imprime el error de forma CLARA
    echo "<h1>❌ Error Crítico de Conexión a MySQL:</h1>";
    echo "<p>Causa: " . $conn->connect_error . "</p>";
    echo "<p>Verifica que MySQL esté corriendo en XAMPP y el puerto ({$port}) sea correcto.</p>";
    exit();
} 
else { 
    // Si la conexión es exitosa, lo confirma.
    echo "<h1>✅ Éxito: Conexión a la base de datos {$dbname} realizada correctamente.</h1>"; 
} 

$conn->close();

?>