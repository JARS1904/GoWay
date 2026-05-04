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
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
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
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
            background-color: #fff;
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
        }

        .btn-submit {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 15px;
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
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-action-small .material-icons {
            font-size: 16px;
        }

        .btn-edit {
            background-color: #eff6ff;
            color: #2563eb;
        }
        .btn-edit:hover {
            background-color: #dbeafe;
        }

        .btn-delete {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .btn-delete:hover {
            background-color: #fecaca;
        }

        .btn-archive {
            background-color: #f1f5f9;
            color: #475569;
        }
        .btn-archive:hover {
            background-color: #e2e8f0;
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
                height: auto;
                gap: 16px;
            }

            .right-column {
                height: auto;
            }

            .report-form-container,
            .reports-list-container {
                height: auto;
                max-height: none;
                overflow-y: visible;
            }

            .reports-grid {
                overflow-y: visible;
                max-height: none;
            }

            .compact-stats {
                flex-wrap: wrap;
                gap: 8px;
            }

            .compact-stat {
                min-width: calc(50% - 4px);
            }

            .stats-cards {
                grid-template-columns: 1fr 1fr;
            }

            .filters {
                flex-direction: column;
            }

            .report-meta {
                flex-wrap: wrap;
                gap: 6px;
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
            font-family: inherit;
        }

        .modal-form-group input:focus,
        .modal-form-group select:focus,
        .modal-form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* ── Botón Generar resumen ── */
        .btn-summary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
            white-space: nowrap;
        }
        .btn-summary:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            transform: translateY(-2px);
        }
        .btn-summary .material-icons { font-size: 17px; }

        /* ── Modal de Resumen ── */
        #summaryModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        #summaryModal.active { display: flex; }

        .summary-container {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 780px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            animation: fadeSlideIn 0.3s ease;
        }
        @keyframes fadeSlideIn {
            from { opacity:0; transform: translateY(-20px); }
            to   { opacity:1; transform: translateY(0); }
        }

        .summary-header {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #fff;
            padding: 28px 32px 22px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .summary-header h2 { margin: 0 0 4px; font-size: 1.4rem; }
        .summary-header .summary-meta { font-size: 12px; opacity: 0.82; margin: 3px 0; }
        .summary-close-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
            border-radius: 50%;
            width: 32px; height: 32px;
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.18s, color 0.18s;
        }
        .summary-close-btn:hover { background: rgba(255,255,255,0.35); }

        .summary-body { padding: 28px 32px; overflow-y: auto; flex: 1; min-height: 0; }

        /* KPI cards */
        .summary-kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }
        .kpi-card {
            border-radius: 12px;
            padding: 18px 14px;
            text-align: center;
        }
        .kpi-card .kpi-val {
            font-size: 2rem;
            font-weight: 800;
            display: block;
            line-height: 1;
            margin-bottom: 6px;
        }
        .kpi-card .kpi-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.75;
        }
        .kpi-total    { background:#eff6ff; color:#1e40af; }
        .kpi-pending  { background:#fffbeb; color:#b45309; }
        .kpi-process  { background:#f0f9ff; color:#0369a1; }
        .kpi-resolved { background:#f0fdf4; color:#166534; }

        /* Secciones */
        .summary-section { margin-bottom: 26px; }
        .summary-section:last-child { margin-bottom: 0; }
        .summary-section h4 {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            margin: 0 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        /* Barras de gravedad */
        .gravedad-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        .gravedad-label {
            width: 68px;
            font-size: 13px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .gravedad-bar-wrap { flex: 1; background: #f1f5f9; border-radius: 99px; height: 10px; overflow: hidden; }
        .gravedad-bar { height: 10px; border-radius: 99px; transition: width 0.6s ease; }
        .bar-baja     { background: #22c55e; }
        .bar-media    { background: #f59e0b; }
        .bar-alta     { background: #ef4444; }
        .bar-critica  { background: #7c3aed; }
        .gravedad-count { font-size: 13px; font-weight: 700; color: #334155; min-width: 24px; text-align: right; }

        /* Tablas de resumen */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .summary-table th {
            text-align: left;
            padding: 8px 12px;
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .summary-table td {
            padding: 9px 12px;
            border-top: 1px solid #f1f5f9;
            color: #334155;
        }
        .summary-table tr:hover td { background: #f8fafc; }
        .badge-rank {
            display: inline-block;
            background: #e0e7ff;
            color: #4338ca;
            border-radius: 99px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
        }

        /* Footer del modal */
        .summary-footer {
            border-top: 1px solid #e2e8f0;
            padding: 18px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-radius: 0 0 16px 16px;
        }
        .summary-footer small { color: #94a3b8; font-size: 12px; }
        .btn-download-pdf {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #2962FF, #1a50e8);
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(41,98,255,.32);
        }
        .btn-download-pdf:hover { opacity:.92; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(41,98,255,.38); }
        .btn-download-pdf .material-icons { font-size: 17px; }

        /* Loading state del resumen */
        .summary-loading {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        .summary-loading .material-icons {
            font-size: 48px;
            animation: spin 1s linear infinite;
            color: #6366f1;
            display: block;
            margin-bottom: 12px;
        }

        /* (print is handled via popup window — no @media print needed here) */
    </style>
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
                <div class="header-notif-wrap" style="gap:12px">
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
                            <input type="text" id="placa" name="placa" placeholder="Ingrese la placa" required style="text-transform: uppercase;">
                            <small id="placaError" style="color: #ef4444; display: none; margin-top: 5px; font-weight: 500;">No se encontró asignación para esta placa.</small>
                        </div>
                        
                        <!-- Contenedor para mostrar los datos obtenidos automáticamente -->
                        <div id="datosAsignacion" style="display: none; background: #eff6ff; padding: 18px; border-radius: 12px; margin-bottom: 22px; border: 1px solid #bfdbfe; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.05);">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                                <span class="material-icons" style="color: #2563eb; font-size: 20px;">info</span>
                                <h4 style="margin: 0; color: #1d4ed8; font-size: 16px; font-weight: 700;">Asignación encontrada</h4>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <p style="margin: 0; font-size: 14.5px; color: #334155;"><strong style="color: #1e293b;">Vehículo:</strong> <span id="infoVehiculo"></span></p>
                                <p style="margin: 0; font-size: 14.5px; color: #334155;"><strong style="color: #1e293b;">Conductor:</strong> <span id="infoConductor"></span></p>
                                <p style="margin: 0; font-size: 14.5px; color: #334155;"><strong style="color: #1e293b;">Ruta:</strong> <span id="infoRuta"></span></p>
                            </div>
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
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="margin: 0;">Reportes recientes</h3>
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

    // ─────────────────────────────────────────────
    // RESUMEN EJECUTIVO
    // ─────────────────────────────────────────────
    const TIPO_LABELS = {
        accidente: 'Accidente',
        averia:    'Avería Mecánica',
        retraso:   'Retraso Significativo',
        cliente:   'Incidente con Cliente',
        otro:      'Otro'
    };

    function openSummaryModal() {
        document.getElementById('summaryModal').classList.add('active');
        // Resetear body a loading
        document.getElementById('summaryBody').innerHTML = `
            <div class="summary-loading">
                <span class="material-icons">sync</span>
                Generando resumen...
            </div>`;
        document.getElementById('summaryPeriodo').textContent  = 'Cargando período...';
        document.getElementById('summaryGenerado').textContent = 'Generado el: —';

        fetch('../../api/reportes_api.php?action=get_summary')
            .then(r => r.json())
            .then(data => {
                if (data.success) renderSummary(data);
                else throw new Error(data.error || 'Error al obtener datos');
            })
            .catch(err => {
                document.getElementById('summaryBody').innerHTML =
                    `<p style="color:#ef4444;padding:20px;">Error: ${err.message}</p>`;
            });
    }

    function closeSummaryModal() {
        document.getElementById('summaryModal').classList.remove('active');
    }

    // Cerrar con ESC
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeSummaryModal(); closeEditModal(); }
    });

    function renderSummary(data) {
        const t      = data.totales;
        const total  = parseInt(t.total)      || 0;
        const pend   = parseInt(t.pendientes) || 0;
        const proc   = parseInt(t.en_proceso) || 0;
        const resu   = parseInt(t.resueltos)  || 0;

        // Período
        const desde = data.rango_fechas?.desde ? new Date(data.rango_fechas.desde).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'}) : '—';
        const hasta = data.rango_fechas?.hasta ? new Date(data.rango_fechas.hasta).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'}) : '—';
        document.getElementById('summaryPeriodo').textContent  = `Período: ${desde} — ${hasta}`;

        const genTs = new Date(data.generado_en);
        document.getElementById('summaryGenerado').textContent =
            `Generado el: ${genTs.toLocaleDateString('es-MX',{day:'2-digit',month:'long',year:'numeric'})} a las ${genTs.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'})}`;

        // ── Barras de gravedad ──
        const gravedadOrder = ['critica','alta','media','baja'];
        const gMap = {};
        (data.por_gravedad || []).forEach(g => gMap[g.gravedad] = parseInt(g.total));
        const maxG = Math.max(...Object.values(gMap), 1);
        const gravedadHTML = gravedadOrder.map(g => {
            const cnt  = gMap[g] || 0;
            const pct  = Math.round((cnt / maxG) * 100);
            const label = g === 'critica' ? 'Crítica' : g.charAt(0).toUpperCase() + g.slice(1);
            return `<div class="gravedad-row">
                <span class="gravedad-label">${label}</span>
                <div class="gravedad-bar-wrap">
                    <div class="gravedad-bar bar-${g}" style="width:${pct}%"></div>
                </div>
                <span class="gravedad-count">${cnt}</span>
            </div>`;
        }).join('');

        // ── Tabla tipos ──
        const tipoRows = (data.por_tipo || []).map((row, i) => `
            <tr>
                <td><span class="badge-rank">#${i+1}</span></td>
                <td>${TIPO_LABELS[row.tipo_incidente] || row.tipo_incidente}</td>
                <td><strong>${row.total}</strong></td>
                <td>${total > 0 ? Math.round((row.total/total)*100) : 0}%</td>
            </tr>`).join('') || '<tr><td colspan="4" style="text-align:center;color:#94a3b8">Sin datos</td></tr>';

        // ── Top conductores ──
        const condRows = (data.top_conductores || []).map((row, i) => `
            <tr>
                <td><span class="badge-rank">#${i+1}</span></td>
                <td>${row.nombre}</td>
                <td><strong>${row.total}</strong></td>
            </tr>`).join('') || '<tr><td colspan="3" style="text-align:center;color:#94a3b8">Sin datos</td></tr>';

        // ── Top rutas ──
        const rutaRows = (data.top_rutas || []).map((row, i) => `
            <tr>
                <td><span class="badge-rank">#${i+1}</span></td>
                <td>${row.nombre}</td>
                <td><strong>${row.total}</strong></td>
            </tr>`).join('') || '<tr><td colspan="3" style="text-align:center;color:#94a3b8">Sin datos</td></tr>';

        // ── Top vehículos ──
        const vehicRows = (data.top_vehiculos || []).map((row, i) => `
            <tr>
                <td><span class="badge-rank">#${i+1}</span></td>
                <td>${row.vehiculo}</td>
                <td><strong>${row.total}</strong></td>
            </tr>`).join('') || '<tr><td colspan="3" style="text-align:center;color:#94a3b8">Sin datos</td></tr>';

        // ── Días de la semana ──
        const maxDia = Math.max(...(data.por_dia_semana || []).map(d => d.total), 1);
        const diaColors = ['#6366f1','#3b82f6','#06b6d4','#10b981','#f59e0b','#ef4444','#8b5cf6'];
        const diasHTML = (data.por_dia_semana || []).map((d, i) => {
            const pct = Math.round((d.total / maxDia) * 100);
            return `<div class="gravedad-row">
                <span class="gravedad-label" style="width:36px;font-size:12px">${d.dia}</span>
                <div class="gravedad-bar-wrap">
                    <div class="gravedad-bar" style="width:${pct}%;background:${diaColors[i % diaColors.length]}"></div>
                </div>
                <span class="gravedad-count">${d.total}</span>
            </div>`;
        }).join('');

        document.getElementById('summaryBody').innerHTML = `
            <!-- KPIs -->
            <div class="summary-kpis">
                <div class="kpi-card kpi-total">
                    <span class="kpi-val">${total}</span>
                    <span class="kpi-label">Total</span>
                </div>
                <div class="kpi-card kpi-pending">
                    <span class="kpi-val">${pend}</span>
                    <span class="kpi-label">Pendientes</span>
                </div>
                <div class="kpi-card kpi-process">
                    <span class="kpi-val">${proc}</span>
                    <span class="kpi-label">En Proceso</span>
                </div>
                <div class="kpi-card kpi-resolved">
                    <span class="kpi-val">${resu}</span>
                    <span class="kpi-label">Resueltos</span>
                </div>
            </div>

            <!-- Gravedad -->
            <div class="summary-section">
                <h4>Por Nivel de Gravedad</h4>
                ${gravedadHTML}
            </div>

            <!-- Grid estructurado en filas para asegurar alineación horizontal -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px 24px; align-items:start;">
                <!-- Fila 1 -->
                <div class="summary-section" style="margin:0;">
                    <h4>Por Tipo de Incidente</h4>
                    <table class="summary-table">
                        <thead><tr><th>#</th><th>Tipo</th><th>Total</th><th>%</th></tr></thead>
                        <tbody>${tipoRows}</tbody>
                    </table>
                </div>
                <div class="summary-section" style="margin:0;">
                    <h4>Top Conductores</h4>
                    <table class="summary-table">
                        <thead><tr><th>#</th><th>Conductor</th><th>Reportes</th></tr></thead>
                        <tbody>${condRows}</tbody>
                    </table>
                </div>

                <!-- Fila 2 -->
                <div class="summary-section" style="margin:0;">
                    <h4>Top Vehículos</h4>
                    <table class="summary-table">
                        <thead><tr><th>#</th><th>Vehículo</th><th>Reportes</th></tr></thead>
                        <tbody>${vehicRows}</tbody>
                    </table>
                </div>
                <div class="summary-section" style="margin:0;">
                    <h4>Top Rutas</h4>
                    <table class="summary-table">
                        <thead><tr><th>#</th><th>Ruta</th><th>Reportes</th></tr></thead>
                        <tbody>${rutaRows}</tbody>
                    </table>
                </div>

                <!-- Fila 3 -->
                <div></div>
                <div class="summary-section" style="margin:0;">
                    <h4>Incidentes por Día</h4>
                    ${diasHTML}
                </div>
            </div>`;
    }

    function downloadSummaryPDF() {
        const generadoText = document.getElementById('summaryGenerado').textContent;
        const bodyHTML     = document.getElementById('summaryBody').innerHTML;

        const printCSS = `
            body { font-family: 'Inter', Arial, sans-serif; margin: 0; padding: 0; background:#fff; color:#333; }
            .print-header { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color:#fff; padding:28px 36px 22px; display:flex; align-items:center; gap:16px; print-color-adjust:exact; -webkit-print-color-adjust:exact; }
            .print-header img { width:44px; height:44px; object-fit:contain; }
            .print-header h2 { margin:0 0 4px; font-size:1.35rem; }
            .print-header p { margin:3px 0; font-size:12px; opacity:.85; }
            .print-body { padding:28px 36px; }
            .summary-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:28px; }
            .kpi-card { border-radius:12px; padding:18px 14px; text-align:center; }
            .kpi-card .kpi-val { font-size:2rem; font-weight:800; display:block; line-height:1; margin-bottom:6px; }
            .kpi-card .kpi-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; opacity:.75; }
            .kpi-total{background:#eff6ff;color:#1e40af;}.kpi-pending{background:#fffbeb;color:#b45309;}
            .kpi-process{background:#f0f9ff;color:#0369a1;}.kpi-resolved{background:#f0fdf4;color:#166534;}
            .summary-section { margin-bottom:24px; }
            .summary-section h4 { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#64748b; margin:0 0 12px; padding-bottom:8px; border-bottom:2px solid #e2e8f0; }
            .gravedad-row { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
            .gravedad-label { width:68px; font-size:13px; font-weight:600; text-transform:capitalize; }
            .gravedad-bar-wrap { flex:1; background:#f1f5f9; border-radius:99px; height:10px; overflow:hidden; }
            .gravedad-bar { height:10px; border-radius:99px; }
            .bar-baja{background:#22c55e;}.bar-media{background:#f59e0b;}.bar-alta{background:#ef4444;}.bar-critica{background:#7c3aed;}
            .gravedad-count { font-size:13px; font-weight:700; color:#334155; min-width:24px; text-align:right; }
            .summary-table { width:100%; border-collapse:collapse; font-size:13px; }
            .summary-table th { text-align:left; padding:8px 12px; background:#f8fafc; color:#64748b; font-weight:700; font-size:11px; text-transform:uppercase; }
            .summary-table td { padding:9px 12px; border-top:1px solid #f1f5f9; color:#334155; }
            .badge-rank { display:inline-block; background:#e0e7ff; color:#4338ca; border-radius:99px; padding:2px 8px; font-size:11px; font-weight:700; }
            .print-footer { border-top:1px solid #e2e8f0; padding:14px 36px; background:#f8fafc; font-size:12px; color:#94a3b8; }
            @media print { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        `;

        const win = window.open('', '_blank', 'width=900,height=700');
        win.document.write(`<!DOCTYPE html><html lang="es"><head>
            <meta charset="UTF-8">
            <title>Resumen ejecutivo de reportes - GoWay</title>
            <style>${printCSS}</style>
        </head><body>
            <div class="print-header">
                <img src="${window.location.origin}/GoWay/assets/images/logo_new.png" alt="GoWay">
                <div>
                    <h2>Resumen ejecutivo de reportes</h2>
                    <p>${generadoText}</p>
                </div>
            </div>
            <div class="print-body">${bodyHTML}</div>
            <div class="print-footer">GoWay - Sistema de Transporte Público</div>
        </body></html>`);
        win.document.close();
        win.onload = () => { win.focus(); win.print(); };
    }
</script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>

    <!-- ── Modal Resumen Ejecutivo ── -->
    <div id="summaryModal">
        <div class="summary-container" id="summaryPrintArea">
            <!-- Header -->
            <div class="summary-header">
                <div style="display:flex;align-items:center;gap:14px">
                    <img src="../../assets/images/logo_new.png" alt="GoWay" style="width:42px;height:42px;object-fit:contain;flex-shrink:0;">
                    <div>
                        <h2 style="margin:0 0 4px;font-size:1.35rem;">Resumen ejecutivo de reportes</h2>
                        <p class="summary-meta" id="summaryPeriodo" style="display:none"></p>
                        <p class="summary-meta" id="summaryGenerado">Generado el: —</p>
                    </div>
                </div>
                <button class="summary-close-btn" onclick="closeSummaryModal()">✕</button>
            </div>

            <!-- Body -->
            <div class="summary-body" id="summaryBody">
                <div class="summary-loading">
                    <span class="material-icons">sync</span>
                    Generando resumen...
                </div>
            </div>

            <!-- Footer -->
            <div class="summary-footer">
                <small>GoWay - Sistema de Transporte Público</small>
                <button class="btn-download-pdf" onclick="downloadSummaryPDF()">
                    <span class="material-icons">download</span>
                    Descargar PDF
                </button>
            </div>
        </div>
    </div>
</body>
</html>
