<?php require 'db.php'; $correo = $_POST['correo']; 
$contrasena = $_POST['contrasena']; 
$stmt = $conn->prepare("SELECT id, nombre, contrasena FROM usuarios WHERE correo = ?"); 
$stmt->bind_param("s", $correo); $stmt->execute(); $result = $stmt->get_result(); 
if ($user = $result->fetch_assoc()) { if (password_verify($contrasena, $user['contrasena'])) { echo "Inicio de sesión exitoso. Bienvenido, " . $user['nombre']; 
// Puedes usar sesiones aquí // session_start(); 
// $_SESSION['usuario'] = $user['nombre']; 
} 
else { echo "Contraseña incorrecta."; 
} 
} else { echo "Correo no registrado."; } $stmt->close(); $conn->close(); ?>