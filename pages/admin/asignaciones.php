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
    <!-- KPI Dashboard Section -->
    <div class="kpi-section-title" style="margin-top:0;">
        <h2>Indicadores Operativos</h2>
        <span class="kpi-section-badge">Asignaciones Operativas</span>
    </div>
    
    <div class="stats-grid" id="asignacionesStatsGrid" style="display:none; margin-bottom:20px;"></div>

    <div class="charts-grid" id="asignacionesChartsGrid" style="display:none; grid-template-columns: 1fr 2fr;">
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title"><h4>Estado operativo</h4><span>Total Histórico</span></div>
                <div class="chart-card-icon orange"><span class="material-icons">assignment</span></div>
            </div>
            <canvas id="chartEstadoHoy" height="160"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title"><h4>Carga de Conductores</h4><span>Top 5 en los últimos 7 días</span></div>
                <div class="chart-card-icon blue"><span class="material-icons">badge</span></div>
            </div>
            <canvas id="chartConductores" height="160"></canvas>
        </div>
    </div>
                <div class="section-header">
                    <h3>Lista de Asignaciones</h3>
                    <button class="btn-add">+ Agregar nueva asignación</button>
                </div>
                <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Placa</th>
                            <th>Conductor</th>
                            <th>Ruta</th>
                            <th>Horario</th>
                            <th>Fecha</th>
                            <th>Asientos</th>
                            <th>Estado</th>
                            <th>Activa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = $conexion;
                        
                        // Consulta con JOINs para obtener placa del vehículo y nombre de la ruta
                        $sql = "SELECT a.*, v.placa, r.nombre as nombre_ruta, h.tipo_dia, h.hora_salida 
                                FROM asignaciones a 
                                LEFT JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo 
                                LEFT JOIN rutas r ON a.id_ruta = r.id_ruta
                                LEFT JOIN horarios h ON a.id_horario = h.id_horario";
                        if ($_SESSION['rol'] == 4) {
                            $rfc_empresa_session = $_SESSION['rfc_empresa'];
                            $sql .= " WHERE a.rfc_empresa = '$rfc_empresa_session'";
                        }
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusClass = $row["activa"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activa"] ? 'Sí' : 'No';
                                
                                echo '<tr>
                                        <td data-label="Empresa" data-id="'.$row["id_asignacion"].'">'.$row["rfc_empresa"].'</td>
                                        <td data-label="Placa">'.$row["placa"].'</td>
                                        <td data-label="Conductor">'.$row["rfc_conductor"].'</td>
                                        <td data-label="Ruta">'.$row["nombre_ruta"].'</td>
                                        <td data-label="Horario">
                                            <strong>ID: '.$row["id_horario"].'</strong><br>
                                            <span style="font-size: 0.85em; color: #666;">'.$row["tipo_dia"].'</span><br>
                                            <span style="font-size: 0.85em; color: #666;">'.$row["hora_salida"].'</span>
                                        </td>
                                        <td data-label="Fecha">'.$row["fecha"].'</td>
                                        <td data-label="Asientos">'.$row["asientos_disp"].'</td>
                                        <td data-label="Estado">'.ucfirst(str_replace("_", " ", $row["estado"])).'</td>
                                        <td data-label="Activa"><span class="'.$statusClass.'">'.$statusText.'</span></td>
                                        <td>
                                            <div class="kebab-menu">
                                                <button class="kebab-btn" onclick="toggleKebabMenu(this, event)">
                                                    <span class="material-icons">more_vert</span>
                                                </button>
                                                <div class="dropdown-content">
                                                    <button class="dropdown-item btn-edit" data-id="'.$row["id_asignacion"].'" data-empresa="'.$row["rfc_empresa"].'" data-ruta="'.$row["id_ruta"].'" data-horario="'.$row["id_horario"].'" data-conductor="'.$row["rfc_conductor"].'" data-vehiculo="'.$row["id_vehiculo"].'" data-fecha="'.$row["fecha"].'" data-estado="'.$row["estado"].'" data-asientos="'.$row["asientos_disp"].'" data-activa="'.$row["activa"].'">
                                                        <span class="material-icons">edit_square</span> Editar
                                                    </button>
                                                    <button class="dropdown-item btn-delete" data-id="'.$row["id_asignacion"].'">
                                                        <span class="material-icons">delete_outline</span> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="10">No hay asignaciones registradas</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
                </div>

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
                            <?php if ($_SESSION['rol'] == 1): ?>
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
                            <?php else: ?>
                            <input type="text" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" readonly style="background-color: #f3f4f6; cursor: not-allowed;">
                            <input type="hidden" name="rfc_empresa" value="<?php echo $_SESSION['rfc_empresa']; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="modal-form-group">
                            <label>Vehículo</label>
                            <select name="id_vehiculo" required>
                                <option value="" disabled selected>Seleccionar vehículo</option>
                                <?php
                                $conn = $conexion;
                                $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                                $result = $conn->query("SELECT id_vehiculo, placa, modelo FROM vehiculos" . $where_emp);
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_vehiculo']}'>{$row['placa']} - {$row['modelo']}</option>";
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
                                $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                                $result = $conn->query("SELECT rfc_conductor, nombre FROM conductores" . $where_emp);
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['rfc_conductor']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Estado</label>
                            <select name="estado" required>
                                <option value="programado" selected>Programado</option>
                                <option value="en_ruta">En Ruta</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                                <option value="retrasado">Retrasado</option>
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
                                $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                                $result = $conn->query("SELECT id_ruta, nombre FROM rutas" . $where_emp);
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
                                $where_horario = ($_SESSION['rol'] == 4) ? " JOIN rutas r ON h.id_ruta = r.id_ruta WHERE r.rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                                $result = $conn->query("SELECT h.id_horario, h.tipo_dia, h.hora_salida FROM horarios h" . $where_horario);
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

    <div class="modal-overlay" id="editAssignModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar Asignación</h3>
                <button class="modal-close" id="closeEditModal">&times;</button>
            </div>
            <form id="editAssignForm" action="../../controllers/update_asignacion.php" method="POST">
                <input type="hidden" name="id_asignacion" id="edit_id_asignacion">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de la Empresa</label>
                            <?php if ($_SESSION['rol'] == 1): ?>
                            <select name="rfc_empresa" id="edit_rfc_empresa" required>
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
                            <input type="hidden" id="edit_rfc_empresa" name="rfc_empresa" value="<?php echo $_SESSION['rfc_empresa']; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="modal-form-group">
                            <label>Vehículo</label>
                            <select name="id_vehiculo" id="edit_id_vehiculo" required>
                                <option value="" disabled selected>Seleccionar vehículo</option>
                                <?php
                                $conn = $conexion;
                                $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                                $result = $conn->query("SELECT id_vehiculo, placa, modelo FROM vehiculos" . $where_emp);
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_vehiculo']}'>{$row['placa']} - {$row['modelo']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>RFC Conductor</label>
                            <select name="rfc_conductor" id="edit_rfc_conductor" required>
                                <option value="" disabled selected>Seleccionar conductor</option>
                                <?php
                                $conn = $conexion;
                                $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                                $result = $conn->query("SELECT rfc_conductor, nombre FROM conductores" . $where_emp);
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['rfc_conductor']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Estado</label>
                            <select name="estado" id="edit_estado" required>
                                <option value="programado">Programado</option>
                                <option value="en_ruta">En Ruta</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                                <option value="retrasado">Retrasado</option>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Asientos disponibles</label>
                            <input type="number" name="asientos_disp" id="edit_asientos_disp" min="0" required>
                        </div>
                    </div>
                    
                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label>Ruta</label>
                            <select name="id_ruta" id="edit_id_ruta" required>
                                <option value="" disabled selected>Seleccionar ruta</option>
                                <?php
                                $conn = $conexion;
                                $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                                $result = $conn->query("SELECT id_ruta, nombre FROM rutas" . $where_emp);
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_ruta']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Horario</label>
                            <select name="id_horario" id="edit_id_horario" required>
                                <option value="" disabled selected>Seleccionar horario</option>
                                <?php
                                $conn = $conexion;
                                $where_horario = ($_SESSION['rol'] == 4) ? " JOIN rutas r ON h.id_ruta = r.id_ruta WHERE r.rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                                $result = $conn->query("SELECT h.id_horario, h.tipo_dia, h.hora_salida FROM horarios h" . $where_horario);
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_horario']}'>{$row['tipo_dia']} - {$row['hora_salida']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Fecha de creación</label>
                            <input type="date" name="fecha" id="edit_fecha" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Activa</label>
                            <select name="activa" id="edit_activa" required>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Actualizar</button>
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

        // Edit Modal Logic
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('edit_id_asignacion').value = button.dataset.id;
                document.getElementById('edit_rfc_empresa').value = button.dataset.empresa;
                document.getElementById('edit_id_ruta').value = button.dataset.ruta;
                document.getElementById('edit_id_horario').value = button.dataset.horario;
                document.getElementById('edit_rfc_conductor').value = button.dataset.conductor;
                document.getElementById('edit_id_vehiculo').value = button.dataset.vehiculo;
                document.getElementById('edit_fecha').value = button.dataset.fecha;
                document.getElementById('edit_estado').value = button.dataset.estado;
                document.getElementById('edit_activa').value = button.dataset.activa;
                document.getElementById('edit_asientos_disp').value = button.dataset.asientos;
                document.getElementById('editAssignModal').classList.add('active');
            });
        });

        document.getElementById('closeEditModal').addEventListener('click', () => {
            document.getElementById('editAssignModal').classList.remove('active');
        });

        document.getElementById('cancelEditModal').addEventListener('click', () => {
            document.getElementById('editAssignModal').classList.remove('active');
        });

        document.getElementById('editAssignModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Use the existing handleInsertForm or generic form handler for updates
        handleInsertForm(document.getElementById('editAssignForm'), 'Asignación actualizada correctamente');
    </script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const GW = { blue:'#0660fe', green:'#10b981', orange:'#f59e0b', red:'#ef4444', gray:'#64748b', purple:'#8b5cf6' };
    fetch('../../api/kpis_api.php?seccion=asignaciones').then(r=>r.json()).then(data => {
        if(!data.success) return;
        document.getElementById('asignacionesStatsGrid').style.display = 'grid';
        document.getElementById('asignacionesStatsGrid').innerHTML = `
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:var(--primary-color);">assignment</span></div><div class="stat-card-content"><h3>Total Asignaciones</h3><p class="stat-number">${data.kpi.hoy_total}</p><span class="stat-label">Registradas</span></div></div>
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:#10b981;">check_circle</span></div><div class="stat-card-content"><h3>Turnos Cubiertos</h3><p class="stat-number">${data.kpi.porcentaje}%</p><span class="stat-label">${data.kpi.hoy_completadas} completados</span></div></div>
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:#ef4444;">warning</span></div><div class="stat-card-content"><h3>Conflictos</h3><p class="stat-number">${data.kpi.hoy_conflictivas}</p><span class="stat-label">Canceladas/Retrasadas</span></div></div>
        `;
        document.getElementById('asignacionesChartsGrid').style.display = 'grid';
        if(data.estado_hoy && data.estado_hoy.data.some(v=>v>0)) {
            const estadoColorsMap = { 'Programado': GW.gray, 'Completado': GW.green, 'En Ruta': GW.blue, 'Cancelado': GW.red, 'Retrasado': GW.orange, 'Inactiva/Cancelada': GW.red };
            const bgColors = data.estado_hoy.labels.map(l => estadoColorsMap[l] || GW.gray);
            new Chart(document.getElementById('chartEstadoHoy'), {
                type: 'doughnut', data: { labels: data.estado_hoy.labels, datasets: [{ data: data.estado_hoy.data, backgroundColor: bgColors }] }, options: {plugins: {legend: {position: 'bottom'}}, cutout:'70%'}
            });
        }
        if(data.top_conductores && data.top_conductores.data.length > 0) {
            new Chart(document.getElementById('chartConductores'), {
                type: 'bar', data: { labels: data.top_conductores.labels, datasets: [{ label:'Asignaciones', data: data.top_conductores.data, backgroundColor: GW.blue, borderRadius:4 }] }, options: { indexAxis: 'y', plugins:{legend:{display:false}} }
            });
        }
    });
});
</script>
</body>
</html>
