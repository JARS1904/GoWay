<?php
/**
 * Componente compartido: Sidebar del panel de administración.
 *
 * Variables requeridas antes de incluir este archivo:
 *   $page_title  (string) – Título de la página, ej. 'Gestión de Rutas'
 *   $active_page (string) – Slug del ítem activo, ej. 'rutas'
 *   $base_url    (string) – Prefijo de raíz: '' para index.php, '../../' para páginas admin
 *
 * La variable $admin_prefix se calcula internamente:
 *   '' para index.php → los links admin van como 'pages/admin/X.php'
 *   '../../' para páginas admin → los links admin van como 'X.php' (misma carpeta)
 */

$admin_prefix = ($base_url === '') ? 'pages/admin/' : '';
$logout_url   = $base_url . 'pages/logout.php';

// Ítems de navegación: [slug => [label, icon_file, href]]
$nav_items = [
    'dashboard'   => ['label' => 'Dashboard',    'icon' => 'icon_dashboard.png',    'href' => $base_url . 'index.php'],
    'empresas'    => ['label' => 'Empresas',      'icon' => 'icon_empresas.png',     'href' => $admin_prefix . 'empresas.php'],
    'conductores' => ['label' => 'Conductores',   'icon' => 'icon_conductores.png',  'href' => $admin_prefix . 'conductores.php'],
    'vehiculos'   => ['label' => 'Vehículos',     'icon' => 'icon_vehiculos.png',    'href' => $admin_prefix . 'vehiculos.php'],
    'rutas'       => ['label' => 'Rutas',         'icon' => 'icon_rutas.png',        'href' => $admin_prefix . 'rutas.php'],
    'horarios'    => ['label' => 'Horarios',      'icon' => 'icon_horarios.png',     'href' => $admin_prefix . 'horarios.php'],
    'paradas'     => ['label' => 'Paradas',       'icon' => 'icon_paradas.png',      'href' => $admin_prefix . 'paradas_ruta.php'],
    'asignaciones'=> ['label' => 'Asignaciones',  'icon' => 'icon_asignacion.png',   'href' => $admin_prefix . 'asignaciones.php'],
    'checadores'  => ['label' => 'Checadores',    'icon' => 'icon_checadores.png',   'href' => $admin_prefix . 'checadores.php'],
    'reportes'    => ['label' => 'Reportes',      'icon' => 'icon_reportes.png',     'href' => $admin_prefix . 'reportes.php'],
    'usuarios'    => ['label' => 'Usuarios',      'icon' => 'icon_usuarios.png',     'href' => $admin_prefix . 'usuarios.php'],
];
?>

<!-- Overlay para fondo oscuro -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Barra Superior Móvil -->
<div class="mobile-topbar">
    <div class="mobile-topbar-content">
        <div class="mobile-topbar-left">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <h1 class="mobile-page-title"><?php echo htmlspecialchars($page_title); ?></h1>
        </div>
        <div class="mobile-topbar-right">
            <div class="mobile-user-info">
                <?php echo !empty($_SESSION['foto'])
                    ? '<img src="' . $base_url . 'assets/images/profiles/' . htmlspecialchars($_SESSION['foto']) . '" alt="Usuario" class="header-user-avatar">'
                    : '<img src="' . $base_url . 'assets/images/icons/administrador.png" alt="Usuario">'; ?>
                <span><?php echo $_SESSION['nombre']; ?></span>
                <button class="notification-bell" id="mobileNotifBtn" onclick="toggleNotifications()">
                    <span class="material-icons">notifications_none</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Menú Lateral -->
<aside id="sidebar" class="sidebar">
    <!-- Botón de Cerrar para Móvil -->
    <button class="sidebar-close" onclick="closeSidebar()">&times;</button>

    <div class="logo">
        <img src="<?php echo $base_url; ?>assets/images/logo_new.png" alt="Logo de GoWay" class="logo-img">
        <h1>GoWay</h1>
    </div>
    <nav>
        <ul>
            <?php foreach ($nav_items as $slug => $item): ?>
            <li>
                <a href="<?php echo $item['href']; ?>"<?php echo ($active_page === $slug) ? ' class="active"' : ''; ?>>
                    <img src="<?php echo $base_url; ?>assets/images/icons/<?php echo $item['icon']; ?>" alt="<?php echo $item['label']; ?>" class="icon">
                    <span><?php echo $item['label']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Perfil de usuario + Logout -->
    <div class="sidebar-user-card">
        <div class="sidebar-user-avatar-wrap">
            <?php if (!empty($_SESSION['foto'])): ?>
                <img src="<?php echo $base_url; ?>assets/images/profiles/<?php echo htmlspecialchars($_SESSION['foto']); ?>"
                     alt="Foto de perfil" class="sidebar-user-avatar">
            <?php else: ?>
                <img src="<?php echo $base_url; ?>assets/images/icons/administrador.png"
                     alt="Administrador" class="sidebar-user-avatar">
            <?php endif; ?>
        </div>
        <div class="sidebar-user-info">
            <span class="sidebar-user-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
            <span class="sidebar-user-role">Administrador</span>
        </div>
        <a href="<?php echo $logout_url; ?>" id="logout" class="sidebar-logout-btn" title="Cerrar sesión">
            <span class="material-icons">logout</span>
        </a>
    </div>
</aside>


<script>
    // Funciones para el menú hamburguesa
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.querySelector('.toggle-btn');

        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');

        // Ocultar/mostrar botón hamburguesa
        if (sidebar.classList.contains('active')) {
            toggleBtn.style.opacity = '0';
            toggleBtn.style.visibility = 'hidden';
        } else {
            toggleBtn.style.opacity = '1';
            toggleBtn.style.visibility = 'visible';
        }

        // Prevenir scroll del body cuando el menú está abierto
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.querySelector('.toggle-btn');

        sidebar.classList.remove('active');
        overlay.classList.remove('active');

        // Mostrar botón hamburguesa al cerrar
        toggleBtn.style.opacity = '1';
        toggleBtn.style.visibility = 'visible';

        document.body.style.overflow = '';
    }

    // Cerrar sidebar al hacer clic en un enlace (en móvil)
    document.querySelectorAll('.sidebar nav a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });

    // Cerrar sidebar con tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });

    // Ajustar en redimensionamiento de ventana
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
    
    // Modal de cerrar sesión
    document.addEventListener('DOMContentLoaded', function() {
        const logoutLink = document.getElementById('logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                const modal = document.getElementById('logoutConfirmModal');
                const confirmBtn = document.getElementById('confirmLogoutBtn');
                if (modal && confirmBtn) {
                    confirmBtn.setAttribute('href', this.getAttribute('href'));
                    modal.classList.add('active');
                }
            });
        }
    });
</script>
