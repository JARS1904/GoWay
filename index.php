<!--Se agreo para el manejo de sesi├│n-->
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: pages/login.php');
    exit();
}
require_once 'config/conexion_bd.php';
require_once 'config/sync_session_foto.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Transporte P├║blico</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Overlay para fondo oscuro -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <!-- Barra Superior M├│vil -->
        <div class="mobile-topbar">
            <div class="mobile-topbar-content">
                <div class="mobile-topbar-left">
                    <button class="toggle-btn" onclick="toggleSidebar()">Ôÿ░</button>
                    <h1 class="mobile-page-title">Dashboard</h1>
                </div>
                <div class="mobile-topbar-right">
                    <div class="mobile-user-info">
                        <button class="notification-bell" id="mobileNotifBtn" onclick="toggleNotifications()">
                            <span class="material-icons">notifications_none</span>
                        </button>
                        <span><?php echo $_SESSION['nombre']; ?></span>
                        <?php echo !empty($_SESSION['foto']) ? '<img src="assets/images/profiles/' . htmlspecialchars($_SESSION['foto']) . '" alt="Usuario" class="header-user-avatar">' : '<img src="assets/images/icons/administrador.png" alt="Usuario">'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Men├║ Lateral -->
        <aside id="sidebar" class="sidebar">
            <!-- Bot├│n de Cerrar para M├│vil -->
            <button class="sidebar-close" onclick="closeSidebar()">&times;</button>
            
            <div class="logo">
                <img src="assets/images/logo.png" alt="Logo de GoWay" class="logo-img">
                <h1>GoWay</h1>
            </div>
            <nav>
                <ul>
                    <li>
                        <a href="index.php">
                            <img src="assets/images/icons/icon_dashboard.png" alt="Dashboard" class="icon">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/empresas.php">
                            <img src="assets/images/icons/icon_empresas.png" alt="Empresas" class="icon">
                            <span>Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/conductores.php">
                            <img src="assets/images/icons/icon_conductores.png" alt="Conductores" class="icon">
                            <span>Conductores</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/vehiculos.php">
                            <img src="assets/images/icons/icon_vehiculos.png" alt="Veh├¡culos" class="icon">
                            <span>Vehículos</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/rutas.php">
                            <img src="assets/images/icons/icon_rutas.png" alt="Rutas" class="icon">
                            <span>Rutas</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/horarios.php">
                            <img src="assets/images/icons/icon_horarios.png" alt="Horarios" class="icon">
                            <span>Horarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/paradas_ruta.php">
                            <img src="assets/images/icons/icon_paradas.png" alt="Paradas" class="icon">
                            <span>Paradas</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/asignaciones.php">
                            <img src="assets/images/icons/icon_asignacion.png" alt="Asignaciones" class="icon">
                            <span>Asignaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/checadores.php">
                            <img src="assets/images/icons/icon_checadores.png" alt="Checadores" class="icon">
                            <span>Checadores</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/reportes.php">
                            <img src="assets/images/icons/icon_reportes.png" alt="Reportes" class="icon">
                            <span>Reportes</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/admin/usuarios.php">
                            <img src="assets/images/icons/icon_usuarios.png" alt="Usuarios" class="icon">
                            <span>Usuarios</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Bot├│n de Cerrar Sesi├│n -->
            <div class="logout-button">
                <a href="pages/login.php" id="logout">
                    <img src="assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesi├│n" class="icon">
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Dashboard</h2>
                <div class="user-info">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                    <span><?php echo $_SESSION['nombre']; ?></span>
                    <?php echo !empty($_SESSION['foto']) ? '<img src="assets/images/profiles/' . htmlspecialchars($_SESSION['foto']) . '" alt="Usuario" class="header-user-avatar">' : '<img src="assets/images/icons/administrador.png" alt="Usuario">'; ?>
                </div>
            </header>

            <section class="content">
                <!-- Secci├│n de Bienvenida -->
                <div class="dashboard-welcome">
                    <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>
                    <p>Aquí puedes ver un resumen del estado general de tu sistema de transporte</p>
                </div>

                <!-- Grid de Estad├¡sticas -->
                <div class="stats-grid">
                    <?php
                    $conn = $conexion;

                    if ($conn->connect_error) {
                        die("Error de conexi├│n: " . $conn->connect_error);
                    }

                    $sql = "SELECT 
                            (SELECT COUNT(*) FROM empresas) AS total_empresas,
                            (SELECT COUNT(*) FROM rutas) AS total_rutas,
                            (SELECT COUNT(*) FROM vehiculos) AS total_vehiculos,
                            (SELECT COUNT(*) FROM conductores) AS total_conductores,
                            (SELECT COUNT(*) FROM horarios) AS total_horarios,
                            (SELECT COUNT(*) FROM checadores) AS total_checadores";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    ?>

                    <!-- Tarjeta Empresas -->
                    <div class="stat-card">
                        <div class="stat-card-icon empresas">
                            <img src="assets/images/icons/icons8-empresa-dashboard-resumen.png" alt="Empresas">
                        </div>
                        <div class="stat-card-content">
                            <h3>Empresas</h3>
                            <p class="stat-number"><?php echo $row['total_empresas']; ?></p>
                            <span class="stat-label">Registradas</span>
                        </div>
                    </div>

                    <!-- Tarjeta Rutas -->
                    <div class="stat-card">
                        <div class="stat-card-icon rutas">
                            <img src="assets/images/icons/icons8-ruta-dashboard-resumen.png" alt="Rutas">
                        </div>
                        <div class="stat-card-content">
                            <h3>Rutas</h3>
                            <p class="stat-number"><?php echo $row['total_rutas']; ?></p>
                            <span class="stat-label">Activas</span>
                        </div>
                    </div>

                    <!-- Tarjeta Veh├¡culos -->
                    <div class="stat-card">
                        <div class="stat-card-icon vehiculos">
                            <img src="assets/images/icons/icons8-vehiculo-dashboard-resumen.png" alt="Veh├¡culos">
                        </div>
                        <div class="stat-card-content">
                            <h3>Vehículos</h3>
                            <p class="stat-number"><?php echo $row['total_vehiculos']; ?></p>
                            <span class="stat-label">En operación</span>
                        </div>
                    </div>

                    <!-- Tarjeta Conductores -->
                    <div class="stat-card">
                        <div class="stat-card-icon conductores">
                            <img src="assets/images/icons/icons8-conductor-dashboard-resumen.png" alt="Conductores">
                        </div>
                        <div class="stat-card-content">
                            <h3>Conductores</h3>
                            <p class="stat-number"><?php echo $row['total_conductores']; ?></p>
                            <span class="stat-label">Activos</span>
                        </div>
                    </div>

                    <!-- Tarjeta Horarios -->
                    <div class="stat-card">
                        <div class="stat-card-icon horarios">
                            <img src="assets/images/icons/icons8-horario-dashboard-resumen.png" alt="Horarios">
                        </div>
                        <div class="stat-card-content">
                            <h3>Horarios</h3>
                            <p class="stat-number"><?php echo $row['total_horarios']; ?></p>
                            <span class="stat-label">Configurados</span>
                        </div>
                    </div>

                    <!-- Tarjeta Checadores -->
                    <div class="stat-card">
                        <div class="stat-card-icon checadores">
                            <img src="assets/images/icons/icons8-checador-dashboard-resumen.png" alt="Checadores">
                        </div>
                        <div class="stat-card-content">
                            <h3>Checadores</h3>
                            <p class="stat-number"><?php echo $row['total_checadores']; ?></p>
                            <span class="stat-label">Registrados</span>
                        </div>
                    </div>
                </div>

                <!-- Secci├│n de Acciones R├ípidas -->
                <div class="quick-actions">
                    <h2>Acciones Rápidas</h2>
                    <div class="actions-grid">
                        <a href="pages/admin/rutas.php" class="action-btn">
                            <img class="action-icon" src="assets/images/icons/icons8-rutas-dashboard.png" alt="Rutas">
                            <span>Gestionar Rutas</span>
                        </a>
                        <a href="pages/admin/vehiculos.php" class="action-btn">
                            <img class="action-icon" src="assets/images/icons/icons8-vehiculos-dashboard.png" alt="Veh├¡culos">
                            <span>Gestionar Vehículos</span>
                        </a>
                        <a href="pages/admin/conductores.php" class="action-btn">
                            <img class="action-icon" src="assets/images/icons/icons8-conditores-dashboard.png" alt="Conductores">
                            <span>Gestionar Conductores</span>
                        </a>
                        <a href="pages/admin/horarios.php" class="action-btn">
                            <img class="action-icon" src="assets/images/icons/icons8-horario-dashboard.png" alt="Horarios">
                            <span>Gestionar Horarios</span>
                        </a>
                        <a href="pages/admin/checadores.php" class="action-btn">
                            <img class="action-icon" src="assets/images/icons/icons8-checadores-dashboard.png" alt="Checadores">
                            <span>Gestionar Checadores</span>
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Panel Lateral de Notificaciones -->
    <div class="notifications-panel" id="notificationsPanel">
        <div class="notifications-header">
            <h3>Centro de Notificaciones</h3>
            <button class="close-panel" onclick="toggleNotifications()">&times;</button>
        </div>
        <div class="notifications-actions">
            <button class="btn-add full-width" id="openAddNotificationModal" style="margin: 0; width: 100%;">+ Mandar Notificación</button>
        </div>
        <div class="notifications-body">
            <?php
            $sql_notif  = "SELECT n.*, u.nombre AS usuario_nombre 
                           FROM notificaciones n 
                           LEFT JOIN usuarios u ON n.id_usuario = u.id 
                           ORDER BY n.fecha_creacion DESC LIMIT 50";
            $result_notif = $conn->query($sql_notif);
            
            if ($result_notif && $result_notif->num_rows > 0) {
                while ($row_notif = $result_notif->fetch_assoc()) {
                    $target = ($row_notif['id_usuario'] === null) ? 'Todos los usuarios' : htmlspecialchars($row_notif['usuario_nombre']);
                    $titulo = htmlspecialchars($row_notif['titulo']);
                    $tipo = htmlspecialchars($row_notif['tipo']);
                    $fecha = date('d M Y, h:i a', strtotime($row_notif['fecha_creacion']));

                    $icon_svg = '';
                    $gradient = '';
                    $tipo_text = '';

                    switch ($tipo) {
                        case 'Alerta':
                            $gradient = 'linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%)';
                            $icon_svg = 'warning';
                            $tipo_text = 'Alerta de Seguridad';
                            break;
                        case 'Promocion':
                            $gradient = 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)';
                            $icon_svg = 'local_offer';
                            $tipo_text = 'Promoción Especial';
                            break;
                        case 'Cierre':
                            $gradient = 'linear-gradient(135deg, #00c6ff 0%, #0072ff 100%)';
                            $icon_svg = 'block';
                            $tipo_text = 'Cierre Vial';
                            break;
                        case 'General':
                        default:
                            $gradient = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
                            $icon_svg = 'notifications';
                            $tipo_text = 'Aviso General';
                            break;
                    }
                    ?>
                    <div class="notification-capsule">
                        <div class="notif-icon" style="background: <?php echo $gradient; ?>">
                            <span class="material-icons"><?php echo $icon_svg; ?></span>
                        </div>
                        <div class="notif-content">
                            <h4 class="notif-title"><?php echo $titulo; ?></h4>
                            <p class="notif-desc"><?php echo htmlspecialchars($row_notif['mensaje']); ?></p>
                            
                            <div class="notif-details" style="font-size: 13px; color: #475569; margin-bottom: 8px;">
                                <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 4px;">
                                    <span class="material-icons" style="font-size:14px;">group</span>
                                    <span><strong><?php echo $target; ?></strong></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <span class="material-icons" style="font-size:14px;">label</span>
                                    <span><?php echo $tipo_text; ?></span>
                                </div>
                            </div>

                            <div class="notif-meta" style="display: flex; justify-content: flex-end; width: 100%;">
                                <span class="notif-time" style="font-size: 11px; color: #94a3b8;"><?php echo $fecha; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="empty-notifs">
                        <span class="material-icons" style="font-size: 48px; color: #cbd5e1; margin-bottom: 10px;">notifications_off</span>
                        <p>No hay notificaciones recientes</p>
                      </div>';
            }
            ?>
        </div>
    </div>
    <div class="notifications-overlay" id="notificationsOverlay" onclick="toggleNotifications()"></div>

    <!-- Modal para agregar nueva notificación -->
    <div class="modal-overlay" id="addNotificationModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Enviar Notificación</h3>
                <button class="modal-close" id="closeAddNotifModal">&times;</button>
            </div>
            <form id="notificationForm" action="controllers/insert_notificacion.php" method="POST">
                <div class="modal-body">
                    <div>
                        <div class="modal-form-group">
                            <label>Destinatario (Usuario)</label>
                            <select name="id_usuario" required>
                                <option value="todos">Todos los usuarios (Global)</option>
                                <?php
                                $res_usuarios = $conn->query("SELECT id, nombre, email FROM usuarios ORDER BY nombre ASC");
                                if ($res_usuarios) {
                                    while ($u_row = $res_usuarios->fetch_assoc()) {
                                        echo "<option value='{$u_row['id']}'>" . htmlspecialchars($u_row['nombre']) . " ({$u_row['email']})</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Tipo de Mensaje</label>
                            <select name="tipo" required>
                                <option value="Alerta">Alerta de Seguridad</option>
                                <option value="Cierre">Cierre Vial/Tráfico</option>
                                <option value="Promocion">Promoción Especial</option>
                                <option value="General" selected>Aviso General</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="modal-form-group">
                            <label>Título de la Notificación</label>
                            <input type="text" name="titulo" placeholder="Ej. Accidente en el centro" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Mensaje / Detalles</label>
                            <textarea name="mensaje" rows="4" style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc" placeholder="Escribe aquí las instrucciones para los usuarios..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelAddNotifModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Enviar Notificación ¡Ahora!</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para agregar nueva Empresa -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nueva empresa</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="./controllers/insert_empresa.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label for="nombre">RFC de la Empresa</label>
                            <input type="text" id="rfc_empresa" name="rfc_empresa" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="destino">Nombre de Empresa</label>
                            <input type="text" id="nombre_empresa" name="nombre_empresa" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="destino">Direccion de Empresa</label>
                            <input type="text" id="direccion_empresa" name="direccion_empresa" placeholder="" required>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label for="origen">Telefono</label>
                            <input type="text" id="tel_empresa" name="tel_empresa" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="paradas">E-mail</label>
                            <input type="email" id="email_empresa" name="email_empresa" placeholder=""></input>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funciones para el men├║ hamburguesa
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.querySelector('.toggle-btn');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Ocultar/mostrar bot├│n hamburguesa
            if (sidebar.classList.contains('active')) {
                toggleBtn.style.opacity = '0';
                toggleBtn.style.visibility = 'hidden';
            } else {
                toggleBtn.style.opacity = '1';
                toggleBtn.style.visibility = 'visible';
            }
            
            // Prevenir scroll del body cuando el men├║ est├í abierto
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.querySelector('.toggle-btn');
            
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            
            // Mostrar bot├│n hamburguesa al cerrar
            toggleBtn.style.opacity = '1';
            toggleBtn.style.visibility = 'visible';
            
            document.body.style.overflow = '';
        }

        // Cerrar sidebar al hacer clic en un enlace (en m├│vil)
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
    </script>

    <script src="assets/js/notifications.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Lógica del Panel de Notificaciones
        function toggleNotifications() {
            const panel = document.getElementById('notificationsPanel');
            const overlay = document.getElementById('notificationsOverlay');
            panel.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Ocultar main sidebar si está abierta en móvil
            if (window.innerWidth <= 768 && document.getElementById('sidebar').classList.contains('active')) {
                closeSidebar();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Abrir Modal de Notificación
            const btnOpenNotif = document.getElementById('openAddNotificationModal');
            if (btnOpenNotif) {
                btnOpenNotif.addEventListener('click', function() {
                    toggleNotifications(); // cerrar panel
                    document.getElementById('addNotificationModal').classList.add('active');
                });
            }

            // Cerrar Modal
            const closeModalNotifFn = () => document.getElementById('addNotificationModal').classList.remove('active');
            document.getElementById('closeAddNotifModal')?.addEventListener('click', closeModalNotifFn);
            document.getElementById('cancelAddNotifModal')?.addEventListener('click', closeModalNotifFn);

            // Handler para el formulario usando notifications.js
            const notifForm = document.getElementById('notificationForm');
            if (notifForm && typeof handleInsertForm === 'function') {
                handleInsertForm(notifForm, '¡Notificación enviada correctamente a los usuarios!');
            }
        });
    </script>
</body>
</html>
