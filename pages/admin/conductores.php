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
    <title>Conductores - Transporte Público</title>
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
        $page_title  = 'Gestión de Conductores';
        $active_page = 'conductores';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Conductores</h2>
                                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
                <div class="section-header">
                    <h3>Lista de Conductores</h3>
                    <button class="btn-add">+ Agregar nuevo conductor</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RFC del conductor</th>
                            <th>RFC de la empresa</th>
                            <th>Nombre</th>
                            <th>Licencia</th>
                            <th>Teléfono</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = $conexion;
                        
                        // Consulta para obtener los conductores
                        $sql = "SELECT * FROM conductores";
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
                                
                                echo '<tr data-id="'.$row["rfc_conductor"].'">
                                        <td data-label="RFC del Conductor" data-id="'.$row["rfc_conductor"].'">' . htmlspecialchars($row["rfc_conductor"]) . '</td>
                                        <td data-label="RFC de la Empresa">'.htmlspecialchars($row["rfc_empresa"]).'</td>
                                        <td data-label="Nombre" data-nombre="' . $nombre_esc . '"><div class="avatar-cell">' . $avatar . '<span>' . $nombre_esc . '</span></div></td>
                                        <td data-label="Licencia">'.htmlspecialchars($row["licencia"]).'</td>
                                        <td data-label="Teléfono">'.htmlspecialchars($row["telefono"]).'</td>
                                        <td data-label="Activo"><span class="'.$statusClass.'">'.$statusText.'</span></td>
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
                            echo '<tr><td colspan="7">No hay conductores registrados</td></tr>';
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

    <!-- Modal para agregar nuevo conductor -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nuevo conductor</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../../controllers/insert/insert_conductor.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Conductor</label>
                            <input type="text" id="rfc_conductor" name="rfc_conductor" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Seleccione RFC de la Empresa</label>
                            <?php if ($_SESSION['rol'] == 1): ?>
                            <select name="rfc_empresa" required id="">
                                <option value="" disabled selected>Seleccionar empresa</option>
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
                            <input type="text" name="nombre" required>
                        </div>
                    </div>
                    
                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label>Licencia</label>
                            <input type="text" id="licencia" name="licencia" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Telefono</label>
                            <input id="telefono" name="telefono" required></input>
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

    <!-- Modal para editar conductores -->
    <div class="modal-overlay" id="editConductoresModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar Conductor</h3>
                <button class="modal-close" id="closeEditConductoresModal">×</button>
            </div>
            <form id="editVehicleForm" action="../../controllers/update/update_conductor.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_rfc_conductor">RFC de Conductor</label>
                            <input type="text" id="edit_rfc_conductor" name="rfc_conductor" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_rfc_empresa">RFC Empresa</label>
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
                            <label for="edit_licencia">Licencia</label>
                            <input type="text" id="edit_licencia" name="licencia" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_telefono">Telefono</label>
                            <input type="text" id="edit_telefono" name="telefono" required>
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
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditConductoresModal">Cancelar</button>
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
        handleInsertForm(document.getElementById('routeForm'), 'Conductor agregado correctamente', function(data) {
            if (data.nuevoRegistro) {
                const tbody = document.querySelector('.data-table tbody');
                if (tbody.querySelector('td[colspan]')) {
                    tbody.innerHTML = '';
                }
                
                const reg = data.nuevoRegistro;
                const statusClass = reg.activo == 1 ? 'status-active' : 'status-inactive';
                const statusText = reg.activo == 1 ? 'Sí' : 'No';
                
                // Avatar html
                const initial = reg.nombre.charAt(0).toUpperCase();
                const avatarHtml = reg.foto && reg.foto !== '' 
                    ? `<img src="../../assets/images/profiles/${reg.foto}" class="avatar-img" alt="foto">`
                    : `<div class="avatar-initials">${initial}</div>`;
                
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', reg.rfc_conductor);
                tr.style.transition = 'background-color 0.5s, opacity 0.5s';
                tr.style.backgroundColor = '#dbeafe';
                tr.style.opacity = '0';
                
                tr.innerHTML = `
                    <td data-label="RFC del Conductor" data-id="${reg.rfc_conductor}">${reg.rfc_conductor}</td>
                    <td data-label="RFC de la Empresa">${reg.rfc_empresa}</td>
                    <td data-label="Nombre" data-nombre="${reg.nombre}"><div class="avatar-cell">${avatarHtml}<span>${reg.nombre}</span></div></td>
                    <td data-label="Licencia">${reg.licencia}</td>
                    <td data-label="Teléfono">${reg.telefono}</td>
                    <td data-label="Activo"><span class="${statusClass}">${statusText}</span></td>
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
                `;
                
                tbody.prepend(tr);
                
                setTimeout(() => { tr.style.opacity = '1'; }, 10);
                setTimeout(() => { tr.style.backgroundColor = ''; }, 1000);

                const countEl = document.getElementById('toolbarCount');
                if (countEl) {
                    const count = tbody.querySelectorAll('tr').length;
                    countEl.textContent = `${count} registro${count !== 1 ? 's' : ''}`;
                }

                const deleteBtn = tr.querySelector('.btn-delete');
                if (deleteBtn) {
                    handleDeleteButton(deleteBtn, '../../controllers/delete/delete_conductores.php', 'rfc_conductor', '¿Estás seguro de que deseas eliminar este conductor?', handleDeleteSuccess);
                }
            }
        });

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
                    
                    document.getElementById('edit_rfc_conductor').value = cells[0].textContent.trim();
                    document.getElementById('edit_rfc_empresa').value = cells[1].textContent.trim();
                    document.getElementById('edit_nombre').value = cells[2].dataset.nombre;
                    document.getElementById('edit_licencia').value = cells[3].textContent.trim();
                    document.getElementById('edit_telefono').value = cells[4].textContent.trim();
                    
                    const statusText = cells[5].querySelector('span').textContent.trim();
                    document.getElementById('edit_activo').value = statusText === 'Sí' ? 1 : 0;
                    
                    document.getElementById('editConductoresModal').classList.add('active');
                }
            });
        }

        // Cerrar modal de edición
        document.getElementById('closeEditConductoresModal').addEventListener('click', () => {
            document.getElementById('editConductoresModal').classList.remove('active');
        });

        document.getElementById('cancelEditConductoresModal').addEventListener('click', () => {
            document.getElementById('editConductoresModal').classList.remove('active');
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('editConductoresModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Manejo del formulario de edición
        handleUpdateForm(document.getElementById('editVehicleForm'), 'Conductor actualizado correctamente', function(data) {
            if (data.registroActualizado) {
                const reg = data.registroActualizado;
                const tr = document.querySelector(`tr[data-id="${reg.rfc_conductor}"]`);
                if (tr) {
                    const cells = tr.querySelectorAll('td');
                    cells[0].textContent = reg.rfc_conductor;
                    cells[1].textContent = reg.rfc_empresa;
                    
                    // Actualizar nombre y mantener avatar original o actualizarlo si se subió foto
                    cells[2].dataset.nombre = reg.nombre;
                    if (reg.foto && reg.foto !== '') {
                        cells[2].innerHTML = `<div class="avatar-cell"><img src="../../assets/images/profiles/${reg.foto}" class="avatar-img" alt="foto"><span>${reg.nombre}</span></div>`;
                    } else {
                        // Mantener el avatar actual pero cambiar el span del nombre
                        const currentAvatarHtml = cells[2].querySelector('.avatar-cell').innerHTML.split('<span>')[0];
                        cells[2].innerHTML = `<div class="avatar-cell">${currentAvatarHtml}<span>${reg.nombre}</span></div>`;
                    }
                    
                    cells[3].textContent = reg.licencia;
                    cells[4].textContent = reg.telefono;
                    
                    const statusClass = reg.activo == 1 ? 'status-active' : 'status-inactive';
                    const statusText = reg.activo == 1 ? 'Sí' : 'No';
                    cells[5].innerHTML = `<span class="${statusClass}">${statusText}</span>`;
                    
                    tr.style.transition = 'background-color 0.5s';
                    tr.style.backgroundColor = '#dcfce7';
                    setTimeout(() => { tr.style.backgroundColor = ''; }, 1000);
                }
            }
        });

        const handleDeleteSuccess = function(data, button) {
            const row = button.closest('tr');
            if (row) {
                row.style.transition = 'background-color 0.5s, opacity 0.5s';
                row.style.backgroundColor = '#fee2e2';
                row.style.opacity = '0';
                
                setTimeout(() => {
                    row.remove();
                    const countEl = document.getElementById('toolbarCount');
                    if (countEl) {
                        const tbody = document.querySelector('.data-table tbody');
                        const count = tbody.querySelectorAll('tr').length;
                        if (count === 0) {
                            tbody.innerHTML = '<tr><td colspan="7">No hay conductores registrados</td></tr>';
                            countEl.textContent = '0 registros';
                        } else {
                            countEl.textContent = `${count} registro${count !== 1 ? 's' : ''}`;
                        }
                    }
                }, 500);
            }
        };

        // Inicializar botones de eliminación
        initializeDeleteButtons(
            '.btn-delete',
            '../../controllers/delete/delete_conductores.php',
            'rfc_conductor',
            '¿Estás seguro de que deseas eliminar este conductor?',
            handleDeleteSuccess
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
