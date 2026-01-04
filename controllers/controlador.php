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
            
            // Verificar que NO sea administrador
            if ($usuario['rol'] == 1) {
                $_SESSION['error_message'] = "⚠️ Los administradores deben usar el acceso de administrador";
            } else {
                // Iniciar sesión y guardar datos del usuario
                session_start();
                $_SESSION['id']     = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol']    = $usuario['rol'];
                
                // Redirigir usuario normal
                if ($usuario['rol'] == 2) { // Usuario normal
                    header("Location: ../pages/route_selected_screen.php");
                }
                exit();
            }
        } else {
            $_SESSION['error_message'] = "⚠️ Usuario o contraseña incorrectos";
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Lógica para login de administrador
if (isset($_POST['btnadmin'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['admin_error_message'] = "⚠️ Los campos no pueden estar vacíos";
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
            
            // Verificar que sea administrador
            if ($usuario['rol'] == 1) {
                // Iniciar sesión y guardar datos del admin
                session_start();
                $_SESSION['id']     = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol']    = $usuario['rol'];
                
                // Redirigir al dashboard de admin
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['admin_error_message'] = "⚠️ Solo los administradores pueden acceder aquí";
            }
        } else {
            $_SESSION['admin_error_message'] = "⚠️ Usuario o contraseña incorrectos";
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>