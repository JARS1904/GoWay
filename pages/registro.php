<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - GoWay</title>
    <link rel="icon" href="../assets/images/logo_new.png" type="image/png">
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

<style>
/* ── Registro Page ─────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #c7d8ff 0%, #dce9ff 50%, #eef2ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 88px 16px 24px;
}

/* ── Navbar ─────────────────────────────────────────── */
nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 6%; height: 64px;
    background: rgba(251, 251, 253, 0.8); 
    backdrop-filter: saturate(180%) blur(20px);
    -webkit-backdrop-filter: saturate(180%) blur(20px);
    transition: background 0.3s, box-shadow 0.3s;
    font-family: 'Inter', system-ui, sans-serif;
}
.nav-brand {
    display: flex; align-items: center; gap: 8px;
    text-decoration: none; color: #1d1d1f;
    font-weight: 700; font-size: 1.25rem;
    letter-spacing: -0.02em;
}
.nav-brand img { width: 32px; height: 32px; object-fit: contain; }

.nav-links { display: flex; align-items: center; gap: 24px; }
.nav-links a {
    text-decoration: none; color: #86868b;
    font-size: 0.9rem; font-weight: 500;
    transition: color 0.2s;
}
.nav-links a:hover { color: #1d1d1f; }

.btn-primary-nav {
    background: #2962FF; color: #fff !important;
    padding: 8px 16px; border-radius: 99px; font-weight: 600;
    transition: opacity 0.2s !important;
}
.btn-primary-nav:hover { opacity: 0.9; color: #fff !important; }

@media (max-width: 768px) {
    .nav-links a:not(.btn-primary-nav) { display: none; }
}

/* ── Split wrapper ───────────────────────────────── */
.rg-wrap {
    display: flex;
    width: 100%;
    max-width: 940px;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 24px 72px rgba(41,98,255,.18), 0 6px 24px rgba(0,0,0,.09);
    animation: rg-in .3s ease;
}
@keyframes rg-in {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Left brand panel ───────────────────────────── */
.rg-panel {
    flex: 0 0 50%;
    background: linear-gradient(160deg, #1e4fff 0%, #0d3acc 55%, #0a2fa8 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 44px 40px;
    position: relative;
    overflow: hidden;
}
.rg-panel::before,
.rg-panel::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    opacity: .12;
    background: #fff;
}
.rg-panel::before { width: 280px; height: 280px; top: -80px; right: -80px; }
.rg-panel::after  { width: 200px; height: 200px; bottom: -60px; left: -60px; }

.rg-panel-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
}
.rg-panel-brand img {
    width: 52px; height: 52px;
    object-fit: contain;
}
.rg-panel-brand span {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.5px;
}
.rg-panel-tagline {
    font-size: 1.05rem;
    font-weight: 500;
    color: rgba(255,255,255,.82);
    text-align: center;
    line-height: 1.55;
    margin-bottom: 28px;
}
.rg-panel-img {
    width: 100%;
    max-width: 240px;
    object-fit: contain;
    filter: drop-shadow(0 8px 24px rgba(0,0,0,.25));
    margin-bottom: 24px;
}
.rg-panel-steps {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
    max-width: 220px;
}
.rg-panel-step {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: .82rem;
    color: rgba(255,255,255,.78);
}
.rg-panel-step-num {
    flex-shrink: 0;
    width: 22px; height: 22px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem;
    font-weight: 700;
    color: #fff;
}

/* ── Right form panel ───────────────────────────── */
.rg-card {
    flex: 1;
    background: #fff;
    padding: 44px 44px 36px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.rg-title {
    font-size: 1.45rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 6px;
}
.rg-sub {
    font-size: .85rem;
    color: #6B7280;
    margin-bottom: 26px;
}

.rg-field { margin-bottom: 15px; }
.rg-field label {
    display: block;
    font-size: .82rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 7px;
    letter-spacing: .02em;
}
.rg-input-wrap { position: relative; display: block; }
.rg-input-wrap .rg-ico {
    position: absolute;
    left: 13px;
    top: 50%; transform: translateY(-50%);
    width: 16px; height: 16px;
    stroke: #9CA3AF;
    pointer-events: none;
}
.rg-input-wrap input {
    width: 100%;
    padding: 11px 40px 11px 38px;
    border: 1.5px solid #E5E7EB;
    border-radius: 10px;
    font-size: .93rem;
    color: #111827;
    background: #F9FAFB;
    transition: border-color .18s, box-shadow .18s;
    outline: none;
}
.rg-input-wrap input:focus {
    border-color: #2962FF;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(41,98,255,.11);
}
/* ocultar el ojo nativo del navegador */
.rg-input-wrap input[type="password"]::-ms-reveal,
.rg-input-wrap input[type="password"]::-ms-clear { display: none; }
.rg-toggle {
    position: absolute; right: 11px;
    top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    padding: 4px; display: flex; align-items: center; color: #9CA3AF;
}
.rg-toggle svg { width: 16px; height: 16px; stroke: currentColor; }
.rg-toggle:hover { color: #6B7280; }

.rg-btn {
    width: 100%;
    padding: 13px;
    margin-top: 6px;
    background: linear-gradient(135deg, #2962FF, #1a50e8);
    color: #fff;
    font-size: .97rem;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    letter-spacing: .03em;
    box-shadow: 0 4px 14px rgba(41,98,255,.32);
    transition: opacity .18s, transform .12s;
}
.rg-btn:hover  { opacity: .91; transform: translateY(-1px); }
.rg-btn:active { transform: translateY(0); }

/* ── Error/Success alert ─────────────────────────── */
.rg-alert {
    padding: 12px 16px;
    border-radius: 10px;
    font-size: .85rem;
    font-weight: 500;
    margin-bottom: 16px;
    display: none;
}
.rg-alert.error   { background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; }
.rg-alert.success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; }
.rg-alert.visible { display: flex; align-items: center; gap: 8px; }
.rg-alert svg { width: 18px; height: 18px; flex-shrink: 0; }

.rg-footer {
    text-align: center;
    margin-top: 20px;
    font-size: .83rem;
    color: #6B7280;
}
.rg-footer a { color: #2962FF; font-weight: 600; text-decoration: none; }
.rg-footer a:hover { text-decoration: underline; }

.rg-divider {
    border: none;
    border-top: 1px solid #F3F4F6;
    margin: 20px 0 16px;
}
.rg-admin-btn {
    width: 100%;
    padding: 11px;
    background: transparent;
    color: #6B7280;
    font-size: .85rem;
    font-weight: 500;
    border: 1.5px solid #E5E7EB;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color .18s, color .18s;
    display: flex; align-items: center; justify-content: center; gap: 7px;
}
.rg-admin-btn svg { width: 15px; height: 15px; stroke: currentColor; }
.rg-admin-btn:hover { border-color: #2962FF; color: #2962FF; }

/* ── Responsive ─────────────────────────────────── */
@media (max-width: 700px) {
    .rg-wrap { flex-direction: column; border-radius: 18px; }
    .rg-panel { flex: none; padding: 28px 24px 24px; }
    .rg-panel-img { max-width: 160px; margin-bottom: 0; }
    .rg-panel-steps { display: none; }
    .rg-card { padding: 32px 24px 28px; }
}
</style>

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

<style>
/* ── Admin Modal (shared) ────────────────────────── */
.adm-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(10,15,30,.65); backdrop-filter: blur(4px);
    z-index: 9999; align-items: center; justify-content: center;
}
.adm-overlay.active { display: flex; }
.adm-card {
    position: relative; background: #fff; width: 100%; max-width: 350px;
    border-radius: 20px; padding: 44px 40px 36px;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
    animation: adm-in .25s ease;
}
@keyframes adm-in {
    from { opacity:0; transform: translateY(-22px) scale(.97); }
    to   { opacity:1; transform: translateY(0) scale(1); }
}
.adm-close {
    position: absolute; top: 16px; right: 18px;
    background: #f1f3f7; border: none; width: 32px; height: 32px;
    border-radius: 50%; font-size: 18px; cursor: pointer; color: #555;
    display: flex; align-items: center; justify-content: center; transition: background .18s;
}
.adm-close:hover { background: #e2e6ef; color: #111; }
.adm-icon-wrap {
    width: 64px; height: 64px;
    background: linear-gradient(135deg,#2962FF22,#2962FF11);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px;
}
.adm-icon-wrap svg { width: 30px; height: 30px; stroke: #2962FF; }
.adm-title { text-align:center; font-size:1.35rem; font-weight:700; color:#111827; margin:0 0 6px; }
.adm-subtitle { text-align:center; font-size:.85rem; color:#6B7280; margin:0 0 28px; }
.adm-field { margin-bottom: 18px; }
.adm-field label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:7px; }
.adm-input-wrap { position:relative; display:block; }
.adm-input-wrap > svg:first-child { position:absolute; left:13px; top:50%; transform:translateY(-50%); width:16px; height:16px; stroke:#9CA3AF; pointer-events:none; }
.adm-input-wrap input {
    width:100%; padding:11px 40px 11px 38px;
    border:1.5px solid #E5E7EB; border-radius:10px;
    font-size:.93rem; color:#111827; background:#F9FAFB;
    transition:border-color .18s,box-shadow .18s; outline:none;
}
.adm-input-wrap input:focus { border-color:#2962FF; background:#fff; box-shadow:0 0 0 3px rgba(41,98,255,.12); }
/* ocultar el ojo nativo del navegador */
.adm-input-wrap input[type="password"]::-ms-reveal,
.adm-input-wrap input[type="password"]::-ms-clear { display: none; }
.adm-toggle-pw { position:absolute; right:11px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; padding:4px; display:flex; align-items:center; color:#9CA3AF; }
.adm-toggle-pw svg { width:16px; height:16px; stroke:currentColor; }
.adm-toggle-pw:hover { color:#6B7280; }
.adm-submit {
    width:100%; padding:13px; margin-top:8px;
    background:linear-gradient(135deg,#2962FF,#1a50e8); color:#fff;
    font-size:.97rem; font-weight:600; border:none; border-radius:10px;
    cursor:pointer; letter-spacing:.03em;
    box-shadow:0 4px 14px rgba(41,98,255,.35); transition:opacity .18s,transform .12s;
}
.adm-submit:hover { opacity:.92; transform:translateY(-1px); }
@media (max-width:480px) { .adm-card { padding:36px 22px 28px; } }
</style>

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