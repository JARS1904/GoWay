<?php
// Calcular ruta base dinámicamente según la profundidad del archivo
$notif_base = '';
if (strpos($_SERVER['SCRIPT_NAME'], '/actualizar/') !== false) {
    $notif_base = '../../../';
} elseif (strpos($_SERVER['SCRIPT_NAME'], '/pages/') !== false || strpos($_SERVER['SCRIPT_NAME'], '\pages\\') !== false) {
    $notif_base = '../../';
}

if (!isset($conn)) {
    global $conexion;
    if (isset($conexion)) {
        $conn = $conexion;
    }
}

$is_admin   = (isset($_SESSION['rol']) && ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2));
$is_empresa = (isset($_SESSION['rol']) && $_SESSION['rol'] == 4 && !empty($_SESSION['rfc_empresa']));
$can_send   = ($is_admin || $is_empresa) && empty($hide_send_notification);
?>
<style>
/* Botones de filtro del panel de notificaciones */
.notif-filters button.notif-chip {
    width: auto !important;
    border: none !important;
    padding: 8px 16px !important;
}
</style>
<!-- Panel Lateral de Notificaciones -->
<div class="notifications-panel" id="notificationsPanel">
    <div class="notifications-header">
        <h3>Centro de notificaciones</h3>
        <button class="modal-close" onclick="toggleNotifications()">&times;</button>
    </div>
    
    <!-- Buscador -->
    <div class="notif-search-container">
        <div class="notif-search-box">
            <span class="material-icons">search</span>
            <input type="text" id="notifSearchInput" placeholder="Buscar por título..." onkeyup="filterNotifications()">
        </div>
    </div>

    <!-- Filtros Categoría -->
    <div class="notif-filters">
        <button class="notif-chip active" onclick="filterByChip(this, 'all')">Todos</button>
        <button class="notif-chip" onclick="filterByChip(this, 'alerta')">Alertas de Seguridad</button>
        <button class="notif-chip" onclick="filterByChip(this, 'cierre')">Cierre Vial</button>
        <button class="notif-chip" onclick="filterByChip(this, 'trafico')">Tráfico Pesado</button>
        <button class="notif-chip" onclick="filterByChip(this, 'general')">Aviso General</button>
    </div>

    <?php if ($can_send): ?>
    <div class="notifications-actions">
        <button class="btn-add full-width" id="openAddNotificationModal" style="margin: 0; width: 100%;">+ Enviar notificación</button>
    </div>
    <?php endif; ?>
    
    <div class="notifications-body" id="notifListBody">
        <?php
        if (isset($conn) && $conn) {
            if ($is_empresa) {
                // Empresa: solo ve su historial de enviadas
                $stmt_panel = $conn->prepare(
                    "SELECT n.*, NULL AS usuario_nombre FROM notificaciones n
                     WHERE n.rfc_empresa = ?
                     ORDER BY n.fecha_creacion DESC LIMIT 50"
                );
                $stmt_panel->bind_param("s", $_SESSION['rfc_empresa']);
                $stmt_panel->execute();
                $result_notif = $stmt_panel->get_result();
            } else {
                $sql_notif = "SELECT n.*, u.nombre AS usuario_nombre
                              FROM notificaciones n
                              LEFT JOIN usuarios u ON n.id_usuario = u.id ";
                if (!$is_admin) {
                    if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
                        $user_id = (int)$_SESSION['id'];
                        $sql_notif .= " WHERE n.id_usuario = $user_id 
                                        OR (n.id_usuario IS NULL AND n.rfc_empresa IS NULL)
                                        OR (n.id_usuario IS NULL AND n.rfc_empresa IN (
                                            SELECT r.rfc_empresa FROM rutas r 
                                            INNER JOIN favoritos f ON r.id = f.id_ruta 
                                            WHERE f.id_usuario = $user_id
                                        )) ";
                    } else {
                        $sql_notif .= " WHERE n.id_usuario IS NULL AND n.rfc_empresa IS NULL ";
                    }
                }
                $sql_notif .= " ORDER BY n.fecha_creacion DESC LIMIT 50";
                $result_notif = $conn->query($sql_notif);
            }
            
            if ($result_notif && $result_notif->num_rows > 0) {
                date_default_timezone_set('America/Mexico_City');
                $current_date_group = '';
                $hoy_str = date('Y-m-d');
                $ayer_str = date('Y-m-d', strtotime('-1 day'));

                while ($row_notif = $result_notif->fetch_assoc()) {
                    if ($row_notif['destinatario_tipo'] === 'checadores') {
                        $target = ($row_notif['rfc_empresa'] !== null) ? 'Checadores de la empresa' : 'Todos los checadores';
                    } else {
                        if ($row_notif['rfc_empresa'] !== null) {
                            $target = 'Suscriptores de la empresa';
                        } else {
                            $target = ($row_notif['id_usuario'] === null) ? 'Todos los usuarios' : htmlspecialchars($row_notif['usuario_nombre']);
                        }
                    }
                    $titulo = htmlspecialchars($row_notif['titulo']);
                    $tipo = htmlspecialchars($row_notif['tipo']);
                    
                    // Lógica para separar por fechas
                    $fecha_db_str = date('Y-m-d', strtotime($row_notif['fecha_creacion']));
                    if ($fecha_db_str == $hoy_str) {
                        $date_label = 'Hoy';
                    } elseif ($fecha_db_str == $ayer_str) {
                        $date_label = 'Ayer';
                    } else {
                        $date_label = date('d M', strtotime($row_notif['fecha_creacion']));
                    }

                    if ($current_date_group !== $date_label) {
                        echo '<div class="notif-date-header" data-dategroup="1">' . $date_label . '</div>';
                        $current_date_group = $date_label;
                    }

                    $hora_str = date('d M Y, h:i a', strtotime($row_notif['fecha_creacion']));

                    $icon_svg = '';
                    $icon_bg_class = '';
                    $tipo_text = '';

                    switch ($tipo) {
                        case 'Alerta':
                            $icon_bg_class = 'bg-red';
                            $icon_svg = 'warning';
                            $tipo_text = 'Alerta';
                            break;
                        case 'Cierre':
                            $icon_bg_class = 'bg-blue';
                            $icon_svg = 'block';
                            $tipo_text = 'Cierre Vial';
                            break;
                        case 'Trafico':
                            $icon_bg_class = 'bg-orange';
                            $icon_svg = 'directions_car';
                            $tipo_text = 'Tráfico Pesado';
                            break;
                        case 'General':
                        default:
                            $icon_bg_class = '';
                            $icon_svg = 'notifications_none';
                            $tipo_text = 'Aviso General';
                            break;
                    }
                    ?>
                    <div class="notif-item" data-type="<?php echo strtolower($tipo); ?>" data-title="<?php echo strtolower($titulo); ?>" onclick="this.classList.toggle('expanded')">
                        <div class="notif-icon-col">
                            <div class="notif-icon-circle <?php echo $icon_bg_class; ?>">
                                <span class="material-icons"><?php echo $icon_svg; ?></span>
                            </div>
                        </div>
                        <div class="notif-item-content">
                            <div class="notif-item-top">
                                <span class="notif-time"><?php echo $hora_str . ' &bull; ' . $tipo_text; ?></span>
                            </div>
                            <div class="notif-title-row">
                                <div class="notif-dot"></div>
                                <h4 class="notif-title"><?php echo $titulo; ?></h4>
                            </div>
                            <p class="notif-desc"><?php echo htmlspecialchars($row_notif['mensaje']); ?></p>
                            <?php if ($is_admin): ?>
                            <p class="notif-desc" style="font-size:0.75rem; color:#9ca3af; margin:0;">Enviado a: <strong><?php echo $target; ?></strong></p>
                            <?php endif; ?>
                        </div>
                        <div class="notif-item-chevron">
                            <span class="material-icons">chevron_right</span>
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
        }
        ?>
    </div>
</div>

<script>
function filterNotifications() {
    const searchVal = document.getElementById('notifSearchInput').value.toLowerCase();
    const activeChip = document.querySelector('.notif-chip.active');
    const onclickText = activeChip ? activeChip.getAttribute('onclick') : '';
    let typeFilter = 'all';
    if (onclickText.includes('alerta')) typeFilter = 'alerta';
    else if (onclickText.includes('cierre')) typeFilter = 'cierre';
    else if (onclickText.includes('trafico')) typeFilter = 'trafico';
    else if (onclickText.includes('general')) typeFilter = 'general';
    
    applyFilters(searchVal, typeFilter);
}

function filterByChip(btnElem, type) {
    document.querySelectorAll('.notif-chip').forEach(btn => btn.classList.remove('active'));
    btnElem.classList.add('active');
    const searchVal = document.getElementById('notifSearchInput').value.toLowerCase();
    applyFilters(searchVal, type);
}

function applyFilters(search, type) {
    const listBody = document.getElementById('notifListBody');
    if (!listBody) return;
    
    let pendingHeader = null;
    let hasVisibleSinceHeader = false;
    
    Array.from(listBody.children).forEach(child => {
        if (child.classList.contains('notif-date-header')) {
            if (pendingHeader) {
                pendingHeader.style.display = hasVisibleSinceHeader ? 'block' : 'none';
            }
            pendingHeader = child;
            hasVisibleSinceHeader = false;
        } else if (child.classList.contains('notif-item')) {
            const itemType = child.getAttribute('data-type') || '';
            const itemTitle = child.getAttribute('data-title') || '';
            
            let matchesSearch = search === '' || itemTitle.includes(search);
            let matchesType = (type === 'all') || (itemType.toLowerCase().includes(type));
            
            if (matchesSearch && matchesType) {
                child.style.display = 'flex';
                hasVisibleSinceHeader = true;
            } else {
                child.style.display = 'none';
            }
        }
    });
    
    if (pendingHeader) {
        pendingHeader.style.display = hasVisibleSinceHeader ? 'block' : 'none';
    }
}
</script>
<div class="notifications-overlay" id="notificationsOverlay" onclick="toggleNotifications()"></div>

<!-- Modal para agregar nueva notificación -->
<?php if ($can_send): ?>
<div class="modal-overlay" id="addNotificationModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Enviar Notificación</h3>
            <button class="modal-close" id="closeAddNotifModal">&times;</button>
        </div>
        <form id="notificationForm" action="<?php echo $notif_base; ?>controllers/insert_notificacion.php" method="POST">
            <div class="modal-body">
                <div>
                    <div class="modal-form-group" <?php echo $is_empresa ? 'style="display:none"' : ''; ?>>
                        <label>Destinatario</label>
                        <select name="id_usuario">
                            <option value="todos">Todos los usuarios (Global)</option>
                            <option value="todos_checadores">Todos los checadores (Global)</option>
                            <optgroup label="Usuarios específicos">
                            <?php
                            if (isset($conn) && !$is_empresa) {
                                $res_usuarios = $conn->query("SELECT id, nombre, email FROM usuarios ORDER BY nombre ASC");
                                if ($res_usuarios) {
                                    while ($u_row = $res_usuarios->fetch_assoc()) {
                                        echo "<option value='{$u_row['id']}'>" . htmlspecialchars($u_row['nombre']) . " ({$u_row['email']})</option>";
                                    }
                                }
                            }
                            ?>
                            </optgroup>
                        </select>
                    </div>
                    <?php if ($is_empresa): ?>
                    <div class="modal-form-group">
                        <label>Destinatario</label>
                        <select name="destinatario_empresa" required>
                            <option value="favoritos">Usuarios con rutas en favoritos</option>
                            <option value="checadores">Checadores de la empresa</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="modal-form-group">
                        <label>Tipo de Mensaje</label>
                        <select name="tipo" required>
                            <option value="Alerta">Alerta de Seguridad</option>
                            <option value="Cierre">Cierre Vial</option>
                            <option value="Trafico">Tráfico Pesado</option>
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
<?php endif; ?>

<script>
    // Lógica del Panel de Notificaciones
    function toggleNotifications() {
        const panel = document.getElementById('notificationsPanel');
        const overlay = document.getElementById('notificationsOverlay');
        if (panel) panel.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
        
        // Ocultar main sidebar si está abierta en móvil
        const sidebar = document.getElementById('sidebar');
        if (sidebar && window.innerWidth <= 768 && sidebar.classList.contains('active') && typeof closeSidebar === 'function') {
            closeSidebar();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Abrir Modal de Notificación
        const btnOpenNotif = document.getElementById('openAddNotificationModal');
        if (btnOpenNotif) {
            btnOpenNotif.addEventListener('click', function() {
                toggleNotifications(); // cerrar panel
                const modal = document.getElementById('addNotificationModal');
                if (modal) modal.classList.add('active');
            });
        }

        // Cerrar Modal
        const closeModalNotifFn = () => {
            const modal = document.getElementById('addNotificationModal');
            if (modal) modal.classList.remove('active');
        }
        const btnClose = document.getElementById('closeAddNotifModal');
        if (btnClose) btnClose.addEventListener('click', closeModalNotifFn);
        const btnCancel = document.getElementById('cancelAddNotifModal');
        if (btnCancel) btnCancel.addEventListener('click', closeModalNotifFn);

        // Handler para el formulario usando notifications.js
        const notifForm = document.getElementById('notificationForm');
        if (notifForm && typeof handleInsertForm === 'function') {
            handleInsertForm(notifForm, '¡Notificación enviada correctamente a los usuarios!');
        }
    });
</script>

