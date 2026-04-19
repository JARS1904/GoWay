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
    <title>Dashboard - Transporte Público</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        $page_title  = 'Dashboard';
        $active_page = 'dashboard';
        $base_url    = '';
        require_once __DIR__ . '/components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Dashboard</h2>
                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
                <!-- Secci├│n de Bienvenida -->
                <div class="dashboard-welcome">
                    <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?> 👋</h1>
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

    <!-- Integración del Panel Lateral de Notificaciones Compartido -->
    <?php require_once __DIR__ . '/components/notifications_panel.php'; ?>

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
            
            // Ocultar/mostrar botón hamburguesa con la X
            if (sidebar.classList.contains('active')) {
                toggleBtn.innerHTML = '&times;';
                toggleBtn.style.fontSize = '36px';
            } else {
                toggleBtn.innerHTML = '&#9776;';
                toggleBtn.style.fontSize = '';
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

    <?php require_once __DIR__ . '/components/logout_modal.php'; ?>
</body>
</html>