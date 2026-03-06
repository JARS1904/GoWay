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
    <title>Usuarios - Transporte Público</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
    <link rel="icon" href="../../assets/images/logo.png" type="image/png">
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
                    <h1 class="mobile-page-title">Gestión de Usuarios</h1>
                </div>
                <div class="mobile-topbar-right">
                    <div class="mobile-user-info">
                        <span><?php echo $_SESSION['nombre']; ?></span>
                        <?php echo !empty($_SESSION['foto']) ? '<img src="../../assets/images/profiles/' . htmlspecialchars($_SESSION['foto']) . '" alt="Usuario" class="header-user-avatar">' : '<img src="../../assets/images/icons/administrador.png" alt="Usuario">'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menú Lateral -->
        <aside id="sidebar" class="sidebar">
            <!-- Botón de Cerrar para Móvil -->
            <button class="sidebar-close" onclick="closeSidebar()">&times;</button>
            
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="Logo de GoWay" class="logo-img">
                <h1>GoWay</h1>
            </div>
            <nav>
                <ul>
                    <li>
                        <a href="../../index.php">
                            <img src="../../assets/images/icons/icon_dashboard.png" alt="Dashboard" class="icon">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="empresas.php">
                            <img src="../../assets/images/icons/icon_empresas.png" alt="Empresas" class="icon">
                            <span>Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="conductores.php">
                            <img src="../../assets/images/icons/icon_conductores.png" alt="Conductores" class="icon">
                            <span>Conductores</span>
                        </a>
                    </li>
                    <li>
                        <a href="vehiculos.php">
                            <img src="../../assets/images/icons/icon_vehiculos.png" alt="Vehículos" class="icon">
                            <span>Vehículos</span>
                        </a>
                    </li>
                    <li>
                        <a href="rutas.php">
                            <img src="../../assets/images/icons/icon_rutas.png" alt="Rutas" class="icon">
                            <span>Rutas</span>
                        </a>
                    </li>
                    <li>
                        <a href="horarios.php">
                            <img src="../../assets/images/icons/icon_horarios.png" alt="Horarios" class="icon">
                            <span>Horarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="paradas_ruta.php">
                            <img src="../../assets/images/icons/icon_paradas.png" alt="Paradas" class="icon">
                            <span>Paradas</span>
                        </a>
                    </li>
                    <li>
                        <a href="asignaciones.php">
                            <img src="../../assets/images/icons/icon_asignacion.png" alt="Asignaciones" class="icon">
                            <span>Asignaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="checadores.php">
                            <img src="../../assets/images/icons/icon_checadores.png" alt="Checadores" class="icon">
                            <span>Checadores</span>
                        </a>
                    </li>
                    <li>
                        <a href="reportes.php">
                            <img src="../../assets/images/icons/icon_reportes.png" alt="Reportes" class="icon">
                            <span>Reportes</span>
                        </a>
                    </li>
                    <li>
                        <a href="usuarios.php">
                            <img src="../../assets/images/icons/icon_usuarios.png" alt="Usuarios" class="icon">
                            <span>Usuarios</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Botón de Cerrar Sesión -->
            <div class="logout-button">
                <a href="../login.php" id="logout">
                    <img src="../../assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon">
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Usuarios</h2>
                <div class="user-info">
                    <span><?php echo $_SESSION['nombre']; ?></span>
                    <?php echo !empty($_SESSION['foto']) ? '<img src="../../assets/images/profiles/' . htmlspecialchars($_SESSION['foto']) . '" alt="Usuario" class="header-user-avatar">' : '<img src="../../assets/images/icons/administrador.png" alt="Usuario">'; ?>
                </div>
            </header>

            <section class="content">
                <div class="section-header">
                    <h3>Lista de Usuarios</h3>
                    <button class="btn-add">+ Agregar nuevo usuario</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <!-- <th>ID usuario</th> -->
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Rol</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = $conexion;

                        // Consulta para obtener los usuarios
                        $sql = "SELECT * FROM usuarios";
                        $result = $conn->query($sql);

                        // Mapeo de roles
                        $rol_mapping = [
                            1 => "Administrador",
                            2 => "Usuario"
                        ];

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $password_display = !empty($row["password"]) ? "●●●●●●●●" : "Sin contraseña";
                                $rol_label = $rol_mapping[$row["rol"]] ?? "Desconocido";
                                $nombre_esc = htmlspecialchars($row["nombre"]);
                                $initial = htmlspecialchars(mb_strtoupper(mb_substr($row["nombre"], 0, 1)));
                                $avatar = !empty($row["foto"])
                                    ? '<img src="../../assets/images/profiles/' . htmlspecialchars($row["foto"]) . '" class="avatar-img" alt="foto">'
                                    : '<div class="avatar-initials">' . $initial . '</div>';
                                
                                echo '<tr data-id="' . $row["id"] . '">
                                        <td data-label="Nombre" data-nombre="' . $nombre_esc . '"><div class="avatar-cell">' . $avatar . '<span>' . $nombre_esc . '</span></div></td>
                                        <td data-label="Email">' . htmlspecialchars($row["email"]) . '</td>
                                        <td data-label="Password">' . $password_display . '</td>
                                        <td data-label="Rol" data-rol="' . $row["rol"] . '">' . $rol_label . '</td>
                                        <td>
                                            <button class="btn-action btn-edit">Editar</button>
                                            <button class="btn-action btn-delete">Eliminar</button>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6">No hay usuarios registrados</td></tr>';
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

    <!-- Modal para agregar nuevo usuario -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nuevo usuario</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../../controllers/insert_user.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingresa un nombre de usuario" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="Email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Ingresa un correo electrónico" required>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label for="password">Contraseña</label>
                            <input type="text" id="password" name="password" placeholder="Ingresa una contraseña" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="rol">Rol</label>
                            <select id="rol" name="rol" required>
                                <option value="" disabled selected>Seleccionar rol</option>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Foto de perfil</label>
                            <label class="foto-upload-label">
                                <span class="foto-upload-icon">📷</span>
                                <span class="foto-upload-btn">Elegir imagen</span>
                                <span class="foto-upload-name">Sin archivo</span>
                                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="input-foto">
                            </label>
                            <small class="form-hint">Opcional · JPG, PNG o WebP · Máx. 2 MB</small>
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

    <!-- Modal para edición de usuarios -->
    <div class="modal-overlay" id="editUserModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar usuario</h3>
                <button class="modal-close" id="closeEditModal">&times;</button>
            </div>
            <form id="editUserForm" action="actualizar/actu_usuariosSql.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id_usuario" name="id_usuario">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre" placeholder="Ingresa el nombre del usuario">
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_email">E-mail</label>
                            <input type="email" id="edit_email" name="email" placeholder="Ingresa un correo electrónico">
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_password">Contraseña</label>
                            <input type="text" id="edit_password" name="password" placeholder="Ingresa una contraseña">
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_rol">Rol</label>
                            <select id="edit_rol" name="rol" required>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Cambiar foto</label>
                            <label class="foto-upload-label">
                                <span class="foto-upload-icon">📷</span>
                                <span class="foto-upload-btn">Elegir imagen</span>
                                <span class="foto-upload-name">Sin archivo</span>
                                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="input-foto">
                            </label>
                            <small class="form-hint">Dejar vacío para conservar la foto actual</small>
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
    </script>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/notifications.js"></script>
    <script src="../../assets/js/pagination.js"></script>
    
    <script>
        // Manejar cierre de modal de agregar
        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('addRouteModal').classList.remove('active');
        });

        document.getElementById('cancelModal').addEventListener('click', () => {
            document.getElementById('addRouteModal').classList.remove('active');
        });

        // Manejo del formulario de inserción
        handleInsertForm(document.getElementById('routeForm'), 'Usuario agregado correctamente');

        // Cerrar modal al hacer clic fuera
        document.getElementById('addRouteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Usar event delegation para botones de edición
        const tbody = document.querySelector('tbody');
        if (tbody) {
            tbody.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-edit');
                if (btn) {
                    const row = btn.closest('tr');
                    const cells = row.querySelectorAll('td');
                    
                    document.getElementById('edit_id_usuario').value = row.getAttribute('data-id');
                    document.getElementById('edit_nombre').value = cells[0].dataset.nombre;
                    document.getElementById('edit_email').value = cells[1].textContent.trim();
                    document.getElementById('edit_password').value = cells[2].textContent.trim();
                    document.getElementById('edit_rol').value = cells[3].getAttribute('data-rol');
                    
                    document.getElementById('editUserModal').classList.add('active');
                }
            });
        }

        // Cerrar modal de edición
        document.getElementById('closeEditModal').addEventListener('click', () => {
            document.getElementById('editUserModal').classList.remove('active');
        });

        document.getElementById('cancelEditModal').addEventListener('click', () => {
            document.getElementById('editUserModal').classList.remove('active');
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('editUserModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Manejo del formulario de edición
        handleUpdateForm(document.getElementById('editUserForm'), 'Usuario actualizado correctamente');

        // Inicializar botones de eliminación
        initializeDeleteButtons(
            '.btn-delete',
            '../../controllers/delete/delete_usuarios.php',
            'id',
            '¿Estás seguro de que deseas eliminar este usuario?'
        );

        document.querySelectorAll('.input-foto').forEach(function(input) {
            input.addEventListener('change', function() {
                var nameEl = this.closest('.foto-upload-label').querySelector('.foto-upload-name');
                if (nameEl) nameEl.textContent = this.files.length > 0 ? this.files[0].name : 'Sin archivo';
            });
        });
    </script>
</body>
</html>
