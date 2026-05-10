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
    <!-- KPI Dashboard Section -->
    <div class="kpi-section-title" style="margin-top:0;">
        <h2>Indicadores Operativos</h2>
        <span class="kpi-section-badge">Flota de Vehículos</span>
    </div>
    
    <div class="stats-grid" id="vehiculosStatsGrid" style="display:none; margin-bottom:20px;"></div>

    <div class="charts-grid" id="vehiculosChartsGrid" style="display:none; grid-template-columns: 1fr 2fr;">
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title"><h4>Estado del Parque</h4><span>Disponibilidad actual</span></div>
                <div class="chart-card-icon green"><span class="material-icons">directions_bus</span></div>
            </div>
            <canvas id="chartEstadoVehiculos" height="160"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title"><h4>Antigüedad y Modelos</h4><span>Distribución de la flota</span></div>
                <div class="chart-card-icon orange"><span class="material-icons">commute</span></div>
            </div>
            <canvas id="chartModelos" height="160"></canvas>
        </div>
    </div>
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
                    if ($_SESSION['rol'] == 4) {
                        $rfc_empresa_session = $_SESSION['rfc_empresa'];
                        $sql .= " WHERE rfc_empresa = '$rfc_empresa_session'";
                    }
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
                            <?php if ($_SESSION['rol'] == 1): ?>
                            <select name="rfc_empresa" id="" required>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const GW = { blue:'#0660fe', green:'#10b981', orange:'#f59e0b', red:'#ef4444', text:'#1a1c23', gray:'#e2e8f0' };
    fetch('../../api/kpis_api.php?seccion=vehiculos').then(r=>r.json()).then(data => {
        if(!data.success) return;
        document.getElementById('vehiculosStatsGrid').style.display = 'grid';
        document.getElementById('vehiculosStatsGrid').innerHTML = `
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:var(--primary-color);">directions_bus</span></div><div class="stat-card-content"><h3>Total Flota</h3><p class="stat-number">${data.kpi.total}</p><span class="stat-label">Registrados</span></div></div>
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:#10b981;">check_circle</span></div><div class="stat-card-content"><h3>Disponibilidad</h3><p class="stat-number">${data.kpi.disp}%</p><span class="stat-label">Activos</span></div></div>
            
        `;
        document.getElementById('vehiculosChartsGrid').style.display = 'grid';
        if(data.estado_vehiculos && data.estado_vehiculos.data.some(v=>v>0)) {
            new Chart(document.getElementById('chartEstadoVehiculos'), {
                type: 'doughnut', data: { labels: data.estado_vehiculos.labels, datasets: [{ data: data.estado_vehiculos.data, backgroundColor: [GW.green, GW.orange] }] }, options: {plugins: {legend: {position: 'bottom'}}, cutout: '70%'}
            });
        }
        if(data.modelos && data.modelos.data.length > 0) {
            new Chart(document.getElementById('chartModelos'), {
                type: 'bar', data: { labels: data.modelos.labels, datasets: [{ label:'Unidades', data: data.modelos.data, backgroundColor: GW.blue, borderRadius:4 }] }, options: { plugins:{legend:{display:false}} }
            });
        }
    });
});
</script>
</body>
</html>
