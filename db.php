

<?php $host = "localhost"; 
$user = "root"; 
$password = ""; 
$dbname = "sportrulescenter"; 
$conn = new mysqli($host, $user, $password, $dbname); 
if ($conn->connect_error) { die("Conexión fallida: " . $conn->connect_error); } 
else { echo "✅ Conexión a la base de datos exitosa."; } ?>