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
</head>
<body>

<style>
/* ── Login Page ─────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #c7d8ff 0%, #dce9ff 50%, #eef2ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
}

/* ── Split wrapper ───────────────────────────────── */
.lg-wrap {
    display: flex;
    width: 100%;
    max-width: 920px;
    min-height: 560px;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 24px 72px rgba(41,98,255,.18), 0 6px 24px rgba(0,0,0,.09);
    animation: lg-in .3s ease;
}
@keyframes lg-in {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Left brand panel ───────────────────────────── */
.lg-panel {
    flex: 0 0 50%;
    background: linear-gradient(160deg, #1e4fff 0%, #0d3acc 55%, #0a2fa8 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 44px 40px;
    gap: 0;
    position: relative;
    overflow: hidden;
}
/* subtle decorative circles */
.lg-panel::before,
.lg-panel::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    opacity: .12;
    background: #fff;
}
.lg-panel::before { width: 280px; height: 280px; top: -80px; right: -80px; }
.lg-panel::after  { width: 200px; height: 200px; bottom: -60px; left: -60px; }

.lg-panel-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
}
.lg-panel-brand img {
    width: 52px; height: 52px;
    object-fit: contain;
}
.lg-panel-brand span {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.5px;
}

.lg-panel-tagline {
    font-size: 1.05rem;
    font-weight: 500;
    color: rgba(255,255,255,.82);
    text-align: center;
    line-height: 1.55;
    margin-bottom: 28px;
}

.lg-panel-img {
    width: 100%;
    max-width: 260px;
    object-fit: contain;
    filter: drop-shadow(0 8px 24px rgba(0,0,0,.25));
    margin-bottom: 24px;
}

.lg-panel-note {
    font-size: .8rem;
    color: rgba(255,255,255,.55);
    text-align: center;
    letter-spacing: .02em;
}

/* ── Right form panel ───────────────────────────── */
.lg-card {
    flex: 1;
    background: #fff;
    padding: 48px 44px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.lg-title {
    font-size: 1.45rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 6px;
}
.lg-sub {
    font-size: .85rem;
    color: #6B7280;
    margin-bottom: 30px;
}

.lg-error {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    color: #B91C1C;
    font-size: .82rem;
    padding: 10px 14px;
    border-radius: 10px;
    margin-bottom: 18px;
}

.lg-field { margin-bottom: 18px; }
.lg-field label {
    display: block;
    font-size: .82rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 7px;
    letter-spacing: .02em;
}
.lg-input-wrap { position: relative; display: block; }
.lg-input-wrap .lg-ico {
    position: absolute;
    left: 13px;
    top: 50%; transform: translateY(-50%);
    width: 16px; height: 16px;
    stroke: #9CA3AF;
    pointer-events: none;
}
.lg-input-wrap input {
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
.lg-input-wrap input:focus {
    border-color: #2962FF;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(41,98,255,.11);
}
/* ocultar el ojo nativo del navegador */
.lg-input-wrap input[type="password"]::-ms-reveal,
.lg-input-wrap input[type="password"]::-ms-clear { display: none; }
.lg-toggle {
    position: absolute; right: 11px;
    top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    padding: 4px; display: flex; align-items: center; color: #9CA3AF;
}
.lg-toggle svg { width: 16px; height: 16px; stroke: currentColor; }
.lg-toggle:hover { color: #6B7280; }

.lg-row {
    display: flex;
    justify-content: flex-end;
    margin-top: -10px;
    margin-bottom: 20px;
}
.lg-row a { font-size: .8rem; color: #2962FF; text-decoration: none; }
.lg-row a:hover { text-decoration: underline; }

.lg-btn {
    width: 100%;
    padding: 13px;
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
.lg-btn:hover  { opacity: .91; transform: translateY(-1px); }
.lg-btn:active { transform: translateY(0); }

.lg-footer {
    text-align: center;
    margin-top: 22px;
    font-size: .83rem;
    color: #6B7280;
}
.lg-footer a { color: #2962FF; font-weight: 600; text-decoration: none; }
.lg-footer a:hover { text-decoration: underline; }

.lg-divider {
    border: none;
    border-top: 1px solid #F3F4F6;
    margin: 22px 0 18px;
}
.lg-admin-btn {
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
.lg-admin-btn svg { width: 15px; height: 15px; stroke: currentColor; }
.lg-admin-btn:hover { border-color: #2962FF; color: #2962FF; }

/* ── Responsive ─────────────────────────────────── */
@media (max-width: 680px) {
    .lg-wrap { flex-direction: column; min-height: unset; border-radius: 18px; }
    .lg-panel { flex: none; padding: 32px 24px 28px; }
    .lg-panel-img { max-width: 180px; margin-bottom: 0; }
    .lg-panel-note { display: none; }
    .lg-card { padding: 32px 24px 28px; }
}
</style>

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

        <p class="lg-footer">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>

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

    <style>
    /* ── Admin Login Modal ─────────────────────────────── */
    .adm-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 15, 30, 0.65);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .adm-overlay.active { display: flex; }

    .adm-card {
        position: relative;
        background: #fff;
        width: 100%;
        max-width: 350px;
        border-radius: 20px;
        padding: 44px 40px 36px;
        box-shadow: 0 24px 64px rgba(0,0,0,.22);
        animation: adm-in .25s ease;
        overflow: hidden;
    }
    @keyframes adm-in {
        from { opacity: 0; transform: translateY(-22px) scale(.97); }
        to   { opacity: 1; transform: translateY(0)    scale(1);    }
    }

    .adm-close {
        position: absolute;
        top: 16px; right: 18px;
        background: #f1f3f7;
        border: none;
        width: 32px; height: 32px;
        border-radius: 50%;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        color: #555;
        display: flex; align-items: center; justify-content: center;
        transition: background .18s;
    }
    .adm-close:hover { background: #e2e6ef; color: #111; }

    .adm-icon-wrap {
        width: 64px; height: 64px;
        background: linear-gradient(135deg, #2962FF22, #2962FF11);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 18px;
    }
    .adm-icon-wrap svg { width: 30px; height: 30px; color: #2962FF; stroke: #2962FF; }

    .adm-title {
        text-align: center;
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        margin: 0 0 6px;
    }
    .adm-subtitle {
        text-align: center;
        font-size: .85rem;
        color: #6B7280;
        margin: 0 0 28px;
    }

    .adm-error {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #B91C1C;
        font-size: .83rem;
        padding: 10px 14px;
        border-radius: 10px;
        margin-bottom: 18px;
    }

    .adm-field { margin-bottom: 18px; }
    .adm-field label {
        display: block;
        font-size: .82rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 7px;
        letter-spacing: .02em;
    }

    .adm-input-wrap {
        position: relative;
        display: block;
    }
    .adm-input-wrap > svg:first-child {
        position: absolute;
        left: 13px;
        top: 50%; transform: translateY(-50%);
        width: 16px; height: 16px;
        color: #9CA3AF; stroke: #9CA3AF;
        pointer-events: none;
    }
    .adm-input-wrap input {
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
    .adm-input-wrap input:focus {
        border-color: #2962FF;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(41,98,255,.12);
    }

    /* ocultar el ojo nativo del navegador */
    .adm-input-wrap input[type="password"]::-ms-reveal,
    .adm-input-wrap input[type="password"]::-ms-clear { display: none; }
    .adm-toggle-pw {
        position: absolute; right: 11px;
        top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        padding: 4px; display: flex; align-items: center; color: #9CA3AF;
    }
    .adm-toggle-pw svg { width: 16px; height: 16px; stroke: currentColor; }
    .adm-toggle-pw:hover { color: #6B7280; }

    .adm-submit {
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
        transition: opacity .18s, transform .12s;
        box-shadow: 0 4px 14px rgba(41,98,255,.35);
    }
    .adm-submit:hover  { opacity: .92; transform: translateY(-1px); }
    .adm-submit:active { transform: translateY(0); }

    @media (max-width: 480px) {
        .adm-card { padding: 36px 22px 28px; }
    }
    </style>

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