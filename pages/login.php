<?php
session_start();
include("../config/conexion_bd.php");
include("../controllers/controlador.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - GoWay</title>
    <link rel="icon" href="../assets/images/logo_new.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/login.css?v=<?php echo time(); ?>">
</head>
<body>

<nav id="mainNav">
    <a href="../index.php" class="nav-brand">
        <img src="../assets/images/logo_new.png" alt="GoWay Logo">
        <span>GoWay</span>
    </a>
    <div class="nav-links">
        <a href="../index.php">Inicio</a>
        <a href="registro.php">Registrarse</a>
        <a href="registro_empresa.php" class="btn-primary-nav">Registrar empresa</a>
    </div>
</nav>

<div class="lg-wrap">

    <!-- ── Left brand panel ── -->
    <div class="lg-panel">
        <div class="lg-panel-brand">
            <img src="../assets/images/logo_new.png" alt="GoWay logo">
            <span>GoWay</span>
        </div>
        <p class="lg-panel-tagline">Tu destino,<br>a un solo toque.</p>
        <img src="../assets/images/login.png" alt="" class="lg-panel-img">
        <p class="lg-panel-note">Conectando ciudades, personas y destinos.</p>
    </div>

    <!-- ── Right form panel ── -->
    <div class="lg-card">
        <h1 class="lg-title">Bienvenido de nuevo</h1>
        <p class="lg-sub">Inicia sesión para continuar</p>

        <form method="post" action="" id="loginForm">
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="lg-error"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div class="lg-field">
                <label for="email">Correo electrónico</label>
                <div class="lg-input-wrap">
                    <svg class="lg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" autocomplete="email">
                </div>
            </div>

            <div class="lg-field">
                <label for="password">Contraseña</label>
                <div class="lg-input-wrap">
                    <svg class="lg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password">
                    <button type="button" class="lg-toggle" id="togglePw" aria-label="Mostrar contraseña">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="lg-row"><a href="#">¿Olvidaste tu contraseña?</a></div>

            <button name="btningresar" type="submit" class="lg-btn" style="margin-bottom: 12px;">Iniciar sesión</button>
            <button name="btninvitado" type="submit" class="lg-btn" style="background: transparent; color: #2962FF; border: 1.5px solid #2962FF; box-shadow: none;">Continuar como invitado</button>
        </form>

        <p class="lg-footer">¿No tienes cuenta? <a href="registro.php">Regístrate</a> &nbsp;·&nbsp; <a href="registro_empresa.php">Registrar empresa</a></p>

        <hr class="lg-divider">
        <button class="lg-admin-btn" onclick="openAdminModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7z"/></svg>
            Acceso Administrador
        </button>
    </div><!-- /.lg-card -->

</div><!-- /.lg-wrap -->

    <!-- Modal Administrador -->
    <div class="adm-overlay" id="adminModal">
        <div class="adm-card">
            <button class="adm-close" id="closeAdminModal" aria-label="Cerrar">&times;</button>

            <div class="adm-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7z"/>
                    <circle cx="12" cy="11" r="2.5"/>
                    <path d="M7.5 18.5c.83-2.1 2.62-3.5 4.5-3.5s3.67 1.4 4.5 3.5"/>
                </svg>
            </div>

            <h2 class="adm-title">Panel de Administrador</h2>
            <p class="adm-subtitle">Ingresa tus credenciales para continuar</p>

            <form id="adminLoginForm" method="post" action="">
                <?php
                if (isset($_SESSION['admin_error_message'])) {
                    echo '<div class="adm-error">'.$_SESSION['admin_error_message'].'</div>';
                    unset($_SESSION['admin_error_message']);
                }
                ?>

                <div class="adm-field">
                    <label for="admin_email">Correo electrónico</label>
                    <div class="adm-input-wrap">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        <input type="email" id="admin_email" name="email" placeholder="correo@ejemplo.com" required autocomplete="email">
                    </div>
                </div>

                <div class="adm-field">
                    <label for="admin_password">Contraseña</label>
                    <div class="adm-input-wrap">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input type="password" id="admin_password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="adm-toggle-pw" id="toggleAdminPw" aria-label="Mostrar contraseña">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="btnadmin" class="adm-submit">Ingresar</button>
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

        document.getElementById('adminModal').addEventListener('click', function(e) {
            if (e.target === this) closeAdminModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAdminModal();
        });

        document.getElementById('toggleAdminPw').addEventListener('click', function() {
            const input = document.getElementById('admin_password');
            input.type = input.type === 'password' ? 'text' : 'password';
        });
        document.getElementById('togglePw').addEventListener('click', function() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    </script>
</body>
</html>