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
                    <h3>Historial de notificaciones generadas</h3>
                    <button class="btn-add" id="openAddModal">+ Enviar notificación</button>
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
                        $is_empresa = isset($_SESSION['rol']) && $_SESSION['rol'] == 4 && !empty($_SESSION['rfc_empresa']);

                        if ($is_empresa) {
                            // Empresa: solo ve sus propias notificaciones enviadas
                            $stmt_n = $conn->prepare(
                                "SELECT n.*, NULL AS usuario_nombre FROM notificaciones n
                                 WHERE n.rfc_empresa = ?
                                 ORDER BY n.fecha_creacion DESC"
                            );
                            $stmt_n->bind_param("s", $_SESSION['rfc_empresa']);
                        } else {
                            // Super Admin: ve todo
                            $stmt_n = $conn->prepare(
                                "SELECT n.*, u.nombre AS usuario_nombre
                                 FROM notificaciones n
                                 LEFT JOIN usuarios u ON n.id_usuario = u.id
                                 ORDER BY n.fecha_creacion DESC"
                            );
                        }
                        $stmt_n->execute();
                        $result = $stmt_n->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Determinar destinatario
                                if ($row['destinatario_tipo'] === 'checadores') {
                                    if ($row['rfc_empresa'] !== null) {
                                        $target = '<span style="color:#FF6D00;font-weight:600">Checadores de la empresa</span>';
                                    } else {
                                        $target = '<strong>Todos los checadores</strong>';
                                    }
                                } else {
                                    if ($row['rfc_empresa'] !== null) {
                                        $target = '<span style="color:#2962FF;font-weight:600">Suscriptores de la empresa</span>';
                                    } elseif ($row['id_usuario'] === null) {
                                        $target = '<strong>Todos los usuarios</strong>';
                                    } else {
                                        $target = htmlspecialchars($row['usuario_nombre'] ?? 'Usuario #'.$row['id_usuario']);
                                    }
                                }
                                $titulo = htmlspecialchars($row['titulo']);
                                $tipo = htmlspecialchars($row['tipo']);
                                $fecha = htmlspecialchars($row['fecha_creacion']);

                                $icon_svg = '';
                                $icon_bg_class = '';
                                $tipo_text = '';

                                switch ($tipo) {
                                    case 'Alerta':
                                        $icon_bg_class = 'bg-red';
                                        $icon_svg = '<span class="material-icons" style="font-size: 18px;">warning</span>';
                                        $tipo_text = 'Alerta de Seguridad';
                                        break;
                                    case 'Cierre':
                                        $icon_bg_class = 'bg-blue';
                                        $icon_svg = '<span class="material-icons" style="font-size: 18px;">block</span>';
                                        $tipo_text = 'Cierre Vial';
                                        break;
                                    case 'Trafico':
                                        $icon_bg_class = 'bg-orange';
                                        $icon_svg = '<span class="material-icons" style="font-size: 18px;">directions_car</span>';
                                        $tipo_text = 'Tráfico Pesado';
                                        break;
                                    case 'General':
                                    default:
                                        $icon_bg_class = ''; // default gray bg
                                        $icon_svg = '<span class="material-icons" style="font-size: 18px;">notifications_none</span>';
                                        $tipo_text = 'Aviso General';
                                        break;
                                }

                                echo "
                                <tr>
                                    <td>
                                        <div style='display: flex; align-items: center; gap: 10px;'>
                                            <div class='notif-icon-circle {$icon_bg_class}' style='width: 32px; height: 32px;'>
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

                <!-- Paginación -->
                <div class="pagination">
                    <button class="pagination-btn" id="prevPage" disabled>‹ Anterior</button>
                    <div class="pagination-info" id="pageInfo">Página 1 de 1</div>
                    <button class="pagination-btn" id="nextPage">Siguiente ›</button>
                </div>
            </section>
        </main>
    </div>


    <script src="../../assets/js/notifications.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/pagination.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Abrir Modal
        const openBtn = document.getElementById('openAddModal');
        if (openBtn) {
            openBtn.addEventListener('click', function() {
                const modal = document.getElementById('addNotificationModal');
                if (modal) {
                    modal.classList.add('active');
                } else {
                    console.error('El modal de notificaciones no existe en el DOM.');
                }
            });
        }
        // Envío AJAX y cierre manejado globalmente por notifications_panel.php
    });
    </script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
</body>
</html>
