<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/logo.png" type="image/png">
</head>
<body>
    <!-- Header -->
    <header class="auth-header">
        <div class="header-right">
            <img src="../assets/images/logo.png" alt="Logo GoWay" class="header-logo">
            <h3>GoWay</h3>
        </div>
        <div class="header-left">
            <a href="#" onclick="openAdminModal(); return false;" class="admin-link">Administrador</a>
        </div>
    </header>

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

    <!-- Modal para Administrador -->
    <div class="modal-overlay" id="adminModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Acceso Administrador</h3>
                <button class="modal-close" id="closeAdminModal">&times;</button>
            </div>
            <form id="adminLoginForm" method="post" action="">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label for="admin_email">Correo electrónico:</label>
                        <input type="email" id="admin_email" placeholder="correo@example.com" name="email" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="admin_password">Contraseña:</label>
                        <input type="password" id="admin_password" placeholder="Ingresa tu contraseña" name="password" required>
                    </div>
                    <?php
                    // Mostrar mensaje de error si existe para el admin
                    if (isset($_SESSION['admin_error_message'])) {
                        echo '<div class="error-message">'.$_SESSION['admin_error_message'].'</div>';
                        unset($_SESSION['admin_error_message']);
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelAdminModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save" name="btnadmin">Ingresar como Admin</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAdminModal() {
            document.getElementById('adminModal').classList.add('active');
        }

        function closeAdminModal() {
            document.getElementById('adminModal').classList.remove('active');
        }

        document.getElementById('closeAdminModal').addEventListener('click', closeAdminModal);
        document.getElementById('cancelAdminModal').addEventListener('click', closeAdminModal);

        // Cerrar modal al hacer clic fuera
        document.getElementById('adminModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAdminModal();
            }
        });
    </script>
</body>
</html>