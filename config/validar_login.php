<?php
include 'conexion_bd.php'; 

$email = $_POST['email'];
$password = $_POST['password'];

$query_verificacion = "SELECT * FROM usuarios WHERE email = ?";
$stmt = mysqli_prepare($conexion, $query_verificacion);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$resultado_verificacion = mysqli_stmt_get_result($stmt);



if(mysqli_num_rows($resultado_verificacion) > 0) {
    // El correo existe en la base de datos
        $usuario = mysqli_fetch_assoc($resultado_verificacion);

    // Verificar la contraseña
    if(password_verify($password, $usuario['password'])) {
        // Contraseña válida, redirigir al usuario a la página de inicio
            session_start();
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['foto'] = $usuario['foto'];
            echo '<script>alert("Bienvenido de nuevo."); window.location = "../index.php";</script>';
        } else {
        // Contraseña incorrecta
            echo '<script>alert("La contraseña es incorrecta."); window.location = "../pages/login.php";</script>';
        }
    } else {
    // El correo no está registrado
        echo '<script>alert("No se encontró ninguna cuenta asociada a este correo."); window.location = "../pages/login.php";</script>';
    }

?>