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
                <button type="submit" class="btn">Registrarse</button>
            </form>
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
        </div>
        <div class="auth-image">
            <img src="../assets/images/registro.png" alt="Imagen de registro">
        </div>
    </div>

    <!-- Modal para Administrador -->
    <div class="modal-overlay" id="adminModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Acceso Administrador</h3>
                <button class="modal-close" id="closeAdminModal">&times;</button>
            </div>
            <form id="adminLoginForm" method="post" action="../pages/login.php">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label for="admin_email">Correo electrónico:</label>
                        <input type="email" id="admin_email" placeholder="correo@example.com" name="email" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="admin_password">Contraseña:</label>
                        <input type="password" id="admin_password" placeholder="Ingresa tu contraseña" name="password" required>
                    </div>
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