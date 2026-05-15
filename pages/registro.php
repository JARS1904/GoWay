<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - GoWay</title>
    <link rel="icon" href="../assets/images/logo_new.png" type="image/png">
        <link rel="stylesheet" href="../assets/css/user_registration.css">
</head>
<body>

<nav id="mainNav">
    <a href="../index.php" class="nav-brand">
        <img src="../assets/images/logo_new.png" alt="GoWay Logo">
        <span>GoWay</span>
    </a>
    <div class="nav-links">
        <a href="../index.php">Inicio</a>
        <a href="login.php">Iniciar sesión</a>
        <a href="registro_empresa.php" class="btn-primary-nav">Registrar empresa</a>
    </div>
</nav>

<div class="rg-wrap">

    <!-- ── Left brand panel ── -->
    <div class="rg-panel">
        <div class="rg-panel-brand">
            <img src="../assets/images/logo_new.png" alt="GoWay logo">
            <span>GoWay</span>
        </div>
        <p class="rg-panel-tagline">Únete a miles de<br>viajeros en GoWay.</p>
        <img src="../assets/images/registro.png" alt="" class="rg-panel-img">
        <div class="rg-panel-steps">
            <div class="rg-panel-step">
                <span class="rg-panel-step-num">1</span>
                Crea tu cuenta gratis
            </div>
            <div class="rg-panel-step">
                <span class="rg-panel-step-num">2</span>
                Explora rutas y horarios
            </div>
            <div class="rg-panel-step">
                <span class="rg-panel-step-num">3</span>
                Viaja sin complicaciones
            </div>
        </div>
    </div>

    <!-- ── Right form panel ── -->
    <div class="rg-card">
        <h1 class="rg-title">Crea tu cuenta</h1>
        <p class="rg-sub">Regístrate para empezar a usar GoWay</p>

        <!-- Alert -->
        <div class="rg-alert" id="rgAlert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span id="rgAlertMsg"></span>
        </div>

    <form method="post" action="../config/login_registro.php">
        <div class="rg-field">
            <label for="username">Nombre de usuario</label>
            <div class="rg-input-wrap">
                <svg class="rg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <input type="text" id="username" name="username" placeholder="Tu nombre de usuario">
            </div>
        </div>

        <div class="rg-field">
            <label for="email">Correo electrónico</label>
            <div class="rg-input-wrap">
                <svg class="rg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" autocomplete="email">
            </div>
        </div>

        <div class="rg-field">
            <label for="password">Contraseña</label>
            <div class="rg-input-wrap">
                <svg class="rg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input type="password" id="password" name="password" placeholder="••••••••">
                <button type="button" class="rg-toggle" id="togglePw1" aria-label="Mostrar contraseña">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>

        <div class="rg-field">
            <label for="confirm-password">Confirmar contraseña</label>
            <div class="rg-input-wrap">
                <svg class="rg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="••••••••">
                <button type="button" class="rg-toggle" id="togglePw2" aria-label="Mostrar contraseña">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>

        <button type="submit" class="rg-btn">Crear cuenta</button>
    </form>

        <p class="rg-footer">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>

        <hr class="rg-divider">
        <button class="rg-admin-btn" onclick="openAdminModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7z"/></svg>
            Acceso Administrador
        </button>
    </div><!-- /.rg-card -->

</div><!-- /.rg-wrap -->

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

        <form id="adminLoginForm" method="post" action="login.php">
            <div class="adm-field">
                <label for="admin_email">Correo electrónico</label>
                <div class="adm-input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                    <input type="email" id="admin_email" name="email" placeholder="correo@ejemplo.com" required autocomplete="email">
                </div>
            </div>
            <div class="adm-field">
                <label for="admin_password">Contraseña</label>
                <div class="adm-input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    function openAdminModal() { document.getElementById('adminModal').classList.add('active'); }
    function closeAdminModal() { document.getElementById('adminModal').classList.remove('active'); }

    document.getElementById('closeAdminModal').addEventListener('click', closeAdminModal);
    document.getElementById('adminModal').addEventListener('click', function(e) { if (e.target===this) closeAdminModal(); });
    document.addEventListener('keydown', function(e) { if (e.key==='Escape') closeAdminModal(); });

    document.getElementById('toggleAdminPw').addEventListener('click', function() {
        const i = document.getElementById('admin_password');
        i.type = i.type==='password' ? 'text' : 'password';
    });
    document.getElementById('togglePw1').addEventListener('click', function() {
        const i = document.getElementById('password');
        i.type = i.type==='password' ? 'text' : 'password';
    });
    document.getElementById('togglePw2').addEventListener('click', function() {
        const i = document.getElementById('confirm-password');
        i.type = i.type==='password' ? 'text' : 'password';
    });

    // Show alert helper
    function showAlert(msg, type) {
        const alertBox = document.getElementById('rgAlert');
        const msgEl = document.getElementById('rgAlertMsg');
        alertBox.className = 'rg-alert ' + type + ' visible';
        msgEl.textContent = msg;
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    const userRegForm = document.querySelector('form[action="../config/login_registro.php"]');
    if (userRegForm) {
        userRegForm.addEventListener('submit', function(e) {
            const pass = document.getElementById('password').value;
            const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            if (!strongPasswordRegex.test(pass)) {
                e.preventDefault();
                showAlert('La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo especial.', 'error');
            }
        });
    }
</script>
</body>
</html>