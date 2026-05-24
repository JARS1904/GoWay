<?php
session_start();

// Verificar si el usuario está logueado y tiene rol=2 o rol=3 (invitado)
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [2, 3])) {
    header("Location: ../login.php");
    exit();
}

$_user_foto = null;
if ($_SESSION['id'] > 0) {
    // Consultar foto fresca de la BD solo si no es invitado
    require_once '../../config/conexion_bd.php';
    $stmt = $conexion->prepare("SELECT foto FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $_user_foto = $stmt->get_result()->fetch_assoc()['foto'] ?? null;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWay - Rutas de Transporte</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <link rel="stylesheet" href="../../assets/css/route_selected_screen.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <img src="../../assets/images/logo_new.png" alt="GoWay Logo">
                <h1>GoWay</h1>
            </div>

            <!-- Nav links para desktop -->
            <nav class="header-nav">
                <a href="https://goway.netlify.app" target="_blank" class="download-btn">
                    <i class="fas fa-download"></i> Descargar App
                </a>
                <?php if ($_SESSION['rol'] == 3): ?>
                    <a href="../login.php" class="download-btn">
                        <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                    </a>
                <?php else: ?>
                    <button class="profile-nav-btn" onclick="openFavoritesPanel()">
                        <i class="fas fa-heart"></i>
                        Favoritos
                    </button>
                    <button class="profile-nav-btn" onclick="openReportModal()">
                        <i class="fas fa-exclamation-triangle"></i>
                        Reportes
                    </button>
                    <button class="profile-nav-btn" onclick="toggleNotifications()">
                        <i class="far fa-bell"></i>
                        Notificaciones
                    </button>
                    <button class="profile-nav-btn" onclick="openProfilePanel()">
                        <?php if (!empty($_user_foto)): ?>
                            <img src="../../assets/images/profiles/<?php echo htmlspecialchars($_user_foto); ?>" class="profile-nav-mini-avatar profile-nav-mini-img" alt="foto">
                        <?php else: ?>
                            <span class="profile-nav-mini-avatar"><?php echo htmlspecialchars(strtoupper(mb_substr($_SESSION['nombre'] ?? 'U', 0, 1))); ?></span>
                        <?php endif; ?>
                        Mi Perfil
                    </button>
                <?php endif; ?>
            </nav>

            <!-- Botón de descarga visible solo en móvil (el resto va a la bottom nav) -->
            <div class="mobile-header-actions">
                <a href="https://goway.netlify.app" target="_blank" class="download-btn" style="font-size:13px; padding:7px 14px;">
                    <i class="fas fa-download"></i> Descargar App
                </a>
                <?php if ($_SESSION['rol'] == 3): ?>
                    <a href="../login.php" class="download-btn" style="font-size:13px; padding:7px 14px;">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if ($_SESSION['rol'] != 3): ?>
    <!-- ── Barra de navegación inferior (solo móvil) ───────────────── -->
    <nav class="mobile-bottom-nav" id="mobileBottomNav">
        <button class="mob-nav-item" id="mbn-favorites" onclick="openFavoritesPanel(); setMobActive('mbn-favorites')">
            <i class="fas fa-heart"></i>
            <span>Favoritos</span>
        </button>
        <button class="mob-nav-item" id="mbn-reports" onclick="openReportModal(); setMobActive('mbn-reports')">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Reportes</span>
        </button>
        <button class="mob-nav-item" id="mbn-notif" onclick="toggleNotifications(); setMobActive('mbn-notif')">
            <i class="far fa-bell"></i>
            <span>Notificaciones</span>
        </button>
        <button class="mob-nav-item" id="mbn-profile" onclick="openProfilePanel(); setMobActive('mbn-profile')">
            <?php if (!empty($_user_foto)): ?>
                <img src="../../assets/images/profiles/<?php echo htmlspecialchars($_user_foto); ?>" class="mob-nav-avatar" alt="foto">
            <?php else: ?>
                <span class="mob-nav-avatar-letter"><?php echo htmlspecialchars(strtoupper(mb_substr($_SESSION['nombre'] ?? 'U', 0, 1))); ?></span>
            <?php endif; ?>
            <span>Mi Perfil</span>
        </button>
    </nav>
    <?php endif; ?>

    <div class="container">
        <!-- Columna izquierda - Búsqueda y resultados -->
        <div class="left-column">
            <section class="search-section">
                <p class="greeting">Hola, <span><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></span> &#128075;</p>
                <h2 class="search-title">¿A dónde quieres ir?</h2>
                <form id="searchForm" class="search-form">
                    <div class="form-group">
                        <label for="origin">Origen</label>
                        <div style="position:relative;">
                            <img src="../../assets/images/icons/icons8-marcador.png" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); width:22px; height:22px; pointer-events:none; z-index:1; filter:grayscale(1) brightness(0.55);">
                            <select id="origin" required style="padding-left:38px;">
                                <option value="">Seleccione el origen</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="destination">Destino</label>
                        <div style="position:relative;">
                            <img src="../../assets/images/icons/icons8-marcador.png" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); width:22px; height:22px; pointer-events:none; z-index:1; filter:grayscale(1) brightness(0.55);">
                            <select id="destination" required style="padding-left:38px;">
                                <option value="">Seleccione el destino</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" id="searchBtn" class="btn" disabled>
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </form>
            </section>

            <div class="divider"></div>

            <section class="results-section">
                <h2 class="section-title">Disponibles</h2>
                

                
                <div id="resultsContainer">
                    <div class="no-routes">
                        <p>Seleccione origen y destino para buscar rutas</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Columna derecha - Detalles de ruta -->
        <div class="right-column">
            <div id="routeDetailsContainer">
                <div class="no-selection">
                    <i class="fas fa-route"></i>
                    <h3>Selecciona una ruta</h3>
                    <p>Elige una ruta de la lista para ver los detalles completos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <i class="fas fa-exclamation-circle"></i>
        <span id="toastMessage"></span>
    </div>


    <script>
        // Configuración de API
        const API_BASE_URL = window.location.origin;
        const API_URL = `${API_BASE_URL}/GoWay/api/routes_api.php`;
        const FAVORITES_URL = `${API_BASE_URL}/GoWay/api/favorites_routes_api.php`;
        const ID_USUARIO = <?php echo isset($_SESSION['id']) ? $_SESSION['id'] : 0; ?>;
    </script>
    
    <!-- Lógica principal de la vista -->
    <script src="../../assets/js/route_selected_screen.js"></script>

    <?php 
    // Paneles modulares
    require_once '../../components/profile_panel.php';
    require_once '../../components/report_modal.php';
    
    $hide_send_notification = true;
    require_once '../../components/notifications_panel.php'; 
    
    require_once '../../components/favorites_panel.php';
    ?>

</body>
</html>
