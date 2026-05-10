<?php
// Si el usuario ya tiene sesión, ir al dashboard
session_start();
if (isset($_SESSION['id'])) {
    header('Location: pages/admin/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWay — Tu destino, a un solo toque</title>
    <meta name="description" content="GoWay conecta ciudades, personas y destinos. Gestiona rutas, flotas y checadores en tiempo real.">
    <link rel="icon" href="assets/images/logo_new.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --blue:    #2962FF;
    --blue-dk: #1a50e8;
    --blue-lt: #EEF3FF;
    --text:    #111827;
    --sub:     #6B7280;
    --border:  #E5E7EB;
    --bg:      #F9FAFB;
}
html { scroll-behavior: smooth; }
body { font-family: 'Inter', system-ui, sans-serif; color: var(--text); background: #fff; overflow-x: hidden; }

/* NAV */
nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 6%; height: 68px;
    background: rgba(255,255,255,0.88); backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(229,231,235,0.7); transition: box-shadow .2s;
}
nav.scrolled { box-shadow: 0 4px 24px rgba(41,98,255,.09); }
.nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.nav-brand img { width: 36px; height: 36px; object-fit: contain; }
.nav-brand span { font-size: 1.35rem; font-weight: 800; color: var(--blue); letter-spacing: -.5px; }
.nav-links { display: flex; align-items: center; gap: 10px; }
.nav-links a {
    text-decoration: none; font-size: .88rem; font-weight: 500;
    color: var(--sub); padding: 8px 16px; border-radius: 9px;
    transition: color .18s, background .18s;
}
.nav-links a:hover { color: var(--text); background: var(--bg); }
.nav-links .btn-primary { background: var(--blue); color: #fff !important; box-shadow: 0 3px 10px rgba(41,98,255,.28); }
.nav-links .btn-primary:hover { background: var(--blue-dk); }

/* HERO */
.hero {
    min-height: 100vh;
    background: linear-gradient(160deg, #0a2fa8 0%, #1e4fff 45%, #4d86ff 100%);
    display: flex; align-items: center;
    padding: 100px 6% 80px; position: relative; overflow: hidden;
}
.hero::before { content:''; position:absolute; width:600px; height:600px; border-radius:50%; background:rgba(255,255,255,.05); top:-200px; right:-100px; }
.hero::after  { content:''; position:absolute; width:400px; height:400px; border-radius:50%; background:rgba(255,255,255,.04); bottom:-150px; left:-80px; }
.hero-inner { max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; gap:60px; width:100%; position:relative; z-index:1; }
.hero-text { flex: 1; }
.hero-badge {
    display:inline-flex; align-items:center; gap:7px;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
    color:#fff; font-size:.78rem; font-weight:600;
    padding:6px 14px; border-radius:99px; margin-bottom:28px;
    letter-spacing:.06em; text-transform:uppercase;
}
.hero-badge span { width:7px; height:7px; border-radius:50%; background:#4ade80; display:inline-block; }
.hero h1 { font-size:clamp(2.4rem,5vw,3.8rem); font-weight:900; color:#fff; line-height:1.1; letter-spacing:-.03em; margin-bottom:22px; }
.hero h1 em { font-style:normal; color:#93c5fd; }
.hero-sub { font-size:1.1rem; color:rgba(255,255,255,.78); line-height:1.65; max-width:480px; margin-bottom:40px; }
.hero-cta { display:flex; gap:14px; flex-wrap:wrap; }
.btn-hero-main {
    display:inline-flex; align-items:center; gap:8px;
    background:#fff; color:var(--blue); font-size:.97rem; font-weight:700;
    padding:14px 28px; border-radius:12px; text-decoration:none;
    box-shadow:0 8px 28px rgba(0,0,0,.18); transition:transform .18s,box-shadow .18s;
}
.btn-hero-main:hover { transform:translateY(-2px); box-shadow:0 12px 36px rgba(0,0,0,.22); }
.btn-hero-sec {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(255,255,255,.12); color:#fff;
    border:1.5px solid rgba(255,255,255,.3);
    font-size:.97rem; font-weight:600;
    padding:14px 28px; border-radius:12px; text-decoration:none;
    transition:background .18s,transform .18s;
}
.btn-hero-sec:hover { background:rgba(255,255,255,.2); transform:translateY(-2px); }
.hero-visual { flex:0 0 420px; display:flex; align-items:center; justify-content:center; }
.hero-card {
    background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);
    border-radius:24px; padding:32px; backdrop-filter:blur(12px); width:100%;
    animation:float 4s ease-in-out infinite;
}
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
.hero-card-logo { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
.hero-card-logo img { width:44px; height:44px; }
.hero-card-logo span { font-size:1.5rem; font-weight:800; color:#fff; }
.hero-stats { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.hero-stat { background:rgba(255,255,255,.1); border-radius:14px; padding:16px; text-align:center; }
.hero-stat-num { font-size:1.8rem; font-weight:800; color:#fff; }
.hero-stat-lbl { font-size:.75rem; color:rgba(255,255,255,.65); margin-top:2px; }

/* FEATURES */
.features { padding:100px 6%; background:var(--bg); }
.section-label { text-align:center; font-size:.78rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--blue); margin-bottom:12px; }
.section-title { text-align:center; font-size:clamp(1.6rem,3vw,2.4rem); font-weight:800; color:var(--text); letter-spacing:-.03em; margin-bottom:14px; }
.section-sub { text-align:center; font-size:1rem; color:var(--sub); max-width:500px; margin:0 auto 60px; line-height:1.65; }
.features-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:24px; max-width:1100px; margin:0 auto; }
.feat-card { background:#fff; border-radius:20px; padding:32px; border:1px solid var(--border); box-shadow:0 2px 12px rgba(0,0,0,.04); transition:transform .2s,box-shadow .2s; }
.feat-card:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(41,98,255,.1); }
.feat-icon { width:52px; height:52px; border-radius:14px; background:var(--blue-lt); display:flex; align-items:center; justify-content:center; margin-bottom:20px; font-size:1.5rem; }
.feat-card h3 { font-size:1.05rem; font-weight:700; margin-bottom:10px; }
.feat-card p  { font-size:.88rem; color:var(--sub); line-height:1.6; }

/* FOR WHO */
.forwho { padding:100px 6%; background:#fff; }
.forwho-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:24px; max-width:1100px; margin:0 auto; }
.who-card { border-radius:20px; padding:36px 28px; display:flex; flex-direction:column; gap:14px; transition:transform .2s; }
.who-card:hover { transform:translateY(-4px); }
.who-card.blue   { background:linear-gradient(135deg,#2962FF,#1a50e8); color:#fff; }
.who-card.indigo { background:linear-gradient(135deg,#4f46e5,#3730a3); color:#fff; }
.who-card.teal   { background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; }
.who-icon { font-size:2.2rem; }
.who-card h3 { font-size:1.15rem; font-weight:700; }
.who-card p  { font-size:.88rem; opacity:.82; line-height:1.6; }
.who-link { margin-top:auto; display:inline-flex; align-items:center; gap:6px; font-size:.85rem; font-weight:600; color:rgba(255,255,255,.9); text-decoration:none; transition:gap .18s; }
.who-link:hover { gap:10px; }

/* CTA */
.cta-section { padding:100px 6%; background:linear-gradient(135deg,#0a2fa8,#2962FF); text-align:center; }
.cta-section h2 { font-size:clamp(1.8rem,3vw,2.6rem); font-weight:900; color:#fff; letter-spacing:-.03em; margin-bottom:16px; }
.cta-section p { color:rgba(255,255,255,.78); font-size:1.05rem; margin-bottom:40px; }
.cta-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }

/* FOOTER */
footer { background:#0f172a; color:rgba(255,255,255,.5); text-align:center; padding:28px 6%; font-size:.83rem; }
footer strong { color:rgba(255,255,255,.8); }

/* RESPONSIVE */
@media (max-width:860px) {
    .hero-inner { flex-direction:column; text-align:center; }
    .hero-sub { max-width:100%; }
    .hero-cta { justify-content:center; }
    .hero-visual { flex:none; width:100%; max-width:360px; }
    .nav-links a:not(.btn-primary) { display:none; }
    .nav-links .btn-primary { display:flex; }
}
</style>
</head>
<body>

<nav id="mainNav">
    <a href="index.php" class="nav-brand">
        <img src="assets/images/logo_new.png" alt="GoWay">
        <span>GoWay</span>
    </a>
    <div class="nav-links">
        <a href="#features">Características</a>
        <a href="#quien">Para quién</a>
        <a href="pages/registro.php" style="color:var(--blue);font-weight:600;">Crear cuenta</a>
        <a href="pages/login.php" class="btn-primary">Iniciar sesión</a>
    </div>
</nav>

<section class="hero" id="inicio">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-badge">
                <span></span> Sistema de Transporte Público
            </div>
            <h1>Tu destino,<br><em>a un solo toque.</em></h1>
            <p class="hero-sub">
                GoWay conecta empresas de transporte, conductores y usuarios en una plataforma inteligente. Gestiona rutas, flotas y horarios en tiempo real.
            </p>
            <div class="hero-cta">
                <a href="pages/login.php" class="btn-hero-main">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Iniciar sesión
                </a>
                <a href="pages/registro.php" class="btn-hero-sec">
                    Crear cuenta gratis
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-card">
                <div class="hero-card-logo">
                    <img src="assets/images/logo_new.png" alt="GoWay">
                    <span>GoWay</span>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat"><div class="hero-stat-num">🚌</div><div class="hero-stat-lbl">Flotas</div></div>
                    <div class="hero-stat"><div class="hero-stat-num">📍</div><div class="hero-stat-lbl">Rutas</div></div>
                    <div class="hero-stat"><div class="hero-stat-num">🕐</div><div class="hero-stat-lbl">Horarios</div></div>
                    <div class="hero-stat"><div class="hero-stat-num">📊</div><div class="hero-stat-lbl">KPIs</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="features" id="features">
    <p class="section-label">¿Por qué GoWay?</p>
    <h2 class="section-title">Todo lo que necesitas en un solo lugar</h2>
    <p class="section-sub">Una plataforma completa para gestionar el transporte público de manera eficiente y segura.</p>
    <div class="features-grid">
        <div class="feat-card"><div class="feat-icon">🗺️</div><h3>Gestión de Rutas</h3><p>Define, edita y monitorea todas tus rutas de transporte con paradas y destinos configurables.</p></div>
        <div class="feat-card"><div class="feat-icon">🚌</div><h3>Control de Flota</h3><p>Administra tu flota de vehículos, conductores y asignaciones desde un panel centralizado.</p></div>
        <div class="feat-card"><div class="feat-icon">🕐</div><h3>Horarios en Tiempo Real</h3><p>Configura y consulta horarios actualizados para cada ruta y vehículo de tu empresa.</p></div>
        <div class="feat-card"><div class="feat-icon">📊</div><h3>KPIs e Indicadores</h3><p>Visualiza el rendimiento de tu operación con gráficos y estadísticas en tiempo real.</p></div>
        <div class="feat-card"><div class="feat-icon">🔔</div><h3>Notificaciones</h3><p>Envía avisos instantáneos a tus usuarios y checadores sobre cambios en el servicio.</p></div>
        <div class="feat-card"><div class="feat-icon">🔒</div><h3>Seguridad Avanzada</h3><p>Acceso multi-rol con contraseñas fuertes. Cada empresa gestiona sus propios datos de forma aislada.</p></div>
    </div>
</section>

<section class="forwho" id="quien">
    <p class="section-label">Para quién es GoWay</p>
    <h2 class="section-title">Una solución para cada rol</h2>
    <p class="section-sub">Desde el administrador hasta el usuario final, GoWay tiene una experiencia diseñada para ti.</p>
    <div class="forwho-grid">
        <div class="who-card blue">
            <div class="who-icon">🏢</div>
            <h3>Empresas de Transporte</h3>
            <p>Registra tu empresa, gestiona tu flota y mantén control total de rutas, conductores y horarios.</p>
            <a href="pages/registro_empresa.php" class="who-link">Registrar empresa →</a>
        </div>
        <div class="who-card indigo">
            <div class="who-icon">✅</div>
            <h3>Checadores</h3>
            <p>Reporta incidencias en tiempo real directamente desde la app móvil asignada por tu empresa.</p>
            <a href="pages/login.php" class="who-link">Acceder →</a>
        </div>
        <div class="who-card teal">
            <div class="who-icon">👤</div>
            <h3>Usuarios</h3>
            <p>Consulta rutas, horarios y favoritos desde la app GoWay. Recibe notificaciones de tu servicio.</p>
            <a href="pages/registro.php" class="who-link">Crear cuenta →</a>
        </div>
    </div>
</section>

<section class="cta-section">
    <h2>¿Listo para comenzar?</h2>
    <p>Únete a GoWay y transforma la forma en que gestionas el transporte público.</p>
    <div class="cta-btns">
        <a href="pages/registro_empresa.php" class="btn-hero-main">Registrar mi empresa</a>
        <a href="pages/login.php" class="btn-hero-sec">Iniciar sesión</a>
    </div>
</section>

<footer>
    <p>&copy; <?php echo date('Y'); ?> <strong>GoWay</strong> — Sistema de Transporte Público. Todos los derechos reservados.</p>
</footer>

<script>
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 20);
    });
</script>
</body>
</html>
