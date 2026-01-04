<?php
require_once "conexion_bd.php"; // Incluye la conexión a la base de datos

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm-password"];
    $role_id = 2; // Por defecto, rol de usuario normal

    // Verificar que los campos no estén vacíos
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        //echo "Todos los campos son obligatorios.";
        echo '<script>alert("Todos los campos son obligatorios."); window.location = "../pages/registro.php";</script>';
    } elseif ($password != $confirm_password) {
        echo "Las contraseñas no coinciden.";
    } else {
        // Verificar si el correo ya está registrado
        $query = "SELECT id FROM usuarios WHERE email = '$email'";
        $result = $conexion->query($query);

        if ($result->num_rows > 0) {
            echo "Este correo ya está registrado.";
        } else {
            // Encriptar la contraseña
            //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos
            $query = "INSERT INTO usuarios (nombre, email, password, rol) VALUES ('$username', '$email', '$password', '$role_id')";
            
            if ($conexion->query($query)) {
                // Iniciar sesión automáticamente después del registro
                session_start();
                $_SESSION['id']     = $conexion->insert_id;
                $_SESSION['nombre'] = $username;
                $_SESSION['rol']    = $role_id;
                
                echo "Registro exitoso. Bienvenido.";
                // Redirigir al usuario normal a su página
                header("location: ../pages/route_selected_screen.php");
            } else {
                echo "Error en el registro.";
            }
        }
    }
}
?>
