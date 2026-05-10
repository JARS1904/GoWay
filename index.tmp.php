<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: pages/login.php');
    exit();
}
require_once 'config/conexion_bd.php';
require_once 'config/sync_session_foto.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Transporte Público</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="icon" href="assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Premium Dashboard Styles */
        :root {
            --primary-gradient: linear-gradient(135deg, #0660fe 0%, #3b82f6 100%);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --card-shadow: 0 10px 30px -10px rgba(6, 96, 254, 0.15);
            --hover-shadow: 0 20px 40px -10px rgba(6, 96, 254, 0.25);
        }

        .dashboard-welcome {
            background: var(--primary-gradient);
            border-radius: 24px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px -15px rgba(6, 96, 254, 0.4);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .dashboard-welcome::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .dashboard-welcome h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0 0 10px 0;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
        }

        .dashboard-welcome p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
            max-width: 600px;
            line-height: 1.5;
            position: relative;
            z-index: 2;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--hover-shadow);
            background: rgba(255, 255, 255, 0.9);
        }

        .stat-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(6, 96, 254, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-card-icon {
            transform: rotate(10deg) scale(1.1);
            background: var(--primary-gradient);
        }

        .stat-card:hover .stat-card-icon img {
            filter: brightness(0) invert(1);
        }

        .stat-card-content h3 {
            font-size: 0.9rem !important;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 5px 0;
            font-weight: 700;
        }

        .stat-number {
            font-size: 2.2rem !important;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            line-height: 1;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .chart-card {
            background: #ffffff;
            border-radius: 24px !important;
            padding: 28px !important;
            box-shadow: 0 10px 40px -15px rgba(0,0,0,0.08) !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            transition: all 0.3s ease;
        }

        .chart-card:hover {
            box-shadow: 0 20px 40px -15px rgba(6, 96, 254, 0.15) !important;
            transform: translateY(-4px);
        }

        .action-btn {
            background: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            box-shadow: var(--card-shadow);
            border: 1px solid #f1f5f9;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: #334155;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
            color: white;
            border-color: transparent;
        }

        .action-btn:hover::before {
            opacity: 1;
        }

        .action-btn > * {
            position: relative;
            z-index: 2;
        }

        .action-btn:hover .action-icon {
            filter: brightness(0) invert(1);
            transform: scale(1.1);
        }

        .action-icon {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $page_title  = 'Dashboard';
        $active_page = 'dashboard';
        $base_url    = '';
        require_once __DIR__ . '/components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Dashboard</h2>
                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">

                <!-- Sección de Bienvenida -->
                <div class="dashboard-welcome">
                    <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?> 👋</h1>
                    <p>Aquí puedes ver un resumen del estado general de tu sistema de transporte</p>
                </div>

                <!-- Grid de Estad├¡sticas -->
                <div class="stats-grid">
                    <?php
                    $conn = $conexion;

                    if ($conn->connect_error) {
                        die("Error de conexi├│n: " . $conn->connect_error);
                    }

                    $is_superadmin = ($_SESSION['rol'] == 1);
                    $rfc_empresa_session = isset($_SESSION['rfc_empresa']) ? $_SESSION['rfc_empresa'] : '';
                    
                    if ($is_superadmin) {
                        $sql = "SELECT 
                                (SELECT COUNT(*) FROM empresas) AS total_empresas,
                                (SELECT COUNT(*) FROM rutas) AS total_rutas,
                                (SELECT COUNT(*) FROM vehiculos) AS total_vehiculos,
                                (SELECT COUNT(*) FROM conductores) AS total_conductores,
                                (SELECT COUNT(*) FROM horarios) AS total_horarios,
                                (SELECT COUNT(*) FROM checadores) AS total_checadores";
                    } else {
                        // Filtro por empresa
                        $sql = "SELECT 
                                0 AS total_empresas,
                                (SELECT COUNT(*) FROM rutas WHERE rfc_empresa = '$rfc_empresa_session') AS total_rutas,
                                (SELECT COUNT(*) FROM vehiculos WHERE rfc_empresa = '$rfc_empresa_session') AS total_vehiculos,
                                (SELECT COUNT(*) FROM conductores WHERE rfc_empresa = '$rfc_empresa_session') AS total_conductores,
                                (SELECT COUNT(*) FROM horarios h JOIN rutas r ON h.id_ruta = r.id_ruta WHERE r.rfc_empresa = '$rfc_empresa_session') AS total_horarios,
                                (SELECT COUNT(*) FROM checadores WHERE rfc_empresa = '$rfc_empresa_session') AS total_checadores";
                    }
                    
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    ?>

                    <!-- Tarjeta Empresas (Solo Súper Admin) -->
                    <?php if ($is_superadmin): ?>
                    <div class="stat-card">
                        <div class="stat-card-icon empresas">
                            <img src="assets/images/icons/icons8-empresa-dashboard-resumen.png" alt="Empresas">
                        </div>
                        <div class="stat-card-content">
                            <h3>Empresas</h3>
                            <p class="stat-number"><?php echo $row['total_empresas']; ?></p>
                            <span class="stat-label">Registradas</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tarjeta Rutas -->
                    <div class="stat-card">
                        <div class="stat-card-icon rutas">
                            <img src="assets/images/icons/icons8-ruta-dashboard-resumen.png" alt="Rutas">
                        </div>
                        <div class="stat-card-content">
                            <h3>Rutas</h3>
                            <p class="stat-number"><?php echo $row['total_rutas']; ?></p>
                            <span class="stat-label">Activas</span>
                        </div>
                    </div>

                    <!-- Tarjeta Veh├¡culos -->
                    <div class="stat-card">
                        <div class="stat-card-icon vehiculos">
                            <img src="assets/images/icons/icons8-vehiculo-dashboard-resumen.png" alt="Veh├¡culos">
                        </div>
                        <div class="stat-card-content">
                            <h3>Vehículos</h3>
                            <p class="stat-number"><?php echo $row['total_vehiculos']; ?></p>
                            <span class="stat-label">En operación</span>
                        </div>
                    </div>

                    <!-- Tarjeta Conductores -->
                    <div class="stat-card">
                        <div class="stat-card-icon conductores">
                            <img src="assets/images/icons/icons8-conductor-dashboard-resumen.png" alt="Conductores">
                        </div>
                        <div class="stat-card-content">
                            <h3>Conductores</h3>
                            <p class="stat-number"><?php echo $row['total_conductores']; ?></p>
                            <span class="stat-label">Activos</span>
                        </div>
                    </div>

                    <!-- Tarjeta Horarios -->
                    <div class="stat-card">
                        <div class="stat-card-icon horarios">
                            <img src="assets/images/icons/icons8-horario-dashboard-resumen.png" alt="Horarios">
                        </div>
                        <div class="stat-card-content">
                            <h3>Horarios</h3>
                            <p class="stat-number"><?php echo $row['total_horarios']; ?></p>
                            <span class="stat-label">Configurados</span>
                        </div>
                    </div>

                    <!-- Tarjeta Checadores -->
                    <div class="stat-card">
                        <div class="stat-card-icon checadores">
                            <img src="assets/images/icons/icons8-checador-dashboard-resumen.png" alt="Checadores">
                        </div>
                        <div class="stat-card-content">
                            <h3>Checadores</h3>
                            <p class="stat-number"><?php echo $row['total_checadores']; ?></p>
                            <span class="stat-label">Registrados</span>
                        </div>
                    </div>
                </div>


                <!-- KPI CHARTS SECTION -->
                <div class="kpi-section-title">
                    <h2>Indicadores de Rendimiento</h2>
                    <span class="kpi-section-badge">KPI en Tiempo Real</span>
                </div>

                <!-- Fila 1: KPIs Esenciales -->
                <div class="charts-grid-3">
                    <!-- Estado de la Flota -->
                    <div class="chart-card">
                        <div class="chart-card-header" style="margin-bottom: 10px;">
                            <div class="chart-card-title">
                                <h4>Estado de la Flota</h4>
                                <span>Veh&iacute;culos registrados</span>
                            </div>
                            <div class="chart-card-icon green">
                                <span class="material-icons">directions_bus</span>
                            </div>
                        </div>
                        <div id="flotaDonaStats" style="display:flex;gap:16px;margin-bottom:12px;flex-wrap:wrap;justify-content:center;"></div>
                        <canvas id="chartFlotaDona" height="140"></canvas>
                    </div>

                    <!-- Asignaciones -->
                    <div class="chart-card">
                        <div class="chart-card-header" style="margin-bottom: 10px;">
                            <div class="chart-card-title">
                                <h4>Asignaciones</h4>
                                <span>&Uacute;ltimos 7 d&iacute;as</span>
                            </div>
                            <div class="chart-card-icon blue">
                                <span class="material-icons">calendar_today</span>
                            </div>
                        </div>
                        <canvas id="chartAsigDias" height="160"></canvas>
                    </div>

                    <!-- Estado de Reportes -->
                    <div class="chart-card">
                        <div class="chart-card-header" style="margin-bottom: 10px;">
                            <div class="chart-card-title">
                                <h4>Estado de Reportes</h4>
                                <span>Sin archivar</span>
                            </div>
                            <div class="chart-card-icon purple">
                                <span class="material-icons">fact_check</span>
                            </div>
                        </div>
                        <canvas id="chartRepEstado" height="160"></canvas>
                    </div>
                </div>
                <!-- Secci├│n de Acciones R├ípidas -->
                <div class="quick-actions">
                    <h2>Acciones Rápidas</h2>
                    <div class="actions-grid">
                        <a href="pages/admin/rutas.php" class="action-btn">
                            <img class="action-icon" src="assets/images/icons/icons8-rutas-dashboard.png" alt="Rutas">
                            <span>Gestionar Rutas</span>
                        </a>
