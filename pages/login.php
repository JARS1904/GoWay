<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>Iniciar Sesión</h2>
            
            <form method="post" action="" id="loginForm">
                <?php
                session_start();
                include("../config/conexion_bd.php");
                include("../controllers/controlador.php");
                
                // Mostrar mensaje de error si existe
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="error-message">'.$_SESSION['error_message'].'</div>';
                    unset($_SESSION['error_message']); // Limpiar el mensaje después de mostrarlo
                }
                ?>
                <div class="form-group">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" placeholder="correo@example.com" name="email">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" placeholder="Ingresa tu contraseña" name="password">
                </div>
                <div class="form-group">
                    <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                </div>
                <button name="btningresar" type="submit" class="btn">Iniciar Sesión</button>
            </form>
            <p>¿No tienes una cuenta? <a href="registro.php">Regístrate</a></p>
        </div>
        <div class="auth-image">
            <img src="../assets/images/login.png" alt="Imagen de inicio de sesión">
        </div>
    </div>
</body>
</html>