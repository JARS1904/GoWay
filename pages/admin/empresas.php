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
    <title>Empresas - Transporte Público</title>
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
        $page_title  = 'Gestión de Empresas';
        $active_page = 'empresas';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Empresas</h2>
                                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
                <div class="section-header">
                    <h3>Lista de Empresas</h3>
                    <button class="btn-add">+ Agregar nueva empresa</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RFC de la empresa</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Activa</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = $conexion;
                        
                        // Consulta para obtener las empresas
                        $sql = "SELECT * FROM empresas";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusClass = $row["activo"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activo"] ? 'Sí' : 'No';
                                
                                echo '<tr>
                                        <td data-label="RFC de la Empresa" data-id="'.$row["rfc_empresa"].'">'.$row["rfc_empresa"].'</td>
                                        <td data-label="Nombre">'.$row["nombre"].'</td>
                                        <td data-label="Dirección">'.$row["direccion"].'</td>
                                        <td data-label="Teléfono">'.$row["telefono"].'</td>
                                        <td data-label="Email">'.$row["email"].'</td>
                                        <td data-label="Activa"><span class="'.$statusClass.'">'.$statusText.'</span></td>
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
                            echo '<tr><td colspan="7">No hay empresas registradas</td></tr>';
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

    <!-- Modal para agregar nueva Empresa -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nueva empresa</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../../controllers/insert_empresa.php" method="POST">
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

    <!-- Modal para editar empresas -->
    <div class="modal-overlay" id="editEmpresasModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar Empresa</h3>
                <button class="modal-close" id="closeEditEmpresasModal">×</button>
            </div>
            <form id="editEmpresasForm" action="actualizar/actu_empresasSql.php" method="POST">
                <div class="modal-body">
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_rfc_empresa">RFC de la empresa</label>
                            <input type="text" id="edit_rfc_empresa" name="rfc_empresa" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_nombre_empresa">Nombre de Empresa</label>
                            <input type="text" id="edit_nombre_empresa" name="nombre_empresa" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_direccion_empresa">Direccion de Empresa</label>
                            <input type="text" id="edit_direccion_empresa" name="direccion_empresa" required>
                        </div>
                    </div>
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_telefono">Telefono</label>
                            <input type="text" id="edit_telefono" name="telefono" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_email_empresa">E-mail</label>
                            <input type="text" id="edit_email_empresa" name="email_empresa" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_activo">Activo</label>
                            <select id="edit_activo" name="activo">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditEmpresasModal">Cancelar</button>
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
        handleInsertForm(document.getElementById('routeForm'), 'Empresa agregada correctamente');

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
                    
                    document.getElementById('edit_rfc_empresa').value = cells[0].textContent.trim();
                    document.getElementById('edit_nombre_empresa').value = cells[1].textContent.trim();
                    document.getElementById('edit_direccion_empresa').value = cells[2].textContent.trim();
                    document.getElementById('edit_telefono').value = cells[3].textContent.trim();
                    document.getElementById('edit_email_empresa').value = cells[4].textContent.trim();
                    
                    const statusText = cells[5].querySelector('span').textContent.trim();
                    document.getElementById('edit_activo').value = statusText === 'Sí' ? 1 : 0;
                    
                    document.getElementById('editEmpresasModal').classList.add('active');
                }
            });
        }

        // Cerrar modal de edición
        document.getElementById('closeEditEmpresasModal').addEventListener('click', () => {
            document.getElementById('editEmpresasModal').classList.remove('active');
        });

        document.getElementById('cancelEditEmpresasModal').addEventListener('click', () => {
            document.getElementById('editEmpresasModal').classList.remove('active');
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('editEmpresasModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Manejo del formulario de edición
        handleUpdateForm(document.getElementById('editEmpresasForm'), 'Empresa actualizada correctamente');

        // Inicializar botones de eliminación
        initializeDeleteButtons(
            '.btn-delete',
            '../../controllers/delete/delete_empresas.php',
            'rfc_empresa',
            '¿Estás seguro de que deseas eliminar esta empresa?'
        );
    </script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
</body>
</html>
