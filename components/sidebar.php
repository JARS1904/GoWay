<?php
/**
 * Componente compartido: Sidebar del panel de administración.
 */

$admin_prefix = ($base_url === '../../') ? '' : 'pages/admin/';
$logout_url   = $base_url . 'pages/logout.php';

$nav_categories = [
    'Principal' => [
        'dashboard' => ['label' => 'Dashboard', 'icon' => 'sidebar/icons8-dashboard-layout-100.png', 'href' => $admin_prefix . 'dashboard.php']
    ]
];

if ($_SESSION['rol'] == 1) {
    $nav_categories['Principal']['empresas'] = ['label' => 'Empresa', 'icon' => 'sidebar/icons8-enterprise-100.png', 'href' => $admin_prefix . 'empresas.php'];
}

$nav_categories['Principal'] = array_merge($nav_categories['Principal'], [
    'rutas'        => ['label' => 'Rutas',        'icon' => 'sidebar/icons8-puntero-azul-100.png',        'href' => $admin_prefix . 'rutas.php'],
    'horarios'     => ['label' => 'Horarios',     'icon' => 'sidebar/icons8-reloj-100.png',     'href' => $admin_prefix . 'horarios.php'],
    'conductores'  => ['label' => 'Conductores',  'icon' => 'sidebar/icons8-conductor-100.png',  'href' => $admin_prefix . 'conductores.php'],
    'vehiculos'    => ['label' => 'Vehículos',    'icon' => 'sidebar/icons8-servicio-de-transporte-100.png',    'href' => $admin_prefix . 'vehiculos.php'],
    'paradas'      => ['label' => 'Paradas',      'icon' => 'sidebar/icons8-privado-100.png',      'href' => $admin_prefix . 'paradas_ruta.php'],
    'asignaciones' => ['label' => 'Asignaciones', 'icon' => 'sidebar/icons8-hierarchy-100.png',   'href' => $admin_prefix . 'asignaciones.php'],
]);

$usuarios_nav = [];
if ($_SESSION['rol'] == 1) {
    $usuarios_nav['usuarios'] = ['label' => 'Usuarios', 'icon' => 'sidebar/icons8-usuario-masculino-en-círculo-100.png', 'href' => $admin_prefix . 'usuarios.php'];
}
$usuarios_nav['checadores'] = ['label' => 'Checadores', 'icon' => 'sidebar/icons8-documentos-de-identificación-comprobados-100.png', 'href' => $admin_prefix . 'checadores.php'];
$nav_categories['Usuarios'] = $usuarios_nav;

$nav_categories['Gestión'] = [
    'reportes'       => ['label' => 'Reportes',       'icon' => 'sidebar/icons8-document-100.png',        'href' => $admin_prefix . 'reportes.php'],
    'notificaciones' => ['label' => 'Notificaciones',  'icon' => 'sidebar/icons8-campana-100.png',  'href' => $admin_prefix . 'notificaciones.php'],
];
?>


<!-- ============================================================
     BARRA MÓVIL — idéntica al <nav> del index.php
     Solo visible en móvil (max-width: 768px)
     ============================================================ -->
<nav class="admin-mobile-nav" id="adminMobileNav">

    <!-- Franja principal: logo izquierda, acciones derecha -->
    <div class="admin-nav-inner">
        <a href="<?php echo $base_url; ?>pages/admin/dashboard.php" class="admin-nav-brand">
            <img src="<?php echo $base_url; ?>assets/images/logo.png" alt="GoWay">
            <span>GoWay</span>
        </a>

        <div class="admin-nav-actions">
            <button class="notification-bell admin-notif-btn" id="mobileNotifBtn" onclick="toggleNotifications()">
                <span class="material-icons">notifications_none</span>
            </button>
            <button class="admin-nav-burger" id="adminNavBurger" aria-label="Menú" onclick="toggleAdminNav()">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <!-- Dropdown (se abre/cierra como el nav-mobile del index) -->
    <div class="admin-nav-dropdown" id="adminNavDropdown">

        <?php foreach ($nav_categories as $category_name => $items): ?>
        <p class="admin-nav-cat-label"><?php echo $category_name; ?></p>
        <?php foreach ($items as $slug => $item): ?>
        <a href="<?php echo $item['href']; ?>"
           class="admin-nav-item<?php echo ($active_page === $slug) ? ' active' : ''; ?>">
            <?php if (isset($item['is_material']) && $item['is_material']): ?>
                <span class="material-icons admin-nav-item-icon"><?php echo $item['icon']; ?></span>
            <?php else: ?>
                <img src="<?php echo $base_url; ?>assets/images/icons/<?php echo $item['icon']; ?>"
                     alt="" class="admin-nav-item-img">
            <?php endif; ?>
            <?php echo $item['label']; ?>
        </a>
        <?php endforeach; ?>
        <?php endforeach; ?>

        <!-- Sección de usuario -->
        <div class="admin-nav-user-row">
            <?php if (!empty($_SESSION['foto'])): ?>
                <img src="<?php echo $base_url; ?>assets/images/profiles/<?php echo htmlspecialchars($_SESSION['foto']); ?>"
                     alt="Perfil" class="admin-nav-user-avatar">
            <?php else: ?>
                <img src="<?php echo $base_url; ?>assets/images/icons/administrador.png"
                     alt="Admin" class="admin-nav-user-avatar">
            <?php endif; ?>
            <div class="admin-nav-user-info">
                <span class="admin-nav-user-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <span class="admin-nav-user-role">Administrador</span>
            </div>
            <a href="<?php echo $logout_url; ?>" id="logoutMobile" class="admin-nav-logout">
                <span class="material-icons">logout</span>
            </a>
        </div>

    </div><!-- /.admin-nav-dropdown -->
</nav><!-- /.admin-mobile-nav -->


<!-- ============================================================
     SIDEBAR DESKTOP (no se toca en móvil)
     ============================================================ -->
<aside id="sidebar" class="sidebar">
    <script>
        if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth > 768) {
            document.getElementById('sidebar').classList.add('collapsed');
        }
    </script>

    <div class="logo">
        <img src="<?php echo $base_url; ?>assets/images/logo.png" alt="Logo de GoWay" class="logo-img">
        <h1>GoWay</h1>
        <button class="desktop-toggle-btn" onclick="toggleDesktopSidebar()">
            <img src="<?php echo $base_url; ?>assets/images/icons/sidebar/icons8-mostrar-panel-lateral-derecho-100.png" alt="Colapsar"
                 style="width:24px;height:24px;object-fit:contain;">
        </button>
    </div>

    <nav>
        <?php foreach ($nav_categories as $category_name => $items): ?>
        <?php $cat_id = 'cat_' . preg_replace('/[^a-zA-Z0-9]/', '', $category_name); ?>
        <div class="sidebar-category open" onclick="toggleCategory('<?php echo $cat_id; ?>', this)">
            <h4><?php echo $category_name; ?></h4>
            <span class="material-icons category-chevron">expand_more</span>
        </div>
        <ul id="<?php echo $cat_id; ?>" class="category-list open">
            <?php foreach ($items as $slug => $item): ?>
            <li>
                <a href="<?php echo $item['href']; ?>" title="<?php echo $item['label']; ?>"
                   <?php echo ($active_page === $slug) ? 'class="active"' : ''; ?>>
                    <?php if (isset($item['is_material']) && $item['is_material']): ?>
                        <span class="material-icons icon"
                              style="font-size:22px;line-height:20px;text-align:center;display:block;margin-right:10px;color:inherit;">
                            <?php echo $item['icon']; ?>
                        </span>
                    <?php else: ?>
                        <img src="<?php echo $base_url; ?>assets/images/icons/<?php echo $item['icon']; ?>"
                             alt="<?php echo $item['label']; ?>" class="icon">
                    <?php endif; ?>
                    <span><?php echo $item['label']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endforeach; ?>
    </nav>

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

    <!-- Créditos de íconos -->
    <style>
        .sidebar.collapsed .sidebar-credits-link {
            display: none !important;
        }
    </style>
    <div class="sidebar-credits-link" style="font-size: 11px; text-align: center; padding: 5px 15px 15px; margin-top: 0px; color: #888;">
        <a href="#" id="openCredits" style="color: inherit; text-decoration: underline;">Créditos de Íconos</a>
    </div>
</aside>

<?php require_once __DIR__ . '/credits_modal.php'; ?>


<script>
/* ── Desktop sidebar ────────────────────────────────── */
function toggleCategory(categoryId, headerElement) {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('collapsed')) return;
    const ul = document.getElementById(categoryId);
    ul.classList.toggle('open');
    headerElement.classList.toggle('open');
}

function toggleDesktopSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed') ? 'true' : 'false');
}

/* ── Menú móvil (estilo nav del index) ──────────────── */
function toggleAdminNav() {
    const nav      = document.getElementById('adminMobileNav');
    const dropdown = document.getElementById('adminNavDropdown');
    const burger   = document.getElementById('adminNavBurger');
    const isOpen   = dropdown.classList.contains('open');

    dropdown.classList.toggle('open');
    burger.classList.toggle('active');
    nav.classList.toggle('menu-open');
    document.body.style.overflow = isOpen ? '' : 'hidden';
}

// Mantener compatibilidad con referencias a toggleSidebar / closeSidebar
function toggleSidebar() { toggleAdminNav(); }
function closeSidebar() {
    const nav      = document.getElementById('adminMobileNav');
    const dropdown = document.getElementById('adminNavDropdown');
    const burger   = document.getElementById('adminNavBurger');
    dropdown.classList.remove('open');
    burger.classList.remove('active');
    nav.classList.remove('menu-open');
    document.body.style.overflow = '';
}

// Cerrar al clicar un enlace del dropdown
document.querySelectorAll('.admin-nav-item').forEach(link => {
    link.addEventListener('click', closeSidebar);
});

// Cerrar con ESC
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

// Cerrar al volver a desktop
window.addEventListener('resize', () => { if (window.innerWidth > 768) closeSidebar(); });

// Modal cerrar sesión
document.addEventListener('DOMContentLoaded', function () {
    function handleLogout(e) {
        e.preventDefault();
        const modal      = document.getElementById('logoutConfirmModal');
        const confirmBtn = document.getElementById('confirmLogoutBtn');
        if (modal && confirmBtn) {
            confirmBtn.setAttribute('href', this.getAttribute('href'));
            modal.classList.add('active');
        }
    }
    const lnk1 = document.getElementById('logout');
    const lnk2 = document.getElementById('logoutMobile');
    if (lnk1) lnk1.addEventListener('click', handleLogout);
    if (lnk2) lnk2.addEventListener('click', handleLogout);
});
</script>
