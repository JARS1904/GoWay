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

// Categorías de navegación: [Categoría => [slug => [label, icon_file, href]]]
$nav_categories = [
    'Principal' => [
        'dashboard'   => ['label' => 'Dashboard',   'icon' => 'icon_dashboard.png',   'href' => $base_url . 'index.php'],
        'empresas'    => ['label' => 'Empresa',     'icon' => 'icon_empresas.png',    'href' => $admin_prefix . 'empresas.php'],
        'rutas'       => ['label' => 'Rutas',       'icon' => 'icon_rutas.png',       'href' => $admin_prefix . 'rutas.php'],
        'horarios'    => ['label' => 'Horarios',    'icon' => 'icon_horarios.png',    'href' => $admin_prefix . 'horarios.php'],
        'conductores' => ['label' => 'Conductores', 'icon' => 'icon_conductores.png', 'href' => $admin_prefix . 'conductores.php'],
        'vehiculos'   => ['label' => 'Vehículos',   'icon' => 'icon_vehiculos.png',   'href' => $admin_prefix . 'vehiculos.php'],
        'paradas'     => ['label' => 'Paradas',     'icon' => 'icon_paradas.png',     'href' => $admin_prefix . 'paradas_ruta.php'],
        'asignaciones'=> ['label' => 'Asignaciones','icon' => 'icon_asignacion.png',  'href' => $admin_prefix . 'asignaciones.php'],
    ],
    'Usuarios' => [
        'usuarios'    => ['label' => 'Usuarios',    'icon' => 'icon_usuarios.png',    'href' => $admin_prefix . 'usuarios.php'],
        'checadores'  => ['label' => 'Checador',    'icon' => 'icon_checadores.png',  'href' => $admin_prefix . 'checadores.php'],
    ],
    'Gestión' => [
        'reportes'    => ['label' => 'Reportes',    'icon' => 'icon_reportes.png',    'href' => $admin_prefix . 'reportes.php'],
        'notificaciones'=>['label'=> 'Notificaciones','icon'=> 'icons_notifications.png', 'href' => $admin_prefix . 'notificaciones.php'],
    ]
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
        <button class="desktop-toggle-btn" onclick="toggleDesktopSidebar()">
            <img src="<?php echo $base_url; ?>assets/images/icons/icons8_panel.png" alt="Colapsar" style="width: 24px; height: 24px; object-fit: contain;">
        </button>
    </div>
    <nav>
        <?php foreach ($nav_categories as $category_name => $items): ?>
        <?php 
            // Replace spaces and special chars for ID
            $cat_id = 'cat_' . preg_replace('/[^a-zA-Z0-9]/', '', $category_name);
        ?>
        <div class="sidebar-category open" onclick="toggleCategory('<?php echo $cat_id; ?>', this)">
            <h4><?php echo $category_name; ?></h4>
            <span class="material-icons category-chevron">expand_more</span>
        </div>
        <ul id="<?php echo $cat_id; ?>" class="category-list open">
            <?php foreach ($items as $slug => $item): ?>
            <li>
                <a href="<?php echo $item['href']; ?>" title="<?php echo $item['label']; ?>"<?php echo ($active_page === $slug) ? ' class="active"' : ''; ?>>
                    <?php if (isset($item['is_material']) && $item['is_material']): ?>
                        <span class="material-icons icon" style="font-size: 22px; line-height: 20px; text-align: center; display: block; margin-right: 10px; color: inherit;"><?php echo $item['icon']; ?></span>
                    <?php else: ?>
                        <img src="<?php echo $base_url; ?>assets/images/icons/<?php echo $item['icon']; ?>" alt="<?php echo $item['label']; ?>" class="icon">
                    <?php endif; ?>
                    <span><?php echo $item['label']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endforeach; ?>
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
    // Funciones para categorías colapsables
    function toggleCategory(categoryId, headerElement) {
        const sidebar = document.getElementById('sidebar');
        // Prevenir toggle si el sidebar general está colapsado (donde no se ven encabezados)
        if (sidebar.classList.contains('collapsed')) return;

        const ul = document.getElementById(categoryId);
        ul.classList.toggle('open');
        headerElement.classList.toggle('open');
    }

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

    function toggleDesktopSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
        
        if (sidebar.classList.contains('collapsed')) {
            localStorage.setItem('sidebarCollapsed', 'true');
        } else {
            localStorage.setItem('sidebarCollapsed', 'false');
        }
    }

    // Persist sidebar state on load
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth > 768) {
            document.getElementById('sidebar').classList.add('collapsed');
        }
    });

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
