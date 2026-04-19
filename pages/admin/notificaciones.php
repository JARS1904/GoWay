<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../../config/conexion_bd.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - Administrador</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php
        $page_title  = 'Centro de Notificaciones';
        $active_page = 'notificaciones';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <header class="header">
                <h2>Centro de Notificaciones</h2>
                                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
                <div class="section-header">
                    <h3>Historial de Notificaciones Generadas</h3>
                    <button class="btn-add" id="openAddModal">+ Mandar Notificación</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tipo de Notificación</th>
                            <th>Título</th>
                            <th>A quién</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = $conexion;
                        $sql  = "SELECT n.*, u.nombre AS usuario_nombre 
                                 FROM notificaciones n 
                                 LEFT JOIN usuarios u ON n.id_usuario = u.id 
                                 ORDER BY n.fecha_creacion DESC LIMIT 50";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $target = ($row['id_usuario'] === null) ? '<strong>Todos los usuarios</strong>' : htmlspecialchars($row['usuario_nombre']);
                                $titulo = htmlspecialchars($row['titulo']);
                                $tipo = htmlspecialchars($row['tipo']);
                                $fecha = htmlspecialchars($row['fecha_creacion']);

                                $icon_svg = '';
                                $gradient = '';
                                $tipo_text = '';

                                switch ($tipo) {
                                    case 'Alerta':
                                        $gradient = 'linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%)';
                                        $icon_svg = '<span class="material-icons" style="color: white; font-size: 18px;">warning</span>';
                                        $tipo_text = 'Aviso de Seguridad';
                                        break;
                                    case 'Promocion':
                                        $gradient = 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)';
                                        $icon_svg = '<span class="material-icons" style="color: white; font-size: 18px;">local_offer</span>';
                                        $tipo_text = 'Promoción';
                                        break;
                                    case 'Cierre':
                                        $gradient = 'linear-gradient(135deg, #00c6ff 0%, #0072ff 100%)';
                                        $icon_svg = '<span class="material-icons" style="color: white; font-size: 18px;">notifications</span>';
                                        $tipo_text = 'Cierre Vial';
                                        break;
                                    case 'General':
                                    default:
                                        $gradient = 'linear-gradient(135deg, #00c6ff 0%, #0072ff 100%)';
                                        $icon_svg = '<span class="material-icons" style="color: white; font-size: 18px;">error</span>';
                                        $tipo_text = 'Aviso General';
                                        break;
                                }

                                echo "
                                <tr>
                                    <td>
                                        <div style='display: flex; align-items: center; gap: 10px;'>
                                            <div style='width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: {$gradient}; flex-shrink: 0;'>
                                                {$icon_svg}
                                            </div>
                                            <span><strong>{$tipo_text}</strong></span>
                                        </div>
                                    </td>
                                    <td>{$titulo}</td>
                                    <td>{$target}</td>
                                    <td>{$fecha}</td>
                                </tr>";
                            }
                        } else {
                            echo '<tr><td colspan="4">No hay notificaciones registradas</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Modal para agregar nueva notificación -->
    <div class="modal-overlay" id="addNotificationModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Enviar Notificación</h3>
                <button class="modal-close" id="closeAddModal">&times;</button>
            </div>
            <form id="notificationForm" action="../../controllers/insert_notificacion.php" method="POST">
                <div class="modal-body">
                    <div>
                        <div class="modal-form-group">
                            <label>Destinatario (Usuario)</label>
                            <select name="id_usuario" required>
                                <option value="todos">Todos los usuarios (Global)</option>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT id, nombre, email FROM usuarios ORDER BY nombre ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['id']}'>" . htmlspecialchars($row['nombre']) . " ({$row['email']})</option>";
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
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelAddModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Enviar Notificación ¡Ahora!</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/notifications.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Abrir Modal
        document.getElementById('openAddModal').addEventListener('click', function() {
            document.getElementById('addNotificationModal').classList.add('active');
        });

        // Cerrar Modal
        const closeModalFn = () => document.getElementById('addNotificationModal').classList.remove('active');
        document.getElementById('closeAddModal').addEventListener('click', closeModalFn);
        document.getElementById('cancelAddModal').addEventListener('click', closeModalFn);

        // Envío AJAX usando handleInsertForm de notifications.js
        handleInsertForm(document.getElementById('notificationForm'), '¡Notificación enviada correctamente a los usuarios!');
    });
    </script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
</body>
</html>
