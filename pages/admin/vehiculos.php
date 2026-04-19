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
    <title>Vehículos - Transporte Público</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        $page_title  = 'Gestión de Vehículos';
        $active_page = 'vehiculos';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Vehículos</h2>
                                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
                <div class="section-header">
                    <h3>Lista de Vehículos</h3>
                    <button class="btn-add">+ Agregar nuevo vehículo</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Número de placa</th>
                            <th>Modelo</th>
                            <th>Capacidad</th>
                            <th>RFC de la empresa</th>
                            <th>Activa</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Conexión a la base de datos
                    $conn = $conexion;

                    // Consulta para obtener los vehículos
                    $sql = "SELECT * FROM vehiculos";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $statusClass = $row["activo"] ? 'status-active' : 'status-inactive';
                            $statusText = $row["activo"] ? 'Sí' : 'No';
                            
                            echo '<tr>
                                    <td data-label="Número de placa" data-id="'.$row["id_vehiculo"].'">' . $row["placa"] . '</td>
                                    <td data-label="Modelo">' . $row["modelo"] . '</td>
                                    <td data-label="Capacidad">' . $row["capacidad"] . '</td>
                                    <td data-label="RFC de la Empresa">' . $row["rfc_empresa"] . '</td>
                                    <td data-label="Activa"><span class="'.$statusClass.'">' . $statusText . '</span></td>
                                    <td>
                                        <button class="btn-action btn-edit">Editar</button>
                                        <button class="btn-action btn-delete">Eliminar</button>
                                    </td>
                                </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No hay vehículos registrados</td></tr>';
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

    <!-- Modal para agregar nuevo vehiculo -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nuevo Vehiculo</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../../controllers/insert_vehiculos.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>Placa del Vehiculo</label>
                            <input type="text" id="" name="placa" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Modelo de Vehiculo</label>
                            <input type="text" id="" name="modelo" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Capacidad del Vehiculo</label>
                            <input type="number" id="" name="capacidad" placeholder=""></input>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Empresa</label>
                            <select name="rfc_empresa" id="">
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Activo</label>
                            <select name="activo" id="">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
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

    <!-- Modal para editar vehiculo -->
    <div class="modal-overlay" id="editVehicleModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar Vehículo</h3>
                <button class="modal-close" id="closeEditVehicleModal">×</button>
            </div>
            <form id="editVehicleForm" action="../../controllers/update/actu_vehiculos.php" method="POST">
                <input type="hidden" id="edit_id_vehiculo" name="id_vehiculo">
                <div class="modal-body">
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_placa">Placa del Vehiculo</label>
                            <input type="text" id="edit_placa" name="placa" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_modelo">Modelo de Vehiculo</label>
                            <input type="text" id="edit_modelo" name="modelo" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_capacidad">Capacidad del Vehiculo</label>
                            <input type="number" id="edit_capacidad" name="capacidad" required>
                        </div>
                    </div>
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_rfc_empresa">RFC de Empresa</label>
                            <select id="edit_rfc_empresa" name="rfc_empresa" required>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
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
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditVehicleModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            handleInsertForm(
                document.getElementById('routeForm'),
                'Vehículo agregado exitosamente'
            );

            // Manejar actualización de vehículos
            handleUpdateForm(
                document.getElementById('editVehicleForm'),
                'Vehículo actualizado exitosamente'
            );

            // Manejar eliminación de vehículos
            initializeDeleteButtons(
                '.btn-delete',
                '/GoWay/controllers/delete/delete_vehiculo.php',
                'id_vehiculo',
                '¿Estás seguro de que deseas eliminar este vehículo?'
            );

            // Modal para agregar
            document.querySelector('.btn-add').addEventListener('click', function() {
                document.getElementById('addRouteModal').classList.add('active');
            });

            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('addRouteModal').classList.remove('active');
            });

            document.getElementById('cancelModal').addEventListener('click', function() {
                document.getElementById('addRouteModal').classList.remove('active');
            });

            document.getElementById('addRouteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
    </script>

    <script src="../../assets/js/notifications.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/update/actu_vehiculo.js"></script>
    <script src="../../assets/js/pagination.js"></script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
</body>
</html>
