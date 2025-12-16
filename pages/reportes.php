<?php
require_once '../config/conexion_bd.php';

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener y sanitizar los datos del formulario
        $id_vehiculo = $conexion->real_escape_string($_POST['vehiculo']);
        $rfc_conductor = $conexion->real_escape_string($_POST['conductor']);
        $id_ruta = $conexion->real_escape_string($_POST['ruta']);
        $tipo_incidente = $conexion->real_escape_string($_POST['tipoIncidente']);
        $fecha_incidente = $conexion->real_escape_string($_POST['fechaIncidente']);
        $descripcion = $conexion->real_escape_string($_POST['descripcion']);
        $gravedad = $conexion->real_escape_string($_POST['gravedad']);

        // Preparar la consulta SQL
        $sql = "INSERT INTO reportes (id_vehiculo, rfc_conductor, id_ruta, tipo_incidente, 
                                    fecha_incidente, descripcion, gravedad) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Preparar y ejecutar la consulta
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("issssss", 
            $id_vehiculo, 
            $rfc_conductor, 
            $id_ruta, 
            $tipo_incidente, 
            $fecha_incidente, 
            $descripcion, 
            $gravedad
        );

        if ($stmt->execute()) {
            echo "<script>
                    alert('Reporte guardado exitosamente');
                    window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                  </script>";
        } else {
            throw new Exception("Error al guardar el reporte: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo "<script>
                alert('Error: " . str_replace("'", "\\'", $e->getMessage()) . "');
              </script>";
    }
}

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
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Los estilos permanecen igual */
        .reports-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .report-form-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .reports-list-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            border-radius: 8px;
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
            max-height: 500px;
            overflow-y: auto;
        }

        .report-card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .report-card:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #3b82f6;
            margin: 10px 0;
        }

        .stat-label {
            color: #64748b;
            font-size: 14px;
        }

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
    </style>
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
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">
                </div>
            </div>
        </div>
    </div>

    <!-- Menú Lateral -->
    <aside id="sidebar" class="sidebar">
        <!-- Botón de Cerrar para Móvil -->
        <button class="sidebar-close" onclick="closeSidebar()">&times;</button>

        <div class="logo">
            <img src="../assets/images/logo.png" alt="Logo de GoWay" class="logo-img">
            <h1>GoWay</h1>
        </div>
        <nav>
            <ul>
                <li>
                    <a href="../index.php">
                        <img src="../assets/images/icons/icon_dashboard.png" alt="Dashboard" class="icon">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="rutas.php">
                        <img src="../assets/images/icons/icon_rutas.png" alt="Rutas" class="icon">
                        <span>Rutas</span>
                    </a>
                </li>
                <li>
                    <a href="horarios.php">
                        <img src="../assets/images/icons/icon_horarios.png" alt="Horarios" class="icon">
                        <span>Horarios</span>
                    </a>
                </li>
                <li>
                    <a href="vehiculos.php">
                        <img src="../assets/images/icons/icon_vehiculos.png" alt="Vehículos" class="icon">
                        <span>Vehículos</span>
                    </a>
                </li>
                <li>
                    <a href="conductores.php">
                        <img src="../assets/images/icons/icon_conductores.png" alt="Conductores" class="icon">
                        <span>Conductores</span>
                    </a>
                </li>
                <li>
                    <a href="empresas.php">
                        <img src="../assets/images/icons/icon_empresas.png" alt="Empresas" class="icon">
                        <span>Empresas</span>
                    </a>
                </li>
                <li>
                    <a href="checadores.php">
                        <img src="../assets/images/icons/icon_checadores.png" alt="Checadores" class="icon">
                        <span>Checadores</span>
                    </a>
                </li>
                <li>
                    <a href="paradas.php">
                        <img src="../assets/images/icons/icon_paradas.png" alt="Paradas" class="icon">
                        <span>Asignaciones</span>
                    </a>
                </li>
                <li>
                    <a href="usuarios.php">
                        <img src="../assets/images/icons/icon_usuarios.png" alt="Usuarios" class="icon">
                        <span>Usuarios</span>
                    </a>
                </li>
                <li>
                    <a href="reportes.php">
                        <img src="../assets/images/icons/icon_reportes.png" alt="Reportes" class="icon">
                        <span>Reportes</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Botón de Cerrar Sesión -->
        <div class="logout-button">
            <a href="login.php" id="logout">
                <img src="../assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon">
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
                <span>Admin</span>
                <img src="../assets/images/icons/administrador.png" alt="Usuario">
            </div>
        </header>

        <section class="content">
            <!-- Estadísticas rápidas -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-label">Total Reportes</div>
                    <div class="stat-number" id="totalReports">12</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pendientes</div>
                    <div class="stat-number" id="pendingReports">5</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">En Proceso</div>
                    <div class="stat-number" id="inProgressReports">3</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Resueltos</div>
                    <div class="stat-number" id="resolvedReports">4</div>
                </div>
            </div>

            <div class="reports-container">
                <!-- Formulario para nuevo reporte -->
                <div class="report-form-container">
                    <h3>Nuevo Reporte de Incidente</h3>
                    <form id="incidentForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="form-group">
                            <label for="vehiculo">Vehículo *</label>
                            <select id="vehiculo" name="vehiculo" required>
                                <option value="">Seleccionar vehículo</option>
                                <?php
                                if ($result_vehiculos && $result_vehiculos->num_rows > 0) {
                                    while($row = $result_vehiculos->fetch_assoc()) {
                                        echo "<option value='" . $row['id_vehiculo'] . "'>" . $row['placa'] . " - " . $row['modelo'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="conductor">Conductor *</label>
                            <select id="conductor" name="conductor" required>
                                <option value="">Seleccionar conductor</option>
                                <?php
                                if ($result_conductores && $result_conductores->num_rows > 0) {
                                    while($row = $result_conductores->fetch_assoc()) {
                                        echo "<option value='" . $row['rfc_conductor'] . "'>" . $row['nombre'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ruta">Ruta *</label>
                            <select id="ruta" name="ruta" required>
                                <option value="">Seleccionar ruta</option>
                                <?php
                                if ($result_rutas && $result_rutas->num_rows > 0) {
                                    while($row = $result_rutas->fetch_assoc()) {
                                        echo "<option value='" . $row['id_ruta'] . "'>" . $row['nombre'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
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
                </div>

                <!-- Lista de reportes existentes -->
                <div class="reports-list-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3>Reportes Recientes</h3>
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

                    <div class="reports-grid" id="reportsList">
                        <!-- Los reportes se cargarán aquí dinámicamente -->
                    </div>
                </div>
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
                    vehiculo: \"" . $row['vehiculo_placa'] . " - " . $row['vehiculo_modelo'] . "\",
                    conductor: \"" . $row['conductor_nombre'] . "\",
                    tipo: \"" . $row['tipo_incidente'] . "\",
                    tipoTexto: \"" . ucfirst($row['tipo_incidente']) . "\",
                    fecha: \"" . $row['fecha_incidente'] . "\",
                    descripcion: \"" . addslashes($row['descripcion']) . "\",
                    gravedad: \"" . $row['gravedad'] . "\",
                    status: \"" . $row['estado'] . "\"
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
                        <h4 class="report-title">${report.tipoTexto}</h4>
                        <span class="report-status status-${report.status}">
                            ${getStatusText(report.status)}
                        </span>
                    </div>
                    <div class="report-meta">
                        <span>Vehículo: ${report.vehiculo}</span>
                        <span>Conductor: ${report.conductor}</span>
                    </div>
                    <div class="report-meta">
                        <span>${formatDate(report.fecha)}</span>
                        <span>Gravedad: ${report.gravedad}</span>
                    </div>
                    <div class="report-description">${report.descripcion}</div>
                    <div class="report-actions">
                        <button class="btn-action-small btn-view" onclick="viewReport(${report.id})">Ver</button>
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

    // Función para manejar el envío del formulario
    document.getElementById('incidentForm').addEventListener('submit', function(e) {
        // No prevenimos el evento submit para permitir el envío del formulario
        const fechaIncidente = document.getElementById('fechaIncidente');
        if (!fechaIncidente.value) {
            e.preventDefault();
            alert('Por favor, seleccione la fecha y hora del incidente');
            return;
        }
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

        fetch('../controllers/delete/delete_reportes.php', {
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
                            showNotification('Reporte eliminado exitosamente', 'success');
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
                        showNotification('Reporte eliminado exitosamente', 'success');
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

    // Función para mostrar notificaciones (opcional)
    function showNotification(message, type = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        // Estilos básicos
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-width: 350px;
        `;

        // Colores según tipo
        if (type === 'success') {
            notification.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        } else if (type === 'error') {
            notification.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
        } else {
            notification.style.background = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
        }

        // Animación
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Añadir al documento
        document.body.appendChild(notification);

        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);

        // Permitir cerrar manualmente
        notification.addEventListener('click', () => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        });
    }

    // Función para manejar el cambio de vehículo
    document.getElementById('vehiculo').addEventListener('change', function() {
        const vehiculoId = this.value;
        // Aquí puedes agregar lógica adicional si necesitas hacer algo cuando se selecciona un vehículo
    });

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
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
</script>
</body>
</html>