<?php
include("../config/conexion_bd.php");

if (isset($_POST['btningresar'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['error_message'] = "⚠️ Los campos no pueden estar vacíos";
    } else {
        $email = $_POST['email'];
        $password = trim($_POST['password']); // Limpiar espacios
        
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            
            // Verificar la contraseña (con compatibilidad para hash y sin hash)
            $password_valid = password_verify($password, $usuario['password']) || $password === $usuario['password'];
            
            if ($password_valid) {
                // Verificar que NO sea administrador
                if ($usuario['rol'] == 1) {
                    $_SESSION['error_message'] = "⚠️ Los administradores deben usar el acceso de administrador";
                } else {
                    // Iniciar sesión y guardar datos del usuario
                    session_start();
                    $_SESSION['id']     = $usuario['id'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['email']  = $usuario['email'];
                    $_SESSION['rol']    = $usuario['rol'];
                    
                    // Redirigir usuario normal
                    if ($usuario['rol'] == 2) { // Usuario normal
                        header("Location: ../pages/usuario/route_selected_screen.php");
                    }
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "⚠️ Usuario o contraseña incorrectos";
            }
        } else {
            $_SESSION['error_message'] = "⚠️ Usuario o contraseña incorrectos";
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Lógica para invitado
if (isset($_POST['btninvitado'])) {
    session_start();
    $_SESSION['id']     = 0;
    $_SESSION['nombre'] = 'Invitado';
    $_SESSION['email']  = '';
    $_SESSION['rol']    = 3; // Rol de invitado

    header("Location: ../pages/usuario/route_selected_screen.php");
    exit();
}

// Lógica para login de administrador o empresa
if (isset($_POST['btnadmin'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['admin_error_message'] = "⚠️ Los campos no pueden estar vacíos";
    } else {
        $email = $_POST['email'];
        $password = trim($_POST['password']);
        
        // Primero buscar en usuarios (superadmin)
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            $password_valid = password_verify($password, $usuario['password']) || $password === $usuario['password'];
            
            if ($password_valid && $usuario['rol'] == 1) {
                session_start();
                $_SESSION['id']     = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol']    = 1; // Superadmin
                $_SESSION['rfc_empresa'] = null; // Superadmin ve todo
                
                header("Location: ../pages/admin/dashboard.php");
                exit();
            }
        }
        
        // Si no fue superadmin, buscar en empresas
        $sql_emp = "SELECT * FROM empresas WHERE email = ?";
        $stmt_emp = $conexion->prepare($sql_emp);
        $stmt_emp->bind_param("s", $email);
        $stmt_emp->execute();
        $resultado_emp = $stmt_emp->get_result();
        
        if ($resultado_emp->num_rows > 0) {
            $empresa = $resultado_emp->fetch_assoc();
            $password_valid = password_verify($password, $empresa['password']) || $password === $empresa['password'];
            
            if ($password_valid) {
                session_start();
                $_SESSION['id']     = $empresa['rfc_empresa']; // Usamos rfc como ID de sesión
                $_SESSION['nombre'] = $empresa['nombre'];
                $_SESSION['rol']    = 4; // Empresa
                $_SESSION['rfc_empresa'] = $empresa['rfc_empresa'];
                
                header("Location: ../pages/admin/dashboard.php");
                exit();
            } else {
                $_SESSION['admin_error_message'] = "⚠️ Usuario o contraseña incorrectos";
            }
        } else {
            $_SESSION['admin_error_message'] = "⚠️ Usuario o contraseña incorrectos";
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>