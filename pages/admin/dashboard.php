<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../../index.php');
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
    <title>Dashboard - GoWay</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php
        $page_title  = 'Dashboard';
        $active_page = 'dashboard';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
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

                <!-- Grid de Estadísticas -->
                <div class="stats-grid">
                    <?php
                    $conn = $conexion;

                    if ($conn->connect_error) {
                        die("Error de conexión: " . $conn->connect_error);
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

                    <?php if ($is_superadmin): ?>
                    <div class="stat-card">
                        <div class="stat-card-icon empresas">
                            <img src="../../assets/images/icons/icons8-empresa-dashboard-resumen.png" alt="Empresas">
                        </div>
                        <div class="stat-card-content">
                            <h3>Empresas</h3>
                            <p class="stat-number"><?php echo $row['total_empresas']; ?></p>
                            <span class="stat-label">Registradas</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="stat-card">
                        <div class="stat-card-icon rutas">
                            <img src="../../assets/images/icons/icons8-ruta-dashboard-resumen.png" alt="Rutas">
                        </div>
                        <div class="stat-card-content">
                            <h3>Rutas</h3>
                            <p class="stat-number"><?php echo $row['total_rutas']; ?></p>
                            <span class="stat-label">Activas</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-icon vehiculos">
                            <img src="../../assets/images/icons/icons8-vehiculo-dashboard-resumen.png" alt="Vehículos">
                        </div>
                        <div class="stat-card-content">
                            <h3>Vehículos</h3>
                            <p class="stat-number"><?php echo $row['total_vehiculos']; ?></p>
                            <span class="stat-label">En operación</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-icon conductores">
                            <img src="../../assets/images/icons/icons8-conductor-dashboard-resumen.png" alt="Conductores">
                        </div>
                        <div class="stat-card-content">
                            <h3>Conductores</h3>
                            <p class="stat-number"><?php echo $row['total_conductores']; ?></p>
                            <span class="stat-label">Activos</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-icon horarios">
                            <img src="../../assets/images/icons/icons8-horario-dashboard-resumen.png" alt="Horarios">
                        </div>
                        <div class="stat-card-content">
                            <h3>Horarios</h3>
                            <p class="stat-number"><?php echo $row['total_horarios']; ?></p>
                            <span class="stat-label">Configurados</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-icon checadores">
                            <img src="../../assets/images/icons/icons8-checador-dashboard-resumen.png" alt="Checadores">
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

                <div class="charts-grid-3">
                    <!-- Estado de la Flota -->
                    <div class="chart-card">
                        <div class="chart-card-header" style="margin-bottom: 10px;">
                            <div class="chart-card-title">
                                <h4>Estado de la flota</h4>
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
                                <h4>Estado de reportes</h4>
                                <span>Sin archivar</span>
                            </div>
                            <div class="chart-card-icon purple">
                                <span class="material-icons">fact_check</span>
                            </div>
                        </div>
                        <canvas id="chartRepEstado" height="160"></canvas>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="quick-actions">
                    <h2>Acciones Rápidas</h2>
                    <div class="actions-grid">
                        <a href="rutas.php" class="action-btn">
                            <img class="action-icon" src="../../assets/images/icons/icons8-rutas-dashboard.png" alt="Rutas">
                            <span>Gestionar Rutas</span>
                        </a>
                        <a href="vehiculos.php" class="action-btn">
                            <img class="action-icon" src="../../assets/images/icons/icons8-vehiculos-dashboard.png" alt="Vehículos">
                            <span>Gestionar Vehículos</span>
                        </a>
                        <a href="conductores.php" class="action-btn">
                            <img class="action-icon" src="../../assets/images/icons/icons8-conditores-dashboard.png" alt="Conductores">
                            <span>Gestionar Conductores</span>
                        </a>
                        <a href="horarios.php" class="action-btn">
                            <img class="action-icon" src="../../assets/images/icons/icons8-horario-dashboard.png" alt="Horarios">
                            <span>Gestionar Horarios</span>
                        </a>
                        <a href="checadores.php" class="action-btn">
                            <img class="action-icon" src="../../assets/images/icons/icons8-checadores-dashboard.png" alt="Checadores">
                            <span>Gestionar Checadores</span>
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>

    <script src="../../assets/js/notifications.js"></script>
    <script src="../../assets/js/main.js"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    <script>
    const GW = {
        blue:'#0660fe', blue2:'#3b82f6', blue3:'#93c5fd',
        green:'#10b981', green2:'#34d399', orange:'#f59e0b',
        red:'#ef4444', red2:'#fca5a5', purple:'#8b5cf6',
        gray:'#e2e8f0', text:'#1a1c23', sub:'#94a3b8',
    };
    const baseFont = { family:"'Inter', system-ui, sans-serif", size:12 };
    const baseTooltip = { backgroundColor:'rgba(15,20,40,0.88)', titleFont:{...baseFont,weight:700,size:13}, bodyFont:baseFont, padding:10, cornerRadius:8, borderColor:'rgba(255,255,255,0.08)', borderWidth:1 };
    const baseGrid = { color:'rgba(0,0,0,0.05)', drawBorder:false };
    function baseTick() { return { color:GW.sub, font:baseFont }; }

    function showEmpty(canvasId, msg) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        canvas.style.display = 'none';
        const d = document.createElement('div');
        d.className = 'chart-empty';
        d.innerHTML = '<span class="material-icons">bar_chart</span><p>' + msg + '</p>';
        canvas.parentElement.appendChild(d);
    }

    fetch('../../api/kpis_api.php')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            renderFlotaDona(data.flota_dona, data.kpi.vehiculos);
            renderAsigDias(data.asig_dias);
            renderRepEstado(data.rep_estados);
        })
        .catch(err => console.error('KPI API error:', err));

    function renderFlotaDona(flota, kpiVeh) {
        const statsDiv = document.getElementById('flotaDonaStats');
        if (statsDiv && kpiVeh) {
            statsDiv.innerHTML = `
                <div class="chart-stat-item"><span class="stat-val" style="color:${GW.blue}">${kpiVeh.activos}</span><span class="stat-lbl">Activos</span></div>
                <div class="chart-stat-item"><span class="stat-val" style="color:${GW.red}">${kpiVeh.total - kpiVeh.activos}</span><span class="stat-lbl">Inactivos</span></div>
                <div class="chart-stat-item"><span class="stat-val">${kpiVeh.total}</span><span class="stat-lbl">Total</span></div>`;
        }
        if (!flota.data || flota.data.every(v => v === 0)) { showEmpty('chartFlotaDona','Sin datos de vehículos'); return; }
        new Chart(document.getElementById('chartFlotaDona'), {
            type:'doughnut',
            data:{ labels:flota.labels, datasets:[{ data:flota.data, backgroundColor:[GW.blue,GW.red], borderColor:['#fff','#fff'], borderWidth:3, hoverOffset:6 }] },
            options:{ cutout:'72%', plugins:{ legend:{ position:'bottom', labels:{ font:baseFont, padding:16, boxWidth:12, color:GW.text } }, tooltip:{...baseTooltip, callbacks:{ label:(ctx)=>` ${ctx.label}: ${ctx.parsed}` }} } }
        });
    }

    function renderAsigDias(d) {
        if (!d.labels || d.labels.length===0) { showEmpty('chartAsigDias','Sin asignaciones en los últimos 7 días'); return; }
        new Chart(document.getElementById('chartAsigDias'), {
            type:'bar',
            data:{ labels:d.labels, datasets:[
                { label:'Completadas', data:d.completadas, backgroundColor:GW.green,  borderRadius:4, stack:'a' },
                { label:'En Ruta',     data:d.en_ruta,     backgroundColor:GW.blue,   borderRadius:4, stack:'a' },
                { label:'Programadas', data:d.programadas,  backgroundColor:GW.blue3,  borderRadius:4, stack:'a' },
                { label:'Canceladas',  data:d.canceladas,   backgroundColor:GW.red2,   borderRadius:4, stack:'a' },
            ]},
            options:{ responsive:true, maintainAspectRatio:true, plugins:{ legend:{ position:'bottom', labels:{ font:baseFont, padding:14, boxWidth:12, color:GW.text } }, tooltip:{...baseTooltip,mode:'index',intersect:false} }, scales:{ x:{ stacked:true, grid:{display:false}, ticks:baseTick() }, y:{ stacked:true, grid:baseGrid, ticks:{...baseTick(),stepSize:1}, beginAtZero:true } } }
        });
    }

    function renderRepEstado(d) {
        if (!d.labels || d.labels.length===0) { showEmpty('chartRepEstado','Sin reportes registrados'); return; }
        const colorMap = { 'Pendiente':GW.orange, 'En Proceso':GW.blue, 'Resuelto':GW.green };
        const colors = d.labels.map(l => colorMap[l] || GW.gray);
        new Chart(document.getElementById('chartRepEstado'), {
            type:'doughnut',
            data:{ labels:d.labels, datasets:[{ data:d.data, backgroundColor:colors, borderColor:'#fff', borderWidth:3, hoverOffset:6 }] },
            options:{ cutout:'68%', plugins:{ legend:{ position:'bottom', labels:{ font:baseFont, padding:14, boxWidth:12, color:GW.text } }, tooltip:{...baseTooltip} } }
        });
    }
    </script>
</body>
</html>
