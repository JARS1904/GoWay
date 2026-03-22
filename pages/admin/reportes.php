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

// Obtener lista de vehículos con placa y modelo
$sql_vehiculos = "SELECT id_vehiculo, placa, modelo FROM vehiculos ORDER BY placa";
$result_vehiculos = $conexion->query($sql_vehiculos);

// Obtener lista de conductores
$sql_conductores = "SELECT rfc_conductor, nombre FROM conductores ORDER BY nombre";
$result_conductores = $conexion->query($sql_conductores);

// Obtener lista de rutas
$sql_rutas = "SELECT id_ruta, nombre FROM rutas ORDER BY nombre";
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
                 ORDER BY r.created_at DESC
                 LIMIT 10";
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
    <link rel="icon" href="../../assets/images/logo.png" type="image/png">
    <style>
        /* Los estilos permanecen igual */
        .reports-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            height: calc(100vh - 140px);
        }

        .right-column {
            display: flex;
            flex-direction: column;
            min-height: 0;
            height: 100%;
        }

        .report-form-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100%;
            box-sizing: border-box;
            overflow-y: auto;
        }

        .reports-list-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
            font-family: 'Arial', sans-serif;
        }

        .btn-submit {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .reports-grid {
            display: grid;
            gap: 15px;
            flex: 1;
            overflow-y: auto;
            min-height: 0;
            align-content: start;
        }

        .report-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            transition: background-color 0.2s ease;
        }

        .report-card:hover {
            background-color: #f1f5f9;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .report-title {
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .report-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pendiente {
            background: #fef3c7;
            color: #d97706;
        }

        .status-en-proceso {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-resuelto {
            background: #d1fae5;
            color: #065f46;
        }

        .report-meta {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .report-description {
            color: #475569;
            font-size: 14px;
            line-height: 1.4;
        }

        .report-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-action-small {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-view {
            background: #10b981;
            color: white;
        }

        .compact-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            justify-content: space-between;
        }

        .compact-stat {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            flex: 1;
            text-align: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }
        
        .compact-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.08);
        }

        .compact-label {
            display: block;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .compact-value {
            display: block;
            font-size: 1.4rem;
            font-weight: 800;
        }

        .val-total  { color: #3b82f6; }
        .val-orange { color: #f59e0b; }
        .val-blue   { color: #0ea5e9; }
        .val-green  { color: #10b981; }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .filter-select {
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .reports-container {
                grid-template-columns: 1fr;
            }

            .stats-cards {
                grid-template-columns: 1fr 1fr;
            }

            .filters {
                flex-direction: column;
            }
        }

        /* Estilos para botones deshabilitados */
        .btn-action-small:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Estilos para indicador de carga */
        .loading {
            position: relative;
            color: transparent !important;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Estilos adicionales para modal */
        .modal-form-group textarea {
            min-height: 110px;
            resize: vertical;
            font-family: 'Arial', sans-serif;
        }

        .modal-form-group input:focus,
        .modal-form-group select:focus,
        .modal-form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
    </style>
    <script src="../../assets/js/notifications.js"></script>
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
                <h1 class="mobile-page-title">Reportes</h1>
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
            <h2>Reportes de Incidentes</h2>
            <div class="user-info">
                <span><?php echo $_SESSION['nombre']; ?></span>
                <?php echo !empty($_SESSION['foto']) ? '<img src="../../assets/images/profiles/' . htmlspecialchars($_SESSION['foto']) . '" alt="Usuario" class="header-user-avatar">' : '<img src="../../assets/images/icons/administrador.png" alt="Usuario">'; ?>
            </div>
        </header>

        <section class="content">
            <div class="reports-container">
                <!-- Formulario para nuevo reporte -->
                <div class="report-form-container">
                    <h3>Nuevo Reporte de Incidente</h3>
                    <form id="incidentForm" method="POST" action="#">
                        <div class="form-group">
                            <label for="placa">Placa de la Unidad *</label>
                            <input type="text" id="placa" name="placa" placeholder="Ingrese la placa" required style="text-transform: uppercase;">
                            <small id="placaError" style="color: #ef4444; display: none; margin-top: 5px; font-weight: 500;">No se encontró asignación para esta placa.</small>
                        </div>
                        
                        <!-- Contenedor para mostrar los datos obtenidos automáticamente -->
                        <div id="datosAsignacion" style="display: none; background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                            <h4 style="margin-top: 0; margin-bottom: 10px; color: #3b82f6;">Asignación encontrada</h4>
                            <p style="margin: 5px 0; font-size: 14px; color: #475569;"><strong>Vehículo:</strong> <span id="infoVehiculo"></span></p>
                            <p style="margin: 5px 0; font-size: 14px; color: #475569;"><strong>Conductor:</strong> <span id="infoConductor"></span></p>
                            <p style="margin: 5px 0; font-size: 14px; color: #475569;"><strong>Ruta:</strong> <span id="infoRuta"></span></p>
                        </div>

                        <div class="form-group">
                            <label for="tipoIncidente">Tipo de Incidente *</label>
                            <select id="tipoIncidente" name="tipoIncidente" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="accidente">Accidente</option>
                                <option value="averia">Avería Mecánica</option>
                                <option value="retraso">Retraso Significativo</option>
                                <option value="cliente">Incidente con Cliente</option>
                                <option value="otro">Otro</option>
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
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="critica">Crítica</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-submit">Generar Reporte</button>
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
                                                <option value="accidente">Accidente</option>
                                                <option value="averia">Avería Mecánica</option>
                                                <option value="retraso">Retraso Significativo</option>
                                                <option value="cliente">Incidente con Cliente</option>
                                                <option value="otro">Otro</option>
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
                                                <option value="baja">Baja</option>
                                                <option value="media">Media</option>
                                                <option value="alta">Alta</option>
                                                <option value="critica">Crítica</option>
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
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="margin: 0;">Reportes Recientes</h3>
                        <div class="filters">
                            <div class="filter-group">
                                <label>Filtrar por:</label>
                                <select class="filter-select" id="filterStatus" onchange="filterReports()">
                                    <option value="todos">Todos</option>
                                    <option value="pendiente">Pendientes</option>
                                    <option value="en-proceso">En Proceso</option>
                                    <option value="resuelto">Resueltos</option>
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
                    status: \"" . addslashes($row['estado']) . "\"
                },";
            }
        }
        ?>
    ];

    // Cargar reportes al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        loadReports(reportes);
        updateStats(reportes);
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
                        <!--<button class="btn-action-small btn-view" onclick="viewReport(${report.id})">Ver</button>-->
                        <button class="btn-action-small btn-edit" onclick="editReport(${report.id})">Editar</button>
                        <button class="btn-action-small btn-delete" onclick="deleteReport(${report.id})">Eliminar</button>
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

        if (filterValue === 'todos') {
            filteredReports = reportes;
        } else if (filterValue === 'pendiente') {
            filteredReports = reportes.filter(report => report.status === 'pendiente');
        } else if (filterValue === 'en-proceso') {
            filteredReports = reportes.filter(report => report.status === 'en-proceso');
        } else if (filterValue === 'resuelto') {
            filteredReports = reportes.filter(report => report.status === 'resuelto');
        } else {
            filteredReports = reportes;
        }

        loadReports(filteredReports);
    }

    // Variables para almacenar datos temporales de la asignación
    let currentAsignacionId = null;
    const idUsuarioActual = <?php echo isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0; ?>;

    // Escuchar cambios en el input de placa
    const inputPlaca = document.getElementById('placa');
    if (inputPlaca) {
        inputPlaca.addEventListener('blur', function() {
            const placaVal = this.value.trim().toUpperCase();
            this.value = placaVal;
            const placaError = document.getElementById('placaError');
            const dataContainer = document.getElementById('datosAsignacion');
            
            if (!placaVal) {
                dataContainer.style.display = 'none';
                placaError.style.display = 'none';
                currentAsignacionId = null;
                return;
            }

            fetch(`../../api/reportes_api.php?action=get_assignment_data&placa=${encodeURIComponent(placaVal)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        placaError.style.display = 'none';
                        dataContainer.style.display = 'block';
                        document.getElementById('infoVehiculo').textContent = `${data.data.vehiculo_placa} - ${data.data.vehiculo_modelo}`;
                        document.getElementById('infoConductor').textContent = data.data.conductor_nombre;
                        document.getElementById('infoRuta').textContent = `${data.data.ruta_nombre} (${data.data.origen} - ${data.data.destino})`;
                        currentAsignacionId = data.data.id_asignacion;
                    } else {
                        placaError.style.display = 'block';
                        placaError.textContent = data.error || 'No se encontró asignación para esta placa.';
                        dataContainer.style.display = 'none';
                        currentAsignacionId = null;
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    placaError.style.display = 'block';
                    placaError.textContent = 'Error de conexión al buscar placa.';
                    dataContainer.style.display = 'none';
                    currentAsignacionId = null;
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
                currentAsignacionId = null;
                
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
        // Actualizar estadísticas con datos reales
        document.getElementById('totalReports').textContent = reports.length;
        document.getElementById('pendingReports').textContent = reports.filter(r => r.status === 'pendiente').length;
        document.getElementById('inProgressReports').textContent = reports.filter(r => r.status === 'en-proceso').length;
        document.getElementById('resolvedReports').textContent = reports.filter(r => r.status === 'resuelto').length;
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

    // Función para mostrar notificaciones (opcional) ya es parte del archivo utils

    // Funciones para el menú hamburguesa
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.querySelector('.toggle-btn');

        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');

        if (sidebar.classList.contains('active')) {
            toggleBtn.style.opacity = '0';
            toggleBtn.style.visibility = 'hidden';
        } else {
            toggleBtn.style.opacity = '1';
            toggleBtn.style.visibility = 'visible';
        }

        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.querySelector('.toggle-btn');

        sidebar.classList.remove('active');
        overlay.classList.remove('active');

        toggleBtn.style.opacity = '1';
        toggleBtn.style.visibility = 'visible';

        document.body.style.overflow = '';
    }

    document.querySelectorAll('.sidebar nav a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSidebar();
            closeEditModal();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            closeSidebar();
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
</script>
</body>
</html>
