<!--Se agreo para el manejo de sesión-->
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}
?>

<?php
require_once '../../config/conexion_bd.php';
require_once '../../config/sync_session_foto.php';
require_once '../../config/opciones_reportes.php';

// Filtro para multi-tenant (Empresas)
$where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
$where_emp_v = ($_SESSION['rol'] == 4) ? " WHERE v.rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";

// Obtener lista de vehículos con placa y modelo
$sql_vehiculos = "SELECT id_vehiculo, placa, modelo FROM vehiculos" . $where_emp . " ORDER BY placa";
$result_vehiculos = $conexion->query($sql_vehiculos);

// Obtener lista de conductores
$sql_conductores = "SELECT rfc_conductor, nombre FROM conductores" . $where_emp . " ORDER BY nombre";
$result_conductores = $conexion->query($sql_conductores);

// Obtener lista de rutas
$sql_rutas = "SELECT id_ruta, nombre FROM rutas" . $where_emp . " ORDER BY nombre";
$result_rutas = $conexion->query($sql_rutas);

// Obtener reportes recientes
$sql_reportes = "SELECT r.*,
                        v.placa as vehiculo_placa, v.modelo as vehiculo_modelo,
                        c.nombre as conductor_nombre,
                        ru.nombre as ruta_nombre
                 FROM reportes r
                 INNER JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                 INNER JOIN conductores c ON r.rfc_conductor = c.rfc_conductor
                 INNER JOIN rutas ru ON r.id_ruta = ru.id_ruta
                 " . $where_emp_v . "
                 ORDER BY r.created_at DESC
                 LIMIT 150";
$result_reportes = $conexion->query($sql_reportes);

// Si hay error en la conexión o en las consultas
if ($conexion->error) {
    die("Error en la conexión: " . $conexion->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Transporte Público</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/reportes.css">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <script src="../../assets/js/notifications.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="container">
    <?php
    $page_title  = 'Reportes';
    $active_page = 'reportes';
    $base_url    = '../../';
    require_once __DIR__ . '/../../components/sidebar.php';
    ?>

    <!-- Contenido Principal -->
    <main class="main-content" id="mainContent">
        <!-- Header para escritorio -->
        <header class="header">
            <h2>Reportes de Incidencias</h2>
                <div class="header-notif-wrap">
                    <button class="btn-summary" id="btnGenerarResumen" onclick="openSummaryModal()">
                        <span class="material-icons">summarize</span>
                        Generar resumen
                    </button>
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
        </header>

        <section class="content">
            <div class="reports-container">
                <!-- Formulario para nuevo reporte -->
                <div class="report-form-container">
                    <h3>Nuevo reporte de Incidencias</h3>
                    <form id="incidentForm" method="POST" action="#">
                        <div class="form-group">
                            <label for="placa">Placa de la Unidad *</label>
                            <input type="text" id="placa" name="placa" placeholder="Ingrese la placa" required>
                            <small id="placaError">No se encontró asignación para esta placa.</small>
                        </div>
                        
                        <!-- Contenedor para mostrar los datos obtenidos automáticamente -->
                        <div id="datosAsignacion" class="asignacion-info" style="display: none;">
                            <div class="asignacion-info-header">
                                <span class="material-icons">info</span>
                                <h4>Asignación encontrada</h4>
                            </div>
                            <div class="asignacion-info-body">
                                <p><strong>Vehículo:</strong> <span id="infoVehiculo"></span></p>
                                <p><strong>Conductor:</strong> <span id="infoConductor"></span></p>
                                <p><strong>Ruta:</strong> <span id="infoRuta"></span></p>
                            </div>
                        </div>

                        <!-- Toggle: Trayecto de regreso (visible solo al encontrar asignación) -->
                        <div id="retornoSection" class="retorno-toggle-row" style="display: none;">
                            <span class="retorno-label">Es trayecto de regreso</span>
                            <label class="switch-ios">
                                <input type="checkbox" id="esRetorno" name="es_retorno">
                                <span class="slider-ios"></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="tipoIncidente">Tipo de Incidente *</label>
                            <select id="tipoIncidente" name="tipoIncidente" required>
                                <option value="">Seleccionar tipo</option>
                                <?php foreach($TIPOS_INCIDENCIA as $key => $val): ?>
                                    <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($val); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fechaIncidente">Fecha y Hora *</label>
                            <input type="datetime-local" id="fechaIncidente" name="fechaIncidente" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción del Incidente *</label>
                            <textarea id="descripcion" name="descripcion" placeholder="Describa detalladamente el incidente..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="gravedad">Nivel de Gravedad</label>
                            <select id="gravedad" name="gravedad">
                                <?php foreach($NIVELES_GRAVEDAD as $key => $val): ?>
                                    <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $key === 'media' ? 'selected' : ''; ?>><?php echo htmlspecialchars($val); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn-submit">Generar reporte</button>
                    </form>

                    <!-- Modal editar reporte -->
                    <div class="modal-overlay" id="editModal">
                        <div class="modal-container">
                            <div class="modal-header">
                                <h3>Editar Reporte</h3>
                                <button type="button" class="modal-close" id="editClose">&times;</button>
                            </div>
                            <form id="editForm">
                                <input type="hidden" id="edit_id" name="id">
                                <div class="modal-body">
                                    <div>
                                        <div class="modal-form-group">
                                            <label for="edit_vehiculo">Vehículo *</label>
                                            <select id="edit_vehiculo" name="vehiculo" required>
                                                <option value="">Seleccionar vehículo</option>
                                                <?php
                                                $r = $conexion->query("SELECT id_vehiculo, placa, modelo FROM vehiculos ORDER BY placa");
                                                while($v = $r->fetch_assoc()){
                                                    echo "<option value='{$v['id_vehiculo']}'>{$v['placa']} - {$v['modelo']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="modal-form-group">
                                            <label for="edit_conductor">Conductor *</label>
                                            <select id="edit_conductor" name="conductor" required>
                                                <option value="">Seleccionar conductor</option>
                                                <?php
                                                $r = $conexion->query("SELECT rfc_conductor, nombre FROM conductores ORDER BY nombre");
                                                while($c = $r->fetch_assoc()){
                                                    echo "<option value='{$c['rfc_conductor']}'>{$c['nombre']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="modal-form-group">
                                            <label for="edit_ruta">Ruta *</label>
                                            <select id="edit_ruta" name="ruta" required>
                                                <option value="">Seleccionar ruta</option>
                                                <?php
                                                $r = $conexion->query("SELECT id_ruta, nombre FROM rutas ORDER BY nombre");
                                                while($ru = $r->fetch_assoc()){
                                                    echo "<option value='{$ru['id_ruta']}'>{$ru['nombre']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="modal-form-group">
                                            <label for="edit_tipoIncidente">Tipo de Incidente *</label>
                                            <select id="edit_tipoIncidente" name="tipoIncidente" required>
                                                <option value="">Seleccionar tipo</option>
                                                <?php foreach($TIPOS_INCIDENCIA as $key => $val): ?>
                                                    <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($val); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="modal-form-group">
                                            <label for="edit_fechaIncidente">Fecha y Hora *</label>
                                            <input type="datetime-local" id="edit_fechaIncidente" name="fechaIncidente" required>
                                        </div>

                                        <div class="modal-form-group">
                                            <label for="edit_descripcion">Descripción *</label>
                                            <textarea id="edit_descripcion" name="descripcion" required></textarea>
                                        </div>

                                        <div class="modal-form-group">
                                            <label for="edit_gravedad">Gravedad</label>
                                            <select id="edit_gravedad" name="gravedad">
                                                <?php foreach($NIVELES_GRAVEDAD as $key => $val): ?>
                                                    <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($val); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="modal-form-group">
                                            <label for="edit_estado">Estado</label>
                                            <select id="edit_estado" name="estado">
                                                <option value="pendiente">Pendiente</option>
                                                <option value="en-proceso">En Proceso</option>
                                                <option value="resuelto">Resuelto</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" id="editCancel" class="modal-btn modal-btn-cancel">Cancelar</button>
                                    <button type="submit" id="editSave" class="modal-btn modal-btn-save">Guardar cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha contenedora -->
                <div class="right-column">
                    <!-- Lista de reportes existentes -->
                <div class="reports-list-container">
                    <div class="reports-list-header">
                        <h3>Reportes recientes</h3>
                        <div class="filters">
                            <div class="filter-group">
                                <label>Filtrar por:</label>
                                <select class="filter-select" id="filterStatus" onchange="filterReports()">
                                    <option value="todos">Todos (Activos)</option>
                                    <option value="pendiente">Pendientes</option>
                                    <option value="en-proceso">En Proceso</option>
                                    <option value="resuelto">Resueltos</option>
                                    <option value="archivados">Archivados</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="compact-stats">
                        <div class="compact-stat">
                            <span class="compact-label">Total</span>
                            <span class="compact-value val-total" id="totalReports">0</span>
                        </div>
                        <div class="compact-stat">
                            <span class="compact-label">Pendientes</span>
                            <span class="compact-value val-orange" id="pendingReports">0</span>
                        </div>
                        <div class="compact-stat">
                            <span class="compact-label">Proceso</span>
                            <span class="compact-value val-blue" id="inProgressReports">0</span>
                        </div>
                        <div class="compact-stat">
                            <span class="compact-label">Resueltos</span>
                            <span class="compact-value val-green" id="resolvedReports">0</span>
                        </div>
                    </div>

                    <div class="reports-grid" id="reportsList">
                        <!-- Los reportes se cargarán aquí dinámicamente -->
                    </div>
                </div>
                </div> <!-- Fin de right-column -->
            </div>
        </section>
    </main>
</div>

<script>
    // Cargar reportes desde PHP
    const reportes = [
        <?php 
        if ($result_reportes && $result_reportes->num_rows > 0) {
            while($row = $result_reportes->fetch_assoc()) {
            echo "{
                    id: " . $row['id'] . ",
                    id_vehiculo: " . (int)$row['id_vehiculo'] . ",
                    rfc_conductor: \"" . addslashes($row['rfc_conductor']) . "\",
                    id_ruta: " . (int)$row['id_ruta'] . ",
                    vehiculo: \"" . addslashes($row['vehiculo_placa'] . " - " . $row['vehiculo_modelo']) . "\",
                    conductor: \"" . addslashes($row['conductor_nombre']) . "\",
                    ruta: \"" . addslashes($row['ruta_nombre']) . "\",
                    tipo: \"" . addslashes($row['tipo_incidente']) . "\",
                    tipoTexto: \"" . addslashes(ucfirst($row['tipo_incidente'])) . "\",
                    fecha: \"" . addslashes($row['fecha_incidente']) . "\",
                    descripcion: \"" . addslashes($row['descripcion']) . "\",
                    gravedad: \"" . addslashes($row['gravedad']) . "\",
                    status: \"" . addslashes($row['estado']) . "\",
                    archivado: " . (int)$row['archivado'] . "
                },";
            }
        }
        ?>
    ];

    // Cargar reportes al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        filterReports();
        updateStats(reportes);

        // Inyectar botón "Generar resumen" en el topbar móvil
        const mobileRight = document.querySelector('.mobile-topbar-right');
        if (mobileRight) {
            const btn = document.createElement('button');
            btn.className = 'notification-bell';
            btn.innerHTML = '<span class="material-icons">summarize</span>';
            btn.title = 'Generar resumen';
            btn.onclick = openSummaryModal;
            mobileRight.insertBefore(btn, mobileRight.firstChild);
        }
    });

    // Función para cargar reportes en la lista
    function loadReports(reports) {
        const reportsList = document.getElementById('reportsList');
        reportsList.innerHTML = '';

        reports.forEach(report => {
            const reportCard = document.createElement('div');
            reportCard.className = 'report-card';
            reportCard.innerHTML = `
                    <div class="report-header">
                        <h4 class="report-title">Incidente: ${report.tipoTexto}</h4>
                        <span class="report-status status-${report.status}">
                            ${getStatusText(report.status)}
                        </span>
                    </div>
                    <div class="report-meta">
                        <span>Vehículo: ${report.vehiculo}</span>
                        <span>Conductor: ${report.conductor}</span>
                    </div>
                    <div class="report-meta">
                         <span>Ruta: ${report.ruta}</span>
                    </div>
                    <div class="report-meta">
                        <span>${formatDate(report.fecha)}</span>
                        <span>Gravedad: ${report.gravedad}</span>
                    </div>
                    <div class="report-description">${report.descripcion}</div>
                    <div class="report-actions">
                        <button class="btn-action-small btn-edit" onclick="editReport(${report.id})" ${report.archivado ? 'style="display:none"' : ''}>
                            <span class="material-icons">edit_square</span> Editar
                        </button>
                        <button class="btn-action-small btn-archive" onclick="toggleArchiveReport(${report.id}, ${report.archivado})">
                            <span class="material-icons">archive</span> ${report.archivado ? 'Desarchivar' : 'Archivar'}
                        </button>
                        <button class="btn-action-small btn-delete" onclick="deleteReport(${report.id})">
                            <span class="material-icons">delete_outline</span> Eliminar
                        </button>
                    </div>
                `;
            reportsList.appendChild(reportCard);
        });
    }

    // Función para filtrar reportes segun los siguientes estados:
    // Pendiente
    // En Proceso
    // Resuelto
    function filterReports() {
        const filterValue = document.getElementById('filterStatus').value;
        let filteredReports;

        if (filterValue === 'archivados') {
            filteredReports = reportes.filter(report => report.archivado === 1);
        } else {
            const activeReports = reportes.filter(report => report.archivado === 0);
            if (filterValue === 'todos') {
                filteredReports = activeReports;
            } else if (filterValue === 'pendiente') {
                filteredReports = activeReports.filter(report => report.status === 'pendiente');
            } else if (filterValue === 'en-proceso') {
                filteredReports = activeReports.filter(report => report.status === 'en-proceso');
            } else if (filterValue === 'resuelto') {
                filteredReports = activeReports.filter(report => report.status === 'resuelto');
            } else {
                filteredReports = activeReports;
            }
        }

        loadReports(filteredReports);
    }

    // Variables para almacenar datos temporales de la asignación
    let currentAsignacionId = null;
    let currentAsignacionData = null;
    const idUsuarioActual = <?php echo isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0; ?>;

    // Actualiza el texto de ruta según el estado del toggle de retorno
    function updateRutaDisplay() {
        if (!currentAsignacionData) return;
        const asig = currentAsignacionData;
        const isRetorno = document.getElementById('esRetorno').checked;
        if (isRetorno) {
            const texto = asig.ruta_retorno_nombre
                ? asig.ruta_retorno_nombre
                : asig.ruta_nombre;
            document.getElementById('infoRuta').textContent = texto;
        } else {
            document.getElementById('infoRuta').textContent = asig.ruta_nombre;
        }
    }

    // Listener del toggle de retorno
    document.getElementById('esRetorno').addEventListener('change', updateRutaDisplay);

    // Escuchar cambios en el input de placa
    const inputPlaca = document.getElementById('placa');
    if (inputPlaca) {
        inputPlaca.addEventListener('blur', function() {
            const placaVal = this.value.trim().toUpperCase();
            this.value = placaVal;
            const placaError = document.getElementById('placaError');
            const dataContainer = document.getElementById('datosAsignacion');
            const retornoSection = document.getElementById('retornoSection');
            
            if (!placaVal) {
                dataContainer.style.display = 'none';
                retornoSection.style.display = 'none';
                placaError.style.display = 'none';
                currentAsignacionId = null;
                currentAsignacionData = null;
                document.getElementById('esRetorno').checked = false;
                return;
            }

            fetch(`../../api/reportes_api.php?action=get_assignment_data&placa=${encodeURIComponent(placaVal)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        placaError.style.display = 'none';
                        currentAsignacionData = data.data;
                        currentAsignacionId = data.data.id_asignacion;

                        document.getElementById('infoVehiculo').textContent = `${data.data.vehiculo_placa} - ${data.data.vehiculo_modelo}`;
                        document.getElementById('infoConductor').textContent = data.data.conductor_nombre;
                        document.getElementById('esRetorno').checked = false;
                        updateRutaDisplay();

                        dataContainer.style.display = 'block';
                        retornoSection.style.display = 'flex';
                    } else {
                        placaError.style.display = 'block';
                        placaError.textContent = data.error || 'No se encontró asignación para esta placa.';
                        dataContainer.style.display = 'none';
                        retornoSection.style.display = 'none';
                        currentAsignacionId = null;
                        currentAsignacionData = null;
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    placaError.style.display = 'block';
                    placaError.textContent = 'Error de conexión al buscar placa.';
                    dataContainer.style.display = 'none';
                    retornoSection.style.display = 'none';
                    currentAsignacionId = null;
                    currentAsignacionData = null;
                });
        });
    }

    // Función para manejar el envío del formulario con AJAX (Nuevo reporte)
    document.getElementById('incidentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const placaVal = document.getElementById('placa').value.trim().toUpperCase();
        if (!placaVal || !currentAsignacionId) {
            showNotification('Por favor, ingrese una placa válida y verifique sus datos.', 'error');
            return;
        }

        const fechaIncidente = document.getElementById('fechaIncidente');
        if (!fechaIncidente.value) {
            showNotification('Por favor, seleccione la fecha y hora del incidente', 'error');
            return;
        }

        let fechaHr = fechaIncidente.value;
        if (fechaHr.length === 16) { // YYYY-MM-DDTHH:MM
            fechaHr += ':00';
        }
        fechaHr = fechaHr.replace('T', ' ');

        // Deshabilitar botón submit
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';

        const payload = {
            id_usuario: idUsuarioActual,
            placa: placaVal,
            es_retorno: document.getElementById('esRetorno').checked,
            tipo_incidente: document.getElementById('tipoIncidente').value,
            fecha_hora: fechaHr,
            descripcion: document.getElementById('descripcion').value,
            gravedad: document.getElementById('gravedad').value
        };

        fetch('../../api/reportes_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;

            if (data.success) {
                // Limpiar formulario y ocultar contenedor
                document.getElementById('incidentForm').reset();
                document.getElementById('datosAsignacion').style.display = 'none';
                document.getElementById('retornoSection').style.display = 'none';
                document.getElementById('esRetorno').checked = false;
                currentAsignacionId = null;
                currentAsignacionData = null;
                
                // Mostrar notificación
                showNotification(data.message || 'Reporte guardado exitosamente', 'info');

                // Recargar la página después de generar el reporte
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showNotification(data.error || data.message || 'Error al guardar el reporte', 'error');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            console.error('Error:', error);
            showNotification('Error de conexión: ' + error.message, 'error');
        });
    });

    // Funciones auxiliares
    function getStatusText(status) {
        const statusMap = {
            'pendiente': 'Pendiente',
            'en-proceso': 'En Proceso',
            'resuelto': 'Resuelto'
        };
        return statusMap[status] || status;
    }

    // Antigua función para la fecha de los reportes
    /*
    function formatDate(dateTime) {
        const date = new Date(dateTime);
        return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
    }
    */

    // Nueva función para manejar los casos de las fechas
    function formatDate(dateTime) {
        try {
            const date = new Date(dateTime);
            if (isNaN(date.getTime())) {
                return 'Fecha inválida';
            }
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }) + ' ' + date.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateTime;
        }
    }

    function updateStats(reports) {
        // Solo contar los reportes según el filtro actual
        const filterValue = document.getElementById('filterStatus').value;
        const visibleReports = filterValue === 'archivados' ? reports.filter(r => r.archivado === 1) : reports.filter(r => r.archivado === 0);
        
        document.getElementById('totalReports').textContent = visibleReports.length;
        document.getElementById('pendingReports').textContent = visibleReports.filter(r => r.status === 'pendiente').length;
        document.getElementById('inProgressReports').textContent = visibleReports.filter(r => r.status === 'en-proceso').length;
        document.getElementById('resolvedReports').textContent = visibleReports.filter(r => r.status === 'resuelto').length;
    }

    function viewReport(id) {
        alert(`Viendo reporte ${id}`);
        // Implementar vista detallada del reporte
    }

    function editReport(id) {
        alert(`Editando reporte ${id}`);
        // Implementar edición del reporte
    }

    /**
     * Función para eliminar un reporte
     * 
     * Elimina un reporte por id y actualiza la UI.
     * @param {number} id - ID del reporte.
     * Comportamiento:
     *  - Muestra confirm dialog.
     *  - Envía POST a '../controllers/delete/delete_reportes.php' con FormData {id}.
     *  - Espera JSON { success: boolean, message: string }.
     *  - Si success true: elimina la tarjeta y actualiza estadísticas.
     *  - Si success false o error de red: restaura UI y muestra notificación.
     */
    function deleteReport(id) {
        // Mostrar modal de confirmación
        const confirmDelete = confirm('¿Está seguro de que desea eliminar este reporte?\n\n⚠️ Esta acción no se puede deshacer.');

        if (!confirmDelete) {
            return;
        }

        // Encontrar el elemento del reporte para efectos visuales
        const reportCard = document.querySelector(`.report-card [onclick="deleteReport(${id})"]`)?.closest('.report-card');
        const deleteBtn = document.querySelector(`button[onclick="deleteReport(${id})"]`);

        if (reportCard) {
            // Efecto visual de eliminación
            reportCard.style.transition = 'all 0.3s ease';
            reportCard.style.opacity = '0.5';
            reportCard.style.transform = 'scale(0.98)';
        }

        if (deleteBtn) {
            deleteBtn.classList.add('loading');
            deleteBtn.disabled = true;
            deleteBtn.textContent = '';
        }

        // Crear y enviar solicitud
        const formData = new FormData();
        formData.append('id', id);

        fetch('../../controllers/delete/delete_reportes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error de red');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Encontrar y eliminar del array
                const reportIndex = reportes.findIndex(r => r.id == id);
                if (reportIndex !== -1) {
                    // Efecto visual de eliminación exitosa
                    if (reportCard) {
                        reportCard.style.opacity = '0';
                        reportCard.style.transform = 'scale(0.95)';
                        reportCard.style.height = '0';
                        reportCard.style.margin = '0';
                        reportCard.style.padding = '0';
                        reportCard.style.border = '0';

                        // Eliminar después de la animación
                        setTimeout(() => {
                            reportes.splice(reportIndex, 1);

                            // Actualizar vista
                            const currentFilter = document.getElementById('filterStatus').value;
                            if (currentFilter === 'todos') {
                                loadReports(reportes);
                            } else {
                                filterReports();
                            }

                            updateStats(reportes);

                            // Mostrar mensaje de éxito
                            showNotification('Reporte eliminado exitosamente', 'error');
                        }, 300);
                    } else {
                        // Si no hay elemento visual, solo actualizar
                        reportes.splice(reportIndex, 1);
                        const currentFilter = document.getElementById('filterStatus').value;
                        if (currentFilter === 'todos') {
                            loadReports(reportes);
                        } else {
                            filterReports();
                        }
                        updateStats(reportes);
                        showNotification('Reporte eliminado exitosamente', 'error');
                    }
                }
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error:', error);

            // Restaurar apariencia
            if (reportCard) {
                reportCard.style.opacity = '1';
                reportCard.style.transform = '';
            }

            if (deleteBtn) {
                deleteBtn.classList.remove('loading');
                deleteBtn.disabled = false;
                deleteBtn.textContent = 'Eliminar';
            }

            showNotification('Error al eliminar el reporte: ' + error.message, 'error');
        });
    }

    function toggleArchiveReport(id, currentArchivadoStatus) {
        const flagTarget = currentArchivadoStatus ? 0 : 1;
        const actionStr = currentArchivadoStatus ? 'desarchivar' : 'archivar';
        const confirmAction = confirm(`¿Está seguro de que desea ${actionStr} este reporte?`);
        
        if (!confirmAction) return;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('archivado', flagTarget);

        fetch('../../controllers/update/archivar_reporte.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const reportIndex = reportes.findIndex(r => r.id == id);
                if (reportIndex !== -1) {
                    reportes[reportIndex].archivado = flagTarget;
                    filterReports();
                    updateStats(reportes);
                    showNotification(data.message, 'info');
                }
            } else {
                showNotification(data.message || `Error al ${actionStr}`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(`Error de conexión al ${actionStr}`, 'error');
        });
    }

    // Función para mostrar notificaciones (opcional) ya es parte del archivo utils


    // Cerrar sidebar + modal de edición con tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeEditModal();
        }
    });


    // Marcar enlace activo según la página actual
    document.addEventListener('DOMContentLoaded', function () {
        const currentFile = window.location.pathname.split('/').pop() || 'index.php';
        document.querySelectorAll('.sidebar nav ul li a').forEach(link => {
            const linkFile = link.getAttribute('href').split('/').pop();
            if (linkFile === currentFile) {
                link.classList.add('nav-active');
            }
        });
    });

    // --- funciones para editar ---
    function openEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('active');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('active');
    }

    // Cerrar modal con el botón X
    document.getElementById('editClose').addEventListener('click', function(){
        closeEditModal();
    });

    // Abrir modal y prefilling
    function editReport(id) {
        const report = reportes.find(r => r.id == id);
        if (!report) {
            showNotification('Reporte no encontrado', 'error');
            return;
        }

        // set values (si no tienes id_vehiculo en el objeto report, puede venir del backend)
        document.getElementById('edit_id').value = report.id;
        if (report.id_vehiculo) document.getElementById('edit_vehiculo').value = report.id_vehiculo;
        if (report.rfc_conductor) document.getElementById('edit_conductor').value = report.rfc_conductor;
        if (report.id_ruta) document.getElementById('edit_ruta').value = report.id_ruta;
        document.getElementById('edit_tipoIncidente').value = report.tipo || report.tipo_incidente || '';
        // fecha: convertir a formato compatible con datetime-local (yyyy-mm-ddThh:mm)
        if (report.fecha) {
            const d = new Date(report.fecha);
            if (!isNaN(d.getTime())) {
                const pad = n => n.toString().padStart(2,'0');
                const dt = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                document.getElementById('edit_fechaIncidente').value = dt;
            } else {
                document.getElementById('edit_fechaIncidente').value = '';
            }
        } else {
            document.getElementById('edit_fechaIncidente').value = '';
        }
        document.getElementById('edit_descripcion').value = report.descripcion || '';
        document.getElementById('edit_gravedad').value = report.gravedad || 'media';
        // Estado del reporte (pendiente, en-proceso, resuelto)
        if (document.getElementById('edit_estado')) {
            document.getElementById('edit_estado').value = report.status || report.estado || 'pendiente';
        }

        openEditModal();
    }

    // submit del formulario de edición
    document.getElementById('editForm').addEventListener('submit', function(e){
        e.preventDefault();
        const saveBtn = document.getElementById('editSave');
        saveBtn.disabled = true;
        saveBtn.classList.add('loading');

        const formData = new FormData(this);
        // enviar a endpoint
        fetch('../../controllers/update/update_reportes.php', {
            method: 'POST',
            body: formData
        })
        .then(resp => resp.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.classList.remove('loading');
            if (data.success) {
                // actualizar array reportes en memoria y UI
                const id = formData.get('id');
                const idx = reportes.findIndex(r => r.id == id);
                if (idx !== -1) {
                    const vehSelect = document.getElementById('edit_vehiculo');
                    const condSelect = document.getElementById('edit_conductor');
                    const rutaSelect = document.getElementById('edit_ruta');

                    // actualizar campos visibles en array
                    reportes[idx].vehiculo = vehSelect.selectedOptions[0].text;
                    reportes[idx].conductor = condSelect.selectedOptions[0].text;
                    reportes[idx].ruta = rutaSelect.selectedOptions[0].text || reportes[idx].ruta;
                    reportes[idx].tipo = document.getElementById('edit_tipoIncidente').value;
                    reportes[idx].fecha = document.getElementById('edit_fechaIncidente').value;
                    reportes[idx].descripcion = document.getElementById('edit_descripcion').value;
                    reportes[idx].gravedad = document.getElementById('edit_gravedad').value;
                    // actualizar estado
                    reportes[idx].status = formData.get('estado') || reportes[idx].status;

                    // además mantener ids si los necesitas
                    reportes[idx].id_vehiculo = parseInt(formData.get('vehiculo'));
                    reportes[idx].rfc_conductor = formData.get('conductor');
                    reportes[idx].id_ruta = parseInt(formData.get('ruta'));
                }

                // refrescar lista y stats
                const currentFilter = document.getElementById('filterStatus').value;
                if (currentFilter === 'todos') {
                    loadReports(reportes);
                } else {
                    filterReports();
                }
                updateStats(reportes);

                closeEditModal();
                showNotification(data.message || 'Reporte actualizado', 'success');
            } else {
                showNotification(data.message || 'Error al actualizar', 'error');
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.classList.remove('loading');
            console.error(err);
            showNotification('Error de red al actualizar', 'error');
        });
    });

    // cancelar edición
    document.getElementById('editCancel').addEventListener('click', function(){
        closeEditModal();
    });

    // Fin de lógica de reportes
</script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>

    <?php require_once __DIR__ . '/../../components/report_summary_modal.php'; ?>
</body>
</html>
