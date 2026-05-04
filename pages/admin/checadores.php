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
    <title>Checadores - Transporte Público</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php
        $page_title  = 'Gestión de Checadores';
        $active_page = 'checadores';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Checadores</h2>
                                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
                <div class="section-header">
                    <h3>Lista de Checadores</h3>
                    <button class="btn-add">+ Agregar nuevo checador</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RFC del checador</th>
                            <th>RFC de la empresa</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = $conexion;
                        
                        // Consulta para obtener los checadores
                        $sql = "SELECT * FROM checadores";
                        if ($_SESSION['rol'] == 4) {
                            $rfc_empresa_session = $_SESSION['rfc_empresa'];
                            $sql .= " WHERE rfc_empresa = '$rfc_empresa_session'";
                        }
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusClass = $row["activo"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activo"] ? 'Sí' : 'No';
                                $nombre_esc = htmlspecialchars($row["nombre"]);
                                $initial = htmlspecialchars(mb_strtoupper(mb_substr($row["nombre"], 0, 1)));
                                $avatar = !empty($row["foto"])
                                    ? '<img src="../../assets/images/profiles/' . htmlspecialchars($row["foto"]) . '" class="avatar-img" alt="foto">'
                                    : '<div class="avatar-initials">' . $initial . '</div>';
                                
                                echo '<tr>
                                        <td data-label="RFC del Checador" data-id="'.$row["rfc_checador"].'">'.htmlspecialchars($row["rfc_checador"]).'</td>
                                        <td data-label="RFC de la Empresa">'.htmlspecialchars($row["rfc_empresa"]).'</td>
                                        <td data-label="Nombre" data-nombre="' . $nombre_esc . '"><div class="avatar-cell">' . $avatar . '<span>' . $nombre_esc . '</span></div></td>
                                        <td data-label="Usuario">'.htmlspecialchars($row["usuario"]).'</td>
                                        <td data-label="Estado"><span class="'.$statusClass.'">'.$statusText.'</span></td>
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
                            echo '<tr><td colspan="6">No hay checadores registrados</td></tr>';
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

    <!-- Modal para agregar nuevo checador -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nuevo checador</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../../controllers/insert_checador.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Checador</label>
                            <input type="text" id="" name="rfc_checador" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label>RFC de la Empresa</label>
                            <?php if ($_SESSION['rol'] == 1): ?>
                            <select name="rfc_empresa" id="" required>
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
                            <label>Nombre</label>
                            <input type="text" id="" name="nombre" placeholder="" required>
                        </div>
                    </div>
                    
                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label>Usuario</label>
                            <input type="text" id="add_usuario" name="usuario" placeholder="Correo del usuario" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Contraseña</label>
                            <input type="password" id="" name="password" placeholder=""></input>
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

    <!-- Modal para editar checadores -->
    <div class="modal-overlay" id="editChecadoresModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar Checador</h3>
                <button class="modal-close" id="closeEditChecadoresModal">×</button>
            </div>
            <form id="editChecadoresForm" action="actualizar/actu_checadoresSql.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_rfc_checador">RFC de Checador</label>
                            <input type="text" id="edit_rfc_checador" name="rfc_checador" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_rfc_empresa">RFC de Empresa</label>
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
                            <label for="edit_nombre">Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre" required>
                        </div>
                    </div>
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_usuario">Usuario</label>
                            <input type="text" id="edit_usuario" name="usuario" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_password">Contraseña</label>
                            <input type="password" id="edit_password" name="password" placeholder="Deja vacío para conservar la contraseña actual">
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_activo">Activo</label>
                            <select id="edit_activo" name="activo">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
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
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditChecadoresModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>




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
        handleInsertForm(document.getElementById('routeForm'), 'Checador agregado correctamente');

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
                    
                    document.getElementById('edit_rfc_checador').value = cells[0].textContent.trim();
                    document.getElementById('edit_rfc_empresa').value = cells[1].textContent.trim();
                    document.getElementById('edit_nombre').value = cells[2].dataset.nombre;
                    document.getElementById('edit_usuario').value = cells[3].textContent.trim();
                    document.getElementById('edit_password').value = '';
                    
                    const statusText = cells[4].querySelector('span').textContent.trim();
                    document.getElementById('edit_activo').value = statusText === 'Sí' ? 1 : 0;
                    
                    document.getElementById('editChecadoresModal').classList.add('active');
                }
            });
        }

        // Cerrar modal de edición
        document.getElementById('closeEditChecadoresModal').addEventListener('click', () => {
            document.getElementById('editChecadoresModal').classList.remove('active');
        });

        document.getElementById('cancelEditChecadoresModal').addEventListener('click', () => {
            document.getElementById('editChecadoresModal').classList.remove('active');
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('editChecadoresModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Manejo del formulario de edición
        handleUpdateForm(document.getElementById('editChecadoresForm'), 'Checador actualizado correctamente');

        // Inicializar botones de eliminación
        initializeDeleteButtons(
            '.btn-delete',
            '../../controllers/delete/delete_checadores.php',
            'rfc_checador',
            '¿Estás seguro de que deseas eliminar este checador?'
        );

        document.querySelectorAll('.input-foto').forEach(function(input) {
            input.addEventListener('change', function() {
                var nameEl = this.closest('.foto-upload-label').querySelector('.foto-upload-name');
                if (nameEl) nameEl.textContent = this.files.length > 0 ? this.files[0].name : 'Sin archivo';
            });
        });
    </script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
</body>
</html>
