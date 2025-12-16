<?php
include("../config/conexion_bd.php");

if (isset($_POST['btningresar'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['error_message'] = "⚠️ Los campos no pueden estar vacíos";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $sql = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            
            // Iniciar sesión y guardar datos del usuario

            // Se comento por problemas al iniciar sesión
            /*
            session_start();
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nombre'];
            $_SESSION['user_role'] = $usuario['rol'];
            */

            // Se cambio por esta versión
            session_start();
            $_SESSION['id']     = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol']    = $usuario['rol'];
            
            // Redirigir según el rol
            if ($usuario['rol'] == 1) { // Administrador
                header("Location: ../index.php");
            } elseif ($usuario['rol'] == 2) { // Usuario normal
                header("Location: ../pages/route_selected_screen.php"); // Redirige directamente
            }
            exit();
        } else {
            $_SESSION['error_message'] = "⚠️ Usuario o contraseña incorrectos";
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>