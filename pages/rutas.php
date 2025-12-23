<!--Se agreo para el manejo de sesión-->
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rutas - Transporte Público</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/logo.png" type="image/png">
</head>

<body>
    <div class="container">
        <!-- Overlay para fondo oscuro -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <!-- Barra Superior Móvil -->
        <div class="mobile-topbar">
            <div class="mobile-topbar-content">
                <div class="mobile-topbar-left">
                    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
                    <h1 class="mobile-page-title">Gestión de Rutas</h1>
                </div>
                <div class="mobile-topbar-right">
                    <div class="mobile-user-info">
                        <span>Admin</span>
                        <img src="../assets/images/icons/administrador.png" alt="Usuario">
                    </div>
                </div>
            </div>
        </div>

        <!-- Menú Lateral -->
        <aside id="sidebar" class="sidebar">
            <!-- Botón de Cerrar para Móvil -->
            <button class="sidebar-close" onclick="closeSidebar()">&times;</button>
            
            <div class="logo">
                <img src="../assets/images/logo.png" alt="Logo de GoWay" class="logo-img">
                <h1>GoWay</h1>
            </div>
            <nav>
                <ul>
                    <li>
                        <a href="../index.php">
                            <img src="../assets/images/icons/icon_dashboard.png" alt="Dashboard" class="icon">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="rutas.php" class="active">
                            <img src="../assets/images/icons/icon_rutas.png" alt="Rutas" class="icon">
                            <span>Rutas</span>
                        </a>
                    </li>
                    <li>
                        <a href="horarios.php">
                            <img src="../assets/images/icons/icon_horarios.png" alt="Horarios" class="icon">
                            <span>Horarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="vehiculos.php">
                            <img src="../assets/images/icons/icon_vehiculos.png" alt="Vehículos" class="icon">
                            <span>Vehículos</span>
                        </a>
                    </li>
                    <li>
                        <a href="conductores.php">
                            <img src="../assets/images/icons/icon_conductores.png" alt="Conductores" class="icon">
                            <span>Conductores</span>
                        </a>
                    </li>
                    <li>
                        <a href="empresas.php">
                            <img src="../assets/images/icons/icon_empresas.png" alt="Empresas" class="icon">
                            <span>Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="checadores.php">
                            <img src="../assets/images/icons/icon_checadores.png" alt="Checadores" class="icon">
                            <span>Checadores</span>
                        </a>
                    </li>
                    <li>
                        <a href="paradas.php">
                            <img src="../assets/images/icons/icon_paradas.png" alt="Paradas" class="icon">
                            <span>Asignaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="usuarios.php">
                            <img src="../assets/images/icons/icon_usuarios.png" alt="Usuarios" class="icon">
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="reportes.php">
                            <img src="../assets/images/icons/icon_reportes.png" alt="Reportes" class="icon">
                            <span>Reportes</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Botón de Cerrar Sesión -->
            <div class="logout-button">
                <a href="login.php" id="logout">
                    <img src="../assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon">
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Rutas</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">
                </div>
            </header>

            <section class="content">
                <h3>Lista de Rutas</h3>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Paradas</th>
                            <th>Activa</th>
                            <th>RFC de la Empresa</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = new mysqli("localhost", "root", "", "goway");

                        if ($conn->connect_error) {
                            die("Error de conexión: " . $conn->connect_error);
                        }

                        // Consulta para obtener las rutas
                        $sql = "SELECT * FROM rutas";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusClass = $row["activa"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activa"] ? 'Sí' : 'No';
                                
                                echo '<tr>
                                        <td data-label="Nombre" data-id="'.$row["id_ruta"].'">'.$row["nombre"].'</td>
                                        <td data-label="Origen">' . $row["origen"] . '</td>
                                        <td data-label="Destino">' . $row["destino"] . '</td>
                                        <td data-label="Paradas">' . $row["paradas"] . '</td>
                                        <td data-label="Activa"><span class="'.$statusClass.'">' . $statusText . '</span></td>
                                        <td data-label="RFC de la Empresa">' . $row["rfc_empresa"] . '</td>
                                        <td>
                                            <button class="btn-action btn-edit">Editar</button>
                                            <button class="btn-action btn-delete">Eliminar</button>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7">No hay rutas registradas</td></tr>';
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>

                <!-- Paginación -->
                <div class="pagination">
                    <button class="pagination-btn" id="prevPage" disabled>‹ Anterior</button>
                    <div class="pagination-info" id="pageInfo">Página 1 de 5</div>
                    <button class="pagination-btn" id="nextPage">Siguiente ›</button>
                </div>

                <button class="btn-add">+ Agregar nueva ruta</button>
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
            <form id="routeForm" action="../controllers/insertar_ruta.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Empresa</label>
                            
                            <select id="" name="rfc_empresa" required>
                                <option disabled selected>Seleccione Empresa</option>
                                <?php
                                $conn = new mysqli("localhost", "root", "", "goway");
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
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
                            <label for="paradas">Paradas</label>
                            <textarea id="paradas" name="paradas" placeholder="Descripción de paradas importantes"></textarea>
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
            <form id="editRouteForm" action="../controllers/update/actualizar_ruta.php" method="POST">
                <input type="hidden" id="edit_id_ruta" name="id_ruta">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Empresa</label>
                            <select id="edit_rfc_empresa" name="rfc_empresa" required>
                                <?php
                                $conn = new mysqli("localhost", "root", "", "goway");
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
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
                            <label for="edit_paradas">Paradas</label>
                            <textarea id="edit_paradas" name="paradas" placeholder="Descripción de paradas importantes"></textarea>
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

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/update.js"></script>
    <script src="../assets/js/delete/delete_rutas.js"></script>
    <script src="../assets/js/pagination.js"></script>
</body>
</html>