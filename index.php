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
    <title>GoWay - Tu destino a un solo toque</title>
    <meta name="description" content="GoWay conecta ciudades, personas y destinos. Gestiona rutas, flotas y checadores en tiempo real.">
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo time(); ?>">
    <link rel="icon" href="assets/images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<nav id="mainNav">
    <div class="nav-inner">
        <a href="index.php" class="nav-brand">
            <img src="assets/images/logo.png" alt="GoWay Logo">
            <span>GoWay</span>
        </a>
        <div class="nav-links">
            <a href="#empresas">Empresas</a>
            <a href="#checadores">Checadores</a>
            <a href="#usuarios">Usuarios</a>
            <a href="https://wheat-koala-810257.hostingersite.com/products/goway/" target="_blank">Descargar GoWay</a>
            <a href="https://wheat-koala-810257.hostingersite.com/products/goway_checador/" target="_blank">Descargar GoWay Checador</a>
        </div>
        <div class="nav-actions">
            <a href="pages/login.php" class="btn-primary-nav">Iniciar sesión</a>
            <button class="nav-burger" id="navBurger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
    <div class="nav-mobile" id="navMobile">
        <a href="#empresas">Empresas</a>
        <a href="#checadores">Checadores</a>
        <a href="#usuarios">Usuarios</a>
        <a href="https://wheat-koala-810257.hostingersite.com/products/goway/" target="_blank">Descargar GoWay</a>
        <a href="https://wheat-koala-810257.hostingersite.com/products/goway_checador/" target="_blank">Descargar GoWay Checador</a>
        <a href="pages/login.php" class="btn-mobile-dl">Iniciar sesión</a>
    </div>
</nav>

<section class="hero" id="inicio">
    <h1>El transporte,<br><span>hecho inteligente.</span></h1>
    <p>
        GoWay transforma la manera en que gestionas flotas y rutas. Una plataforma centralizada y potente para un servicio eficiente.
    </p>
    
    <div class="hero-cta">
        <a href="pages/registro_empresa.php" class="btn-main btn-company">
            Registrar mi Empresa
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
        <a href="pages/registro.php" class="btn-main btn-user">
            Registrarme como Usuario
        </a>
    </div>

    <div class="hero-image">
        <div class="mockup-header">
            <div class="mockup-dot"></div>
            <div class="mockup-dot"></div>
            <div class="mockup-dot"></div>
        </div>
        <div class="mockup-body">
            <img src="assets/images/empresa/1-dashboard.png" alt="GoWay Dashboard">
        </div>
    </div>
</section>

<section class="section-empresa-intro reveal-on-scroll" id="empresas">
    <div class="empresa-intro-inner">

        <!-- TEXTO -->
        <div class="empresa-text-col">
            <div class="empresa-badge">Para Empresas</div>
            <h2 class="empresa-title">El panel que tu<br>empresa <span>necesita.</span></h2>
            <p class="empresa-desc">
                Administra tu flota de forma centralizada. Controla rutas, vehículos, horarios y checadores desde un solo lugar, con métricas en tiempo real que te ayudan a tomar mejores decisiones.
            </p>

            <div class="empresa-chips">
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                    Gestión de Rutas
                </span>
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 3v18"/></svg>
                    Control de Flota
                </span>
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 3v18h18"/><path d="m7 16 4-4 4 4 5-5"/></svg>
                    KPIs en Tiempo Real
                </span>
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Gestión de Checadores
                </span>
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Reportes Automáticos
                </span>
            </div>

            <a href="pages/registro_empresa.php" class="empresa-cta">
                Registrar mi empresa
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>

        <!-- VISUAL -->
        <div class="empresa-visual-col">
            <div class="empresa-mockup">
                <div class="empresa-mockup-bar">
                    <div class="empresa-mockup-dot"></div>
                    <div class="empresa-mockup-dot"></div>
                    <div class="empresa-mockup-dot"></div>
                </div>
                <img src="assets/images/empresa/1-dashboard.png" alt="Dashboard Empresa GoWay">
            </div>
            <div class="empresa-float-badge">
                <div class="badge-icon"><i class="fa-solid fa-chart-line" style="color: #fff;"></i></div>
                <div class="badge-info">
                    <strong>KPIs en vivo</strong>
                    <span>Actualización en tiempo real</span>
                </div>
            </div>
        </div>

    </div>

    <!-- TAB VIEWER - Explorer de funcionalidades -->
    <div class="empresa-tabs-section" id="caracteristicas">
        <div class="empresa-tabs-label">
            <h3>Explora cada funcionalidad</h3>
            <p>Selecciona una herramienta para ver cómo funciona en tu panel.</p>
        </div>

        <div class="tab-nav" role="tablist">
            <button class="tab-btn active" onclick="switchTab(this, 'tab-rutas')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                Rutas
            </button>
            <button class="tab-btn" onclick="switchTab(this, 'tab-flota')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 3v18"/></svg>
                Vehículos
            </button>
            <button class="tab-btn" onclick="switchTab(this, 'tab-kpis')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 3v18h18"/><path d="m7 16 4-4 4 4 5-5"/></svg>
                KPIs
            </button>
            <button class="tab-btn" onclick="switchTab(this, 'tab-reportes')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Reportes
            </button>
            <button class="tab-btn" onclick="switchTab(this, 'tab-notificaciones')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notificaciones
            </button>
        </div>

        <div class="tab-panels">

            <div class="tab-panel active" id="tab-rutas">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Gestión de Rutas</h4>
                        <p>Diseña, edita y supervisa cada recorrido de tu flota. Agrega paradas, ajusta horarios y monitorea el estado de cada ruta desde un panel centralizado y fácil de usar.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/2-rutas.png" alt="Gestión de Rutas">
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-flota">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Control de Flota</h4>
                        <p>Registra y administra cada vehículo de tu empresa. Asigna conductores, revisa el estado operativo y mantén tu flota siempre en orden y disponible para el servicio.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/5-vehiculos.png" alt="Vehículos">
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-kpis">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Indicadores y KPIs</h4>
                        <p>Visualiza el rendimiento de tu operación con gráficos claros y actualizados. Identifica áreas de mejora y toma decisiones informadas basándote en datos reales de tu servicio.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/1-dashboard.png" alt="KPIs y Dashboard">
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-reportes">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Generación de Reportes</h4>
                        <p>Exporta informes detallados de rutas, vehículos e incidencias. Documenta tu operación y comparte resultados con tu equipo de forma rápida y estructurada.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/8-reportes.png" alt="Reportes">
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-notificaciones">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Notificaciones</h4>
                        <p>Envía alertas en tiempo real a usuarios y checadores sobre cambios en rutas, retrasos o incidencias. Mantén a todos informados con un solo clic desde tu panel.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/9-notificaciones.png" alt="Notificaciones">
                    </div>
                </div>
            </div>

        </div>
    </div>

</section>

<!-- SECCIÓN CHECADORES -->
<section class="section-mobile alt-bg reveal-on-scroll" id="checadores">
    <div class="mobile-inner">

        <div class="mobile-text-col">
            <div class="mobile-badge checador">Para Checadores</div>
            <h2 class="mobile-title">Tu herramienta en el<br><span>campo de operación.</span></h2>
            <p class="mobile-desc">
                La app móvil de GoWay para checadores es rápida, intuitiva y diseñada para trabajar en movimiento. Reporta incidencias, busca vehículos y mantén el flujo del transporte actualizado en tiempo real.
            </p>

            <div class="mobile-features">
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div class="mfi-text">
                        <strong>Buscar vehículo asignado</strong>
                        <span>Localiza rápidamente el autobús asignado a tu ruta del día.</span>
                    </div>
                </div>
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="mfi-text">
                        <strong>Actualizar lugares</strong>
                        <span>Registra la ocupación del vehículo y paradas en tiempo real.</span>
                    </div>
                </div>
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div class="mfi-text">
                        <strong>Reporte de incidencias</strong>
                        <span>Documenta problemas en ruta con detalle y envía al instante.</span>
                    </div>
                </div>
            </div>

            <div class="mobile-notice">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span><strong>Acceso por invitación:</strong> Tu cuenta es creada por la empresa de transporte para la que trabajas. No es necesario registrarte.</span>
            </div>
        </div>

        <div class="carousel-wrapper">
            <div class="screenshots-grid">
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-1-login.jpeg" alt="Login">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-2-search-schedules.jpeg" alt="Buscar horarios">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-3-search-schedule-options.jpeg" alt="Opciones de horario">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-4-reports.jpeg" alt="Reportes">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-5-bitacora.jpeg" alt="Bitácora">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-6-bitacora-details.jpeg" alt="Detalles bitácora">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-7-bitacora-table-mode.jpeg" alt="Modo tabla">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-8-profile.jpeg" alt="Perfil">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/picture-9-config.jpeg" alt="Configuración">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/checador/pciture-10-notifications.jpeg" alt="Notificaciones">
                </div>
            </div>
            <div class="carousel-controls">
                <button class="btn-carousel prev-slide" aria-label="Anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="btn-carousel next-slide" aria-label="Siguiente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

    </div>
</section>

<!-- SECCIÓN USUARIOS -->
<section class="section-mobile reveal-on-scroll" id="usuarios">
    <div class="mobile-inner reverse">

        <div class="mobile-text-col">
            <div class="mobile-badge usuario">Para Usuarios</div>
            <h2 class="mobile-title">Tu transporte,<br><span>en tu bolsillo.</span></h2>
            <p class="mobile-desc">
                Consulta rutas, revisa horarios disponibles, guarda tus favoritas y recibe notificaciones en tiempo real. La app de usuario de GoWay hace que moverte por la ciudad sea simple y sin sorpresas.
            </p>

            <div class="mobile-features">
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-route"></i></div>
                    <div class="mfi-text">
                        <strong>Selección de rutas</strong>
                        <span>Explora todas las rutas disponibles y elige la que más te convenga.</span>
                    </div>
                </div>
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-clock"></i></div>
                    <div class="mfi-text">
                        <strong>Horarios en tiempo real</strong>
                        <span>Consulta los próximos horarios de salida y evita la espera.</span>
                    </div>
                </div>
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="mfi-text">
                        <strong>Reportes</strong>
                        <span>Consulta el historial de tus viajes y reporta incidencias desde la app.</span>
                    </div>
                </div>
            </div>

            <a href="pages/registro.php" class="mobile-cta">
                Crear cuenta gratuita
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>

        <div class="carousel-wrapper">
            <div class="screenshots-grid">
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-1-home.jpeg" alt="Inicio">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-2-selection.jpeg" alt="Selección de rutas">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-3-minimap.jpeg" alt="Minimapa">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-4-schedule-info.jpeg" alt="Horarios">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-5-favorites.jpeg" alt="Favoritos">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-6-reportes.jpeg" alt="Reportes">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-7-profile.jpeg" alt="Perfil">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-8-map.jpeg" alt="Mapa">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-9-config.jpeg" alt="Configuración">
                </div>
                <div class="screenshot-card">
                    <img src="assets/images/usuario/picture-10-notifications.jpeg" alt="Notificaciones">
                </div>
            </div>
            <div class="carousel-controls">
                <button class="btn-carousel prev-slide" aria-label="Anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="btn-carousel next-slide" aria-label="Siguiente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

    </div>
</section>

<!-- FOOTER MODERNO -->
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="#" class="brand-name" style="text-decoration: none;">
                <img src="assets/images/logo.png" alt="GoWay Logo">
                <span>GoWay</span>
            </a>
            <p class="footer-tagline">Tu compañero inteligente en el transporte público.</p>
        </div>
        
        <div class="footer-links">
            <div class="footer-col">
                <h5>Producto</h5>
                <a href="#empresas">Empresas</a>
                <a href="#checadores">Checadores</a>
                <a href="#usuarios">Usuarios</a>
                <a href="https://wheat-koala-810257.hostingersite.com/products/goway/" target="_blank">Descargar GoWay</a>
                <a href="https://wheat-koala-810257.hostingersite.com/products/goway_checador/" target="_blank">Descargar GoWay Checador</a>
            </div>
            <div class="footer-col">
                <h5>Cuenta</h5>
                <a href="pages/registro_empresa.php">Registrar empresa</a>
                <a href="pages/registro.php">Crear cuenta usuario</a>
                <a href="pages/login.php">Acceder al panel</a>
            </div>
            <div class="footer-col">
                <h5>Legal</h5>
                <a href="pages/legal/privacy.php">Política de Privacidad</a>
                <a href="pages/legal/terms.php">Términos de Servicio</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> GoWay. Un producto hecho por Artisan Software.</p>
        <div class="footer-socials">
            <a href="#" aria-label="Instagram">
                <i class="fab fa-instagram" style="font-size: 18px;"></i>
            </a>
            <a href="#" aria-label="X (Twitter)">
                <i class="fab fa-x-twitter" style="font-size: 18px;"></i>
            </a>
            <a href="#" aria-label="LinkedIn">
                <i class="fab fa-linkedin" style="font-size: 18px;"></i>
            </a>
        </div>
    </div>
</footer>

<script>
    // Mobile Menu Toggle
    const navBurger = document.getElementById('navBurger');
    const navMobile = document.getElementById('navMobile');
    if (navBurger && navMobile) {
        navBurger.addEventListener('click', () => {
            navBurger.classList.toggle('active');
            navMobile.classList.toggle('open');
        });
        navMobile.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navBurger.classList.remove('active');
                navMobile.classList.remove('open');
            });
        });
    }

    // Tab Viewer logic
    function switchTab(btn, panelId) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(panelId).classList.add('active');
    }

    // Navbar scroll effect
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 10);
    });

    // Scroll reveal logic
    const revealElements = document.querySelectorAll('.reveal-on-scroll');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                // Dejar de observar una vez que ya apareció
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    });

    revealElements.forEach(el => revealObserver.observe(el));

    // Lógica para Carrusel 3D Coverflow (estilo Pear Inc)
    document.querySelectorAll('.carousel-wrapper').forEach(wrapper => {
        const grid = wrapper.querySelector('.screenshots-grid');
        const slides = Array.from(grid.querySelectorAll('.screenshot-card'));
        const btnPrev = wrapper.querySelector('.prev-slide');
        const btnNext = wrapper.querySelector('.next-slide');
        // Iniciar en la tarjeta central (índice 1 si son 3 tarjetas)
        let currentIndex = Math.floor(slides.length / 2);

        function updateCarousel() {
            const isMobile = window.innerWidth <= 900;
            let baseTx = isMobile ? 80 : 130;
            let stepTx = isMobile ? 40 : 60;
            let baseTz = 100;

            slides.forEach((slide, index) => {
                const offset = index - currentIndex;
                const absOffset = Math.abs(offset);
                const sign = Math.sign(offset);
                
                let tx = absOffset === 0 ? 0 : sign * (baseTx + (absOffset - 1) * stepTx);
                let tz = absOffset === 0 ? 0 : -(absOffset * baseTz);
                let scale = absOffset === 0 ? 1 : (1 - (absOffset * 0.15));
                
                slide.style.transform = `translate3d(${tx}px, 0, ${tz}px) scale(${scale})`;
                slide.style.zIndex = 10 - absOffset;
                slide.style.opacity = absOffset > 2 ? 0 : 1;
            });
        }

        slides.forEach((slide, i) => {
            slide.addEventListener('click', () => {
                currentIndex = i;
                updateCarousel();
            });
        });

        if (btnPrev) {
            btnPrev.addEventListener('click', () => {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateCarousel();
                }
            });
        }
        
        if (btnNext) {
            btnNext.addEventListener('click', () => {
                if (currentIndex < slides.length - 1) {
                    currentIndex++;
                    updateCarousel();
                }
            });
        }

        updateCarousel();
        window.addEventListener('resize', updateCarousel);
    });
</script>
</body>
</html>
