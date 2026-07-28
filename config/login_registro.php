<?php
require_once "conexion_bd.php"; // Incluye la conexión a la base de datos

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm-password"] ?? '';
    $role_id = 2; // Por defecto, rol de usuario normal

    // Verificar que los campos no estén vacíos
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        echo '<script>alert("Todos los campos son obligatorios."); window.location = "../pages/registro.php";</script>';
        exit();
    } elseif ($password != $confirm_password) {
        echo '<script>alert("Las contraseñas no coinciden."); window.location = "../pages/registro.php";</script>';
        exit();
    } else {
        // Verificar si el correo ya está registrado usando sentencias preparadas
        $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $stmt_check->close();
            echo '<script>alert("Este correo ya está registrado."); window.location = "../pages/registro.php";</script>';
            exit();
        }
        $stmt_check->close();

        require_once "password_validation.php";
        if (!validarContrasenaFuerte($password)) {
            echo '<script>alert("La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."); window.location = "../pages/registro.php";</script>';
            exit();
        }

        // Encriptar la contraseña con hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario en la base de datos usando sentencias preparadas
        $stmt_insert = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("sssi", $username, $email, $hashed_password, $role_id);
        
        if ($stmt_insert->execute()) {
            $nuevo_id = $stmt_insert->insert_id;
            $stmt_insert->close();

            // Iniciar sesión automáticamente después del registro
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['id']     = $nuevo_id;
            $_SESSION['nombre'] = $username;
            $_SESSION['email']  = $email;
            $_SESSION['rol']    = $role_id;
            $_SESSION['foto']   = null;
            
            // Redirigir al usuario normal a su página
            header("location: ../pages/usuario/route_selected_screen.php");
            exit();
        } else {
            if (isset($stmt_insert)) $stmt_insert->close();
            echo '<script>alert("Error en el registro. Intente nuevamente."); window.location = "../pages/registro.php";</script>';
            exit();
        }
    }
}
?>
