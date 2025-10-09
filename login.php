<?php
// login.php

// Incluimos la conexión. Si falla la conexión, el script se detiene en db.php.
require 'db.php';

// Establecemos la cabecera para devolver una respuesta JSON
header('Content-Type: application/json');

// Verificación de método y campos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['correo']) && isset($_POST['contrasena'])) {
    
    $correo = trim($_POST['correo']); 
    // ¡IMPORTANTE! El nombre de la columna en la BD es 'contraseña' (con ñ), no 'contrasena'
    // Como encriptaste la contraseña, recuperamos el hash de la BD.
    
    // 1. Preparamos la consulta para obtener el usuario por correo
    // NOTA: Usamos 'contraseña' (con ñ) si así se llama tu columna en la DB.
    $sql = "SELECT id, nombre, correo, contrasena FROM usuario WHERE correo = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        
        $stmt->bind_param("s", $correo); 
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        
        // 2. Verificamos si se encontró el usuario
        if ($user = $result->fetch_assoc()) { 
            
            $contrasena_ingresada = $_POST['contrasena'];
            $hash_almacenado = $user['contrasena']; // Usamos 'contraseña' para el hash
            
            // 3. Verificamos la contraseña encriptada con password_verify
            if (password_verify($contrasena_ingresada, $hash_almacenado)) { 
                
                // INICIO DE SESIÓN EXITOSO
                
                // Aquí deberías iniciar la sesión si deseas mantener al usuario logueado
                // session_start(); 
                // $_SESSION['user_id'] = $user['id']; 
                // $_SESSION['user_name'] = $user['nombre']; 
                
                echo json_encode([
                    'status' => 'success', 
                    'message' => "👋 Inicio de sesión exitoso. ¡Bienvenido, " . htmlspecialchars($user['nombre']) . "!"
                ]);
            } else { 
                // Contraseña incorrecta
                echo json_encode([
                    'status' => 'error', 
                    'message' => "❌ Contraseña incorrecta."
                ]);
            } 
        } else { 
            // Correo no registrado
            echo json_encode([
                'status' => 'error', 
                'message' => "❌ Correo no registrado."
            ]);
        } 
        
        $stmt->close();
    } else {
        // Error en la preparación de la consulta
        echo json_encode([
            'status' => 'error', 
            'message' => '❌ Error de preparación de consulta: ' . $conn->error
        ]);
    }
    
    $conn->close(); 

} else {
    // Si faltan datos o no es POST
     echo json_encode([
        'status' => 'error', 
        'message' => 'Solicitud incompleta.'
    ]);
}
?>