<?php
// registro.php

// 1. Incluir el archivo de conexión. Si la conexión falla, el script se detiene en db.php.
require 'db.php';

// Establecer la cabecera para devolver una respuesta JSON
header('Content-Type: application/json');

// 2. Verificar que la solicitud sea POST y que existan los datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Verificación básica de campos requeridos
    if (empty($_POST["nombre"]) || empty($_POST["correo"]) || empty($_POST["contrasena"])) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Todos los campos son obligatorios.'
        ]);
        $conn->close();
        exit();
    }
    
    // Asignación y limpieza de variables
    $username = trim($_POST["nombre"]);
    $email = trim($_POST["correo"]);
    
    // ¡CORRECCIÓN DE VARIABLE! Usar 'contrasena' (sin ñ) para coincidir con el HTML
    // Encriptar la contraseña de forma segura
    $password_hashed = password_hash($_POST["contrasena"], PASSWORD_DEFAULT); 

    // 3. Preparar la consulta SQL para inserción
    // Asegúrate de que los nombres de las columnas (nombre, correo, contraseña) coincidan
    // exactamente con los de tu tabla en PhpMyAdmin.
    $sql = "INSERT INTO usuario (nombre, correo, contrasena) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        
        // Vincular parámetros ('sss' significa tres strings: nombre, correo, contraseña)
        $stmt->bind_param("sss", $username, $email, $password_hashed);

        // 4. Ejecutar la consulta y enviar la respuesta JSON
        if ($stmt->execute()) {
            // Éxito: devolver una respuesta JSON de éxito
            echo json_encode([
                'status' => 'success', 
                'message' => '✅ Registro exitoso. Ahora puedes iniciar sesión.'
            ]);
        } else {
            // Error en la ejecución de la consulta
            echo json_encode([
                'status' => 'error', 
                'message' => '❌ Error al registrar usuario: ' . $stmt->error
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
    
    // 5. Cerrar la conexión
    $conn->close();

} else {
    // Si se accede directamente al archivo sin POST
    echo json_encode([
        'status' => 'error', 
        'message' => 'Método de solicitud no permitido.'
    ]);
}
?>