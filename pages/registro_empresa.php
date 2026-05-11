<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empresa - GoWay</title>
    <meta name="description" content="Registra tu empresa en GoWay y empieza a gestionar tu flota de transporte público de manera inteligente.">
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
        <a href="registro.php" class="btn-primary-nav">Registro de Usuario</a>
    </div>
</nav>

<style>
/* ── Reset ─────────────────────────────────────────── */
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

/* ── Wrapper ─────────────────────────────────────────── */
.rg-wrap {
    display: flex;
    width: 100%;
    max-width: 980px;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 24px 72px rgba(41,98,255,.18), 0 6px 24px rgba(0,0,0,.09);
    animation: rg-in .3s ease;
}
@keyframes rg-in {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Left brand panel ──────────────────────────────── */
.rg-panel {
    flex: 0 0 42%;
    background: linear-gradient(160deg, #1e4fff 0%, #0d3acc 55%, #0a2fa8 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 40px 40px;
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
    position: relative;
    z-index: 1;
}
.rg-panel-brand img { width: 52px; height: 52px; object-fit: contain; }
.rg-panel-brand span {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.5px;
}
.rg-panel-tagline {
    font-size: 1rem;
    font-weight: 500;
    color: rgba(255,255,255,.82);
    text-align: center;
    line-height: 1.6;
    margin-bottom: 28px;
    position: relative;
    z-index: 1;
}
.rg-panel-icon-wrap {
    width: 100px; height: 100px;
    background: rgba(255,255,255,.12);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 28px;
    position: relative;
    z-index: 1;
}
.rg-panel-icon-wrap svg { width: 52px; height: 52px; stroke: rgba(255,255,255,.9); }

.rg-panel-features {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
    max-width: 230px;
    position: relative;
    z-index: 1;
}
.rg-panel-feature {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: .83rem;
    color: rgba(255,255,255,.80);
}
.rg-panel-feature-icon {
    flex-shrink: 0;
    width: 26px; height: 26px;
    border-radius: 50%;
    background: rgba(255,255,255,.18);
    display: flex; align-items: center; justify-content: center;
}
.rg-panel-feature-icon svg { width: 13px; height: 13px; stroke: #fff; }

/* ── Right form panel ──────────────────────────────── */
.rg-card {
    flex: 1;
    background: #fff;
    padding: 40px 44px 36px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow-y: auto;
    max-height: 100vh;
}

.rg-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 4px;
}
.rg-sub {
    font-size: .85rem;
    color: #6B7280;
    margin-bottom: 22px;
}

/* ── Grid 2 cols ──────────────────────────────────── */
.rg-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 18px;
}
.rg-grid .rg-field-full { grid-column: 1 / -1; }

.rg-field { margin-bottom: 14px; }
.rg-field label {
    display: block;
    font-size: .81rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
    letter-spacing: .02em;
}
.rg-input-wrap { position: relative; display: block; }
.rg-input-wrap .rg-ico {
    position: absolute;
    left: 13px;
    top: 50%; transform: translateY(-50%);
    width: 15px; height: 15px;
    stroke: #9CA3AF;
    pointer-events: none;
}
.rg-input-wrap input {
    width: 100%;
    padding: 10px 40px 10px 37px;
    border: 1.5px solid #E5E7EB;
    border-radius: 10px;
    font-size: .91rem;
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
.rg-input-wrap input[type="password"]::-ms-reveal,
.rg-input-wrap input[type="password"]::-ms-clear { display: none; }
.rg-toggle {
    position: absolute; right: 11px;
    top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    padding: 4px; display: flex; align-items: center; color: #9CA3AF;
}
.rg-toggle svg { width: 15px; height: 15px; stroke: currentColor; }
.rg-toggle:hover { color: #6B7280; }

/* ── Divider ──────────────────────────────────────── */
.rg-section-label {
    font-size: .76rem;
    font-weight: 700;
    color: #9CA3AF;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 10px 0 14px;
    padding-bottom: 6px;
    border-bottom: 1px solid #F3F4F6;
    grid-column: 1 / -1;
}

/* ── Button ──────────────────────────────────────── */
.rg-btn {
    width: 100%;
    padding: 13px;
    margin-top: 8px;
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
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.rg-btn:hover  { opacity: .91; transform: translateY(-1px); }
.rg-btn:active { transform: translateY(0); }
.rg-btn svg { width: 18px; height: 18px; stroke: #fff; }

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

/* ── Footer ──────────────────────────────────────── */
.rg-footer {
    text-align: center;
    margin-top: 16px;
    font-size: .83rem;
    color: #6B7280;
}
.rg-footer a { color: #2962FF; font-weight: 600; text-decoration: none; }
.rg-footer a:hover { text-decoration: underline; }

/* ── Responsive ──────────────────────────────────── */
@media (max-width: 720px) {
    .rg-wrap { flex-direction: column; border-radius: 18px; max-width: 480px; }
    .rg-panel { flex: none; padding: 28px 24px 22px; }
    .rg-panel-features { display: none; }
    .rg-card { padding: 28px 22px 24px; max-height: none; }
    .rg-grid { grid-template-columns: 1fr; }
    .rg-grid .rg-field-full { grid-column: 1; }
}
</style>

<div class="rg-wrap">

    <!-- Left panel -->
    <div class="rg-panel">
        <div class="rg-panel-brand">
            <img src="../assets/images/logo_new.png" alt="GoWay logo">
            <span>GoWay</span>
        </div>
        <p class="rg-panel-tagline">Registra tu empresa y<br>gestiona tu flota con GoWay.</p>

        <div class="rg-panel-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2"/>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                <line x1="12" y1="12" x2="12" y2="16"/>
                <line x1="10" y1="14" x2="14" y2="14"/>
            </svg>
        </div>

        <div class="rg-panel-features">
            <div class="rg-panel-feature">
                <div class="rg-panel-feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                Panel de gestión exclusivo
            </div>
            <div class="rg-panel-feature">
                <div class="rg-panel-feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                Gestiona vehículos y conductores
            </div>
            <div class="rg-panel-feature">
                <div class="rg-panel-feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                Reportes e incidencias en tiempo real
            </div>
            <div class="rg-panel-feature">
                <div class="rg-panel-feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                Tus datos completamente aislados
            </div>
        </div>
    </div>

    <!-- Right form -->
    <div class="rg-card">
        <h1 class="rg-title">Registra tu empresa</h1>
        <p class="rg-sub">Completa los datos para crear tu cuenta empresarial en GoWay</p>

        <!-- Alert -->
        <div class="rg-alert" id="rgAlert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span id="rgAlertMsg"></span>
        </div>

        <form id="empresaRegForm" novalidate>
            <div class="rg-grid">

                <span class="rg-section-label">Datos de la Empresa</span>

                <!-- RFC -->
                <div class="rg-field">
                    <label for="rfc_empresa">RFC de la Empresa *</label>
                    <div class="rg-input-wrap">
                        <svg class="rg-ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <input type="text" id="rfc_empresa" name="rfc_empresa" placeholder="XAXX010101000" maxlength="13" required style="text-transform:uppercase">
                    </div>
                </div>

                <!-- Nombre -->
                <div class="rg-field">
                    <label for="nombre_empresa">Nombre de la Empresa *</label>
                    <div class="rg-input-wrap">
                        <svg class="rg-ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        <input type="text" id="nombre_empresa" name="nombre_empresa" placeholder="Transportes S.A. de C.V." required>
                    </div>
                </div>

                <!-- Dirección (full width) -->
                <div class="rg-field rg-field-full">
                    <label for="direccion_empresa">Dirección</label>
                    <div class="rg-input-wrap">
                        <svg class="rg-ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <input type="text" id="direccion_empresa" name="direccion_empresa" placeholder="Calle, Número, Ciudad">
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="rg-field">
                    <label for="tel_empresa">Teléfono</label>
                    <div class="rg-input-wrap">
                        <svg class="rg-ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.15 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.06 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 16.92z"/></svg>
                        <input type="tel" id="tel_empresa" name="tel_empresa" placeholder="9931234567">
                    </div>
                </div>

                <!-- Email -->
                <div class="rg-field">
                    <label for="email_empresa">Correo Electrónico *</label>
                    <div class="rg-input-wrap">
                        <svg class="rg-ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <input type="email" id="email_empresa" name="email_empresa" placeholder="empresa@correo.com" required autocomplete="email">
                    </div>
                </div>

                <span class="rg-section-label">Credenciales de Acceso</span>

                <!-- Password -->
                <div class="rg-field">
                    <label for="password">Contraseña *</label>
                    <div class="rg-input-wrap">
                        <svg class="rg-ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="new-password">
                        <button type="button" class="rg-toggle" id="togglePw1" aria-label="Mostrar contraseña">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="rg-field">
                    <label for="confirm_password">Confirmar Contraseña *</label>
                    <div class="rg-input-wrap">
                        <svg class="rg-ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required autocomplete="new-password">
                        <button type="button" class="rg-toggle" id="togglePw2" aria-label="Mostrar contraseña">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

            </div><!-- /.rg-grid -->

            <button type="submit" class="rg-btn" id="submitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><polyline points="9 14 11 16 15 12"/></svg>
                Crear cuenta empresarial
            </button>
        </form>

        <p class="rg-footer">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a> &nbsp;·&nbsp;
            ¿Eres usuario? <a href="registro.php">Registro de usuario</a>
        </p>
    </div>

</div><!-- /.rg-wrap -->

<script>
// Toggle password visibility
document.getElementById('togglePw1').addEventListener('click', function() {
    const i = document.getElementById('password');
    i.type = i.type === 'password' ? 'text' : 'password';
});
document.getElementById('togglePw2').addEventListener('click', function() {
    const i = document.getElementById('confirm_password');
    i.type = i.type === 'password' ? 'text' : 'password';
});

// RFC to uppercase
document.getElementById('rfc_empresa').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Show alert helper
function showAlert(msg, type) {
    const alert = document.getElementById('rgAlert');
    const msgEl = document.getElementById('rgAlertMsg');
    alert.className = 'rg-alert ' + type + ' visible';
    msgEl.textContent = msg;
    alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Form submit
document.getElementById('empresaRegForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const pass = document.getElementById('password').value;
    const conf = document.getElementById('confirm_password').value;

    if (pass !== conf) {
        showAlert('Las contraseñas no coinciden.', 'error');
        return;
    }
    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!strongPasswordRegex.test(pass)) {
        showAlert('La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.', 'error');
        return;
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48 2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48 2.83-2.83" style="animation:spin 1s linear infinite;transform-origin:center"/></svg> Registrando...';

    try {
        const fd = new FormData(this);
        fd.append('tel_empresa', document.getElementById('tel_empresa').value);
        const res  = await fetch('../controllers/insert_empresa.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            showAlert('✅ ' + data.message + ' Redirigiendo al inicio de sesión...', 'success');
            setTimeout(() => { window.location.href = 'login.php'; }, 2500);
        } else {
            showAlert(data.message, 'error');
        }
    } catch (err) {
        showAlert('Error de conexión. Verifica tu internet e intenta de nuevo.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><polyline points="9 14 11 16 15 12"/></svg> Crear cuenta empresarial';
    }
});
</script>

</body>
</html>
