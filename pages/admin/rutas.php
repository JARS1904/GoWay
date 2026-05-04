<!--Se agreo para el manejo de sesión-->
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../../config/conexion_bd.php';
require_once '../../config/sync_session_foto.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rutas - Transporte Público</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        $page_title  = 'Gestión de Rutas';
        $active_page = 'rutas';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Rutas</h2>
                                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
                <div class="section-header">
                    <h3>Lista de Rutas</h3>
                    <button class="btn-add">+ Agregar nueva ruta</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Paradas registradas</th>
                            <th>Ruta de retorno</th>
                            <th>Activa</th>
                            <th>RFC de la Empresa</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = $conexion;

                        // Consulta para obtener las rutas con su par de retorno y conteo de paradas
                        $sql = "SELECT r.*,
                                       ret.nombre AS nombre_retorno,
                                       (SELECT COUNT(*) FROM paradas_ruta pr WHERE pr.id_ruta = r.id_ruta) AS total_paradas
                                FROM rutas r
                                LEFT JOIN rutas ret ON r.id_ruta_retorno = ret.id_ruta";
                        if ($_SESSION['rol'] == 4) {
                            $rfc_empresa_session = $_SESSION['rfc_empresa'];
                            $sql .= " WHERE r.rfc_empresa = '$rfc_empresa_session'";
                        }
                        $sql .= " ORDER BY r.id_ruta";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusClass = $row["activa"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activa"] ? 'Sí' : 'No';

                                // Badge de retorno
                                if ($row['id_ruta_retorno']) {
                                    $nombreRetorno = htmlspecialchars($row['nombre_retorno']);
                                    // Cambiar formato: "A - B" a "A ⇄ B"
                                    if (strpos($nombreRetorno, ' - ') !== false) {
                                        $nombreRetornoFormatted = str_replace(' - ', ' ⇄ ', $nombreRetorno);
                                        $retornoBadge = '<span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;white-space:normal;line-height:1.2;">' . $nombreRetornoFormatted . '</span>';
                                    } else {
                                        // Si no tiene el guión, lo dejamos como ⇄ Nombre
                                        $retornoBadge = '<span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;white-space:normal;line-height:1.2;">⇄ ' . $nombreRetorno . '</span>';
                                    }
                                } else {
                                    $retornoBadge = '<span style="color:#94a3b8;font-size:12px;">— Sin par</span>';
                                }
                                
                                echo '<tr>
                                        <td data-label="Nombre" data-id="'.$row["id_ruta"].'">'.$row["nombre"].'</td>
                                        <td data-label="Origen">' . htmlspecialchars($row["origen"]) . '</td>
                                        <td data-label="Destino">' . htmlspecialchars($row["destino"]) . '</td>
                                        <td data-label="Paradas">' . ($row['total_paradas'] > 0
                                            ? '<a href="paradas_ruta.php" style="display:inline-flex;align-items:center;gap:5px;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:3px 11px;font-size:12px;font-weight:600;text-decoration:none;">' . $row['total_paradas'] . ' paradas</a>'
                                            : '<a href="paradas_ruta.php" style="display:inline-flex;align-items:center;gap:5px;background:#fee2e2;color:#b91c1c;border-radius:12px;padding:3px 11px;font-size:12px;font-weight:600;text-decoration:none;">Sin paradas</a>') . '</td>
                                        <td data-label="Ruta de retorno" data-id-retorno="' . ($row['id_ruta_retorno'] ? $row['id_ruta_retorno'] : '') . '">' . $retornoBadge . '</td>
                                        <td data-label="Activa"><span class="'.$statusClass.'">' . $statusText . '</span></td>
                                        <td data-label="RFC de la Empresa">' . $row["rfc_empresa"] . '</td>
                                        <td>
                                            <div class="kebab-menu">
                                                <button class="kebab-btn" onclick="toggleKebabMenu(this, event)">
                                                    <span class="material-icons">more_vert</span>
                                                </button>
                                                <div class="dropdown-content">
                                                    <button class="dropdown-item btn-edit">
                                                        <span class="material-icons">edit_square</span> Editar
                                                    </button>
                                                    <button class="dropdown-item btn-delete">
                                                        <span class="material-icons">delete_outline</span> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8">No hay rutas registradas</td></tr>';
                        }

                        ?>
                    </tbody>
                </table>
                <!-- Paginación -->
                <div class="pagination">
                    <button class="pagination-btn" id="prevPage" disabled>‹ Anterior</button>
                    <div class="pagination-info" id="pageInfo">Página 1 de 5</div>
                    <button class="pagination-btn" id="nextPage">Siguiente ›</button>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para agregar nueva ruta -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nueva ruta</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../../controllers/insertar_ruta.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Empresa</label>
                            <?php if ($_SESSION['rol'] == 1): ?>
                            <select id="" name="rfc_empresa" required>
                                <option value="" disabled selected>Seleccione Empresa</option>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                            <?php else: ?>
                            <input type="text" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" readonly style="background-color: #f3f4f6; cursor: not-allowed;">
                            <input type="hidden" name="rfc_empresa" value="<?php echo $_SESSION['rfc_empresa']; ?>">
                            <?php endif; ?>
                        </div>

                        <div class="modal-form-group">
                            <label for="nombre">Nombre de la ruta</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej: Ruta 3" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="destino">Destino</label>
                            <input type="text" id="destino" name="destino" placeholder="Ej: Centro" required>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label for="origen">Origen</label>
                            <input type="text" id="origen" name="origen" placeholder="Ej: Av. Principal" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="id_ruta_retorno">Ruta de retorno (Opcional)</label>
                            <select id="id_ruta_retorno" name="id_ruta_retorno">
                                <option value="">-- Sin ruta de retorno --</option>
                                <?php
                                $result_rutas = $conexion->query("SELECT id_ruta, nombre FROM rutas ORDER BY nombre");
                                while ($row_ruta = $result_rutas->fetch_assoc()) {
                                    echo "<option value='{$row_ruta['id_ruta']}'>{$row_ruta['nombre']}</option>";
                                }
                                ?>
                            </select>
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

    <!-- Modal para edición -->
    <div class="modal-overlay" id="editRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar ruta</h3>
                <button class="modal-close" id="closeEditModal">&times;</button>
            </div>
            <form id="editRouteForm" action="../../controllers/update/actualizar_ruta.php" method="POST">
                <input type="hidden" id="edit_id_ruta" name="id_ruta">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Empresa</label>
                            <?php if ($_SESSION['rol'] == 1): ?>
                            <select id="edit_rfc_empresa" name="rfc_empresa" required>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                            <?php else: ?>
                            <input type="text" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" readonly style="background-color: #f3f4f6; cursor: not-allowed;">
                            <input type="hidden" id="edit_rfc_empresa" name="rfc_empresa" value="<?php echo $_SESSION['rfc_empresa']; ?>">
                            <?php endif; ?>
                        </div>

                        <div class="modal-form-group">
                            <label for="edit_nombre">Nombre de la ruta</label>
                            <input type="text" id="edit_nombre" name="nombre" placeholder="Ej: Ruta 3" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_destino">Destino</label>
                            <input type="text" id="edit_destino" name="destino" placeholder="Ej: Centro" required>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_origen">Origen</label>
                            <input type="text" id="edit_origen" name="origen" placeholder="Ej: Av. Principal" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_id_ruta_retorno">Ruta de retorno (Opcional)</label>
                            <select id="edit_id_ruta_retorno" name="id_ruta_retorno">
                                <option value="">-- Sin ruta de retorno --</option>
                                <?php
                                $result_rutas_edit = $conexion->query("SELECT id_ruta, nombre FROM rutas ORDER BY nombre");
                                while ($row_ruta_edit = $result_rutas_edit->fetch_assoc()) {
                                    echo "<option value='{$row_ruta_edit['id_ruta']}'>{$row_ruta_edit['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_activa">Activa</label>
                            <select id="edit_activa" name="activa" required>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>

        // Función para mostrar notificaciones
        function showNotification(message, type = 'info') {
            // Crear elemento de notificación
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;

            // Estilos básicos
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                z-index: 10000;
                animation: slideIn 0.3s ease;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                max-width: 350px;
                cursor: pointer;
            `;

            // Colores según tipo
            if (type === 'success') {
                notification.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            } else if (type === 'error') {
                notification.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
            } else {
                notification.style.background = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
            }

            // Animación
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            if (!document.querySelector('style[data-notification="true"]')) {
                style.setAttribute('data-notification', 'true');
                document.head.appendChild(style);
            }

            // Añadir al documento
            document.body.appendChild(notification);

            // Auto-eliminar después de 5 segundos
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);

            // Permitir cerrar manualmente
            notification.addEventListener('click', () => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            });
        }

        // Verificar si hay mensaje de éxito (desde PHP)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            showNotification('Ruta actualizada exitosamente', 'success');
            // Limpiar URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/update.js"></script>
    <script src="../../assets/js/delete/delete_rutas.js"></script>
    <script src="../../assets/js/pagination.js"></script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
</body>
</html>
