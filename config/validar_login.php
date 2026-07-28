<?php
require_once 'conexion_bd.php'; 

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo '<script>alert("Por favor ingrese correo y contraseña."); window.location = "../pages/login.php";</script>';
    exit();
}

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
        // Contraseña válida, iniciar sesión de forma segura y prevenir fijación de sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);

        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['foto'] = $usuario['foto'] ?? null;
        $_SESSION['rol'] = $usuario['rol'] ?? 2;
        if (!empty($usuario['rfc_empresa'])) {
            $_SESSION['rfc_empresa'] = $usuario['rfc_empresa'];
        }
        if (!empty($usuario['email'])) {
            $_SESSION['email'] = $usuario['email'];
        }

        mysqli_stmt_close($stmt);
        echo '<script>alert("Bienvenido de nuevo."); window.location = "../index.php";</script>';
        exit();
    } else {
        mysqli_stmt_close($stmt);
        echo '<script>alert("La contraseña es incorrecta."); window.location = "../pages/login.php";</script>';
        exit();
    }
} else {
    if ($stmt) mysqli_stmt_close($stmt);
    echo '<script>alert("No se encontró ninguna cuenta asociada a este correo."); window.location = "../pages/login.php";</script>';
    exit();
}
?>