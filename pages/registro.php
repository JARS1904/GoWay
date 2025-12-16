<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/logo.png" type="image/png">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>Registro</h2>
            <form method="post" action="../config/login_registro.php">
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" placeholder="Ingresa tu usuario" name="username">
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" placeholder="you@example.com" name="email">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" placeholder="Ingresa tu contraseña" name="password">
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirmar contraseña:</label>
                    <input type="password" id="confirm-password" placeholder="Confirma tu contraseña" name="confirm-password">
                </div>
                <div class="form-group">
                    <label for="role_id">Rol:</label>
                    <select name="role_id" id="role_id" class="role-select" required>
                        <option value="">Selecciona un rol</option>
                        <option value="1">Administrador</option>
                        <option value="2">Usuario</option>
                        <option value="3">Checador</option>
                    </select>
                </div>
                <button type="submit" class="btn">Registrarse</button>
            </form>
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
        </div>
        <div class="auth-image">
            <img src="../assets/images/registro.png" alt="Imagen de registro">
        </div>
    </div>
</body>
</html>