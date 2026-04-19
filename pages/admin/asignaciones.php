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
    <title>Asignaciones - Transporte Público</title>
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
        $page_title  = 'Gestión de Asignaciones';
        $active_page = 'asignaciones';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Asignaciones</h2>
                                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
                <div class="section-header">
                    <h3>Lista de Asignaciones</h3>
                    <button class="btn-add">+ Agregar nueva asignación</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RFC de la empresa</th>
                            <th>Número de placa</th>
                            <th>RFC del conductor</th>
                            <th>Ruta</th>
                            <th>Horario</th>
                            <th>Fecha de creación</th>
                            <th>Activa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = $conexion;
                        
                        // Consulta con JOINs para obtener placa del vehículo y nombre de la ruta
                        // ANTES: $sql = "SELECT * FROM asignaciones";
                        $sql = "SELECT a.*, v.placa, r.nombre as nombre_ruta 
                                FROM asignaciones a 
                                LEFT JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo 
                                LEFT JOIN rutas r ON a.id_ruta = r.id_ruta";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusClass = $row["activa"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activa"] ? 'Sí' : 'No';
                                
                                echo '<tr>
                                        <td data-label="RFC de la Empresa" data-id="'.$row["id_asignacion"].'">'.$row["rfc_empresa"].'</td>
                                        <td data-label="Número de placa">'.$row["placa"].'</td>
                                        <td data-label="RFC Conductor">'.$row["rfc_conductor"].'</td>
                                        <td data-label="Ruta">'.$row["nombre_ruta"].'</td>
                                        <td data-label="Horario">'.$row["id_horario"].'</td>
                                        <td data-label="Fecha de creación">'.$row["fecha"].'</td>
                                        <td data-label="Activa"><span class="'.$statusClass.'">'.$statusText.'</span></td>
                                        <td>
                                            <button class="btn-action btn-delete">Eliminar</button>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8">No hay asignaciones registradas</td></tr>';
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

    <!-- Modal para agregar nueva Asignación -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nueva Asignacion</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../../controllers/insert_asignaciones.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de la Empresa</label>
                            <select name="rfc_empresa" required>
                                <option value="" disabled selected>Seleccionar empresa</option>
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
                            <label>Vehículo</label>
                            <select name="id_vehiculo" required>
                                <option value="" disabled selected>Seleccionar vehículo</option>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT id_vehiculo, placa FROM vehiculos");
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_vehiculo']}'>{$row['placa']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>RFC Conductor</label>
                            <select name="rfc_conductor" required>
                                <option value="" disabled selected>Seleccionar conductor</option>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT rfc_conductor, nombre FROM conductores");
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['rfc_conductor']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label>Ruta</label>
                            <select name="id_ruta" required>
                                <option value="" disabled selected>Seleccionar ruta</option>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT id_ruta, nombre FROM rutas");
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_ruta']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Horario</label>
                            <select name="id_horario" required>
                                <option value="" disabled selected>Seleccionar horario</option>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT id_horario, tipo_dia, hora_salida FROM horarios");
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_horario']}'>{$row['tipo_dia']} - {$row['hora_salida']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Fecha de creación</label>
                            <input type="date" name="fecha" required placeholder="Fecha de creación">
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
        handleInsertForm(document.getElementById('routeForm'), 'Asignación agregada correctamente');

        // Cerrar modal al hacer clic fuera
        document.getElementById('addRouteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Inicializar botones de eliminación
        initializeDeleteButtons(
            '.btn-delete',
            '../../controllers/delete/delete_asignaciones.php',
            'id_asignacion',
            '¿Estás seguro de que deseas eliminar esta asignación?'
        );
    </script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
</body>
</html>
