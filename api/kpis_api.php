<?php
/**
 * kpis_api.php — API de KPIs dinámica por sección
 * Permite renderizar gráficos en Dashboard y en sub-pantallas
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
ini_set('display_errors', 0); error_reporting(E_ALL);

require_once '../config/conexion_bd.php';
function resp($code, $data) { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }

try {
    $conn = $conexion;
    if ($conn->connect_error) resp(500, ["error" => "DB connection error"]);

    $is_superadmin   = isset($_SESSION['rol']) && $_SESSION['rol'] == 1;
    $rfc_empresa     = (!$is_superadmin && !empty($_SESSION['rfc_empresa'])) ? $_SESSION['rfc_empresa'] : null;

    $seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'dashboard';

    $w_empresa_rutas      = $rfc_empresa ? "AND r.rfc_empresa = '$rfc_empresa'"       : "";
    $w_empresa_vehiculos  = $rfc_empresa ? "AND v.rfc_empresa = '$rfc_empresa'"       : "";
    $w_empresa_conductores= $rfc_empresa ? "AND c.rfc_empresa = '$rfc_empresa'"       : "";
    $w_empresa_checadores = $rfc_empresa ? "AND ch.rfc_empresa = '$rfc_empresa'"      : "";
    $w_empresa_asig       = $rfc_empresa ? "AND r.rfc_empresa = '$rfc_empresa'"       : "";
    
    $join_rfc = $rfc_empresa ? "JOIN vehiculos v ON rep.id_vehiculo = v.id_vehiculo" : "";
    $w_rfc    = $rfc_empresa ? "AND v.rfc_empresa = '$rfc_empresa'" : "";

    $data = ["success" => true, "generado_en" => date('Y-m-d H:i:s'), "seccion" => $seccion];

    // ==========================================
    // SECCIÓN: DASHBOARD GENERAL
    // ==========================================
    if ($seccion === 'dashboard') {
        $kpi = [];
        $row = $conn->query("SELECT COUNT(*) AS t, SUM(activa=1) AS a FROM rutas r WHERE 1=1 $w_empresa_rutas")->fetch_assoc();
        $kpi['rutas'] = ['total' => (int)$row['t'], 'activas' => (int)$row['a']];

        $row = $conn->query("SELECT COUNT(*) AS t, SUM(activo=1) AS a FROM vehiculos v WHERE 1=1 $w_empresa_vehiculos")->fetch_assoc();
        $kpi['vehiculos'] = ['total' => (int)$row['t'], 'activos' => (int)$row['a']];

        $row = $conn->query("SELECT COUNT(*) AS t, SUM(activo=1) AS a FROM conductores c WHERE 1=1 $w_empresa_conductores")->fetch_assoc();
        $kpi['conductores'] = ['total' => (int)$row['t'], 'activos' => (int)$row['a']];

        $row = $conn->query("SELECT COUNT(*) AS t, SUM(activo=1) AS a FROM checadores ch WHERE 1=1 $w_empresa_checadores")->fetch_assoc();
        $kpi['checadores'] = ['total' => (int)$row['t'], 'activos' => (int)$row['a']];

        $row = $conn->query("SELECT COUNT(*) AS t, SUM(a.estado='en_ruta') AS e, SUM(a.estado='completado') AS c, SUM(a.estado='cancelado') AS ca, SUM(a.estado='programado') AS p FROM asignaciones a JOIN rutas r ON a.id_ruta = r.id_ruta WHERE a.fecha = CURDATE() $w_empresa_asig")->fetch_assoc();
        $kpi['asignaciones_hoy'] = ['total' => (int)$row['t'], 'en_ruta' => (int)$row['e'], 'completadas'=> (int)$row['c'], 'canceladas' => (int)$row['ca'], 'programadas'=> (int)$row['p']];

        $row = $conn->query("SELECT COUNT(*) AS t, SUM(rep.estado='pendiente') AS p, SUM(rep.estado='en-proceso') AS ep, SUM(rep.estado='resuelto') AS r FROM reportes rep $join_rfc WHERE rep.archivado = 0 $w_rfc")->fetch_assoc();
        $kpi['reportes'] = ['total' => (int)$row['t'], 'pendientes'=> (int)$row['p'], 'en_proceso'=> (int)$row['ep'], 'resueltos' => (int)$row['r']];

        if ($is_superadmin) {
            $row = $conn->query("SELECT COUNT(*) AS t, SUM(activo=1) AS a FROM empresas")->fetch_assoc();
            $kpi['empresas'] = ['total' => (int)$row['t'], 'activas' => (int)$row['a']];
        }

        $row = $conn->query("SELECT SUM(activo=1) AS a, SUM(activo=0) AS i FROM vehiculos v WHERE 1=1 $w_empresa_vehiculos")->fetch_assoc();
        $data['flota_dona'] = ['labels' => ['Activos', 'Inactivos'], 'data' => [(int)$row['a'], (int)$row['i']]];

        $q = $conn->query("SELECT IF(a.activa=0, 'Inactiva/Cancelada', a.estado) AS estado_final, COUNT(*) as t FROM asignaciones a JOIN rutas r ON a.id_ruta = r.id_ruta WHERE 1=1 $w_empresa_asig GROUP BY estado_final");
        $d = ['labels' => [], 'data' => []];
        $em = ['programado' => 'Programado', 'en_ruta' => 'En Ruta', 'completado' => 'Completado', 'cancelado' => 'Cancelado', 'retrasado' => 'Retrasado', 'Inactiva/Cancelada' => 'Inactiva/Cancelada'];
        while ($row = $q->fetch_assoc()) { $d['labels'][] = $em[$row['estado_final']] ?? ucfirst($row['estado_final']); $d['data'][] = (int)$row['t']; }
        $data['asig_dias'] = $d;

        $q = $conn->query("SELECT rep.estado, COUNT(*) AS t FROM reportes rep $join_rfc WHERE rep.archivado = 0 $w_rfc GROUP BY rep.estado");
        $d = ['labels' => [], 'data' => []];
        $em = ['pendiente' => 'Pendiente', 'en-proceso' => 'En Proceso', 'resuelto' => 'Resuelto'];
        while ($row = $q->fetch_assoc()) { $d['labels'][] = $em[$row['estado']] ?? ucfirst($row['estado']); $d['data'][] = (int)$row['t']; }
        $data['rep_estados'] = $d;

        $data['kpi'] = $kpi;
    }

    // ==========================================
    // SECCIÓN: RUTAS
    // ==========================================
    elseif ($seccion === 'rutas') {
        $kpi = [];
        $row = $conn->query("SELECT COUNT(*) AS total, SUM(activa=1) AS a, SUM(id_ruta_retorno IS NOT NULL) as con_retorno FROM rutas r WHERE 1=1 $w_empresa_rutas")->fetch_assoc();
        $kpi['total'] = (int)$row['total'];
        $kpi['activas'] = (int)$row['a'];
        $kpi['con_retorno'] = (int)$row['con_retorno'];

        $row = $conn->query("SELECT COUNT(*) AS total FROM paradas_ruta p JOIN rutas r ON p.id_ruta = r.id_ruta WHERE 1=1 $w_empresa_rutas")->fetch_assoc();
        $kpi['paradas'] = (int)$row['total'];

        $data['kpi'] = $kpi;

        // Gráfico 1: Dona Activas vs Inactivas
        $data['estado_rutas'] = ['labels' => ['Activas', 'Inactivas'], 'data' => [$kpi['activas'], $kpi['total'] - $kpi['activas']]];

        // Gráfico 2: Rutas con más paradas
        $q = $conn->query("SELECT r.nombre, COUNT(p.id_parada) AS t FROM rutas r LEFT JOIN paradas_ruta p ON r.id_ruta = p.id_ruta WHERE 1=1 $w_empresa_rutas GROUP BY r.id_ruta ORDER BY t DESC LIMIT 5");
        $d = ['labels' => [], 'data' => []];
        while ($row = $q->fetch_assoc()) { $d['labels'][] = $row['nombre']; $d['data'][] = (int)$row['t']; }
        $data['top_paradas'] = $d;
    }

    // ==========================================
    // SECCIÓN: VEHICULOS
    // ==========================================
    elseif ($seccion === 'vehiculos') {
        $kpi = [];
        $row = $conn->query("SELECT COUNT(*) AS total, SUM(activo=1) AS a, SUM(activo=0) as inactivos FROM vehiculos v WHERE 1=1 $w_empresa_vehiculos")->fetch_assoc();
        $kpi['total'] = (int)$row['total'];
        $kpi['activos'] = (int)$row['a'];
        $kpi['inactivos'] = (int)$row['inactivos'];
        $kpi['disp'] = $kpi['total'] > 0 ? round(($kpi['activos'] / $kpi['total']) * 100, 1) : 0;
        
        $data['kpi'] = $kpi;

        // Gráfico 1: Dona de estado
        $data['estado_vehiculos'] = ['labels' => ['Activos', 'Inactivos'], 'data' => [$kpi['activos'], $kpi['inactivos']]];

        // Gráfico 2: Distribución por modelo (Barras)
        $q = $conn->query("SELECT modelo, COUNT(*) AS t FROM vehiculos v WHERE 1=1 $w_empresa_vehiculos GROUP BY modelo ORDER BY t DESC LIMIT 5");
        $d = ['labels' => [], 'data' => []];
        while ($row = $q->fetch_assoc()) { $d['labels'][] = empty($row['modelo']) ? 'Desconocido' : $row['modelo']; $d['data'][] = (int)$row['t']; }
        $data['modelos'] = $d;
    }

    // ==========================================
    // SECCIÓN: HORARIOS
    // ==========================================
    elseif ($seccion === 'horarios') {
        $kpi = [];
        $row = $conn->query("SELECT COUNT(*) AS total, COUNT(DISTINCT h.id_ruta) AS rutas_con_horario, SUM(h.frecuencia != '') AS con_frecuencia FROM horarios h JOIN rutas r ON h.id_ruta = r.id_ruta WHERE 1=1 $w_empresa_rutas")->fetch_assoc();
        $kpi['total'] = (int)$row['total'];
        $kpi['rutas_cubiertas'] = (int)$row['rutas_con_horario'];
        $kpi['con_frecuencia'] = (int)$row['con_frecuencia'];
        
        $data['kpi'] = $kpi;

        // Gráfico 1: Franjas horarias
        $q = $conn->query("SELECT 
            SUM(HOUR(h.hora_salida) BETWEEN 6 AND 11) AS manana,
            SUM(HOUR(h.hora_salida) BETWEEN 12 AND 17) AS tarde,
            SUM(HOUR(h.hora_salida) BETWEEN 18 AND 23) AS noche,
            SUM(HOUR(h.hora_salida) BETWEEN 0 AND 5) AS madrugada
            FROM horarios h JOIN rutas r ON h.id_ruta = r.id_ruta WHERE 1=1 $w_empresa_rutas");
        $row = $q->fetch_assoc();
        $data['franjas'] = ['labels' => ['Madrugada', 'Mañana', 'Tarde', 'Noche'], 'data' => [(int)$row['madrugada'], (int)$row['manana'], (int)$row['tarde'], (int)$row['noche']]];

        // Gráfico 2: Tipos de día
        $q = $conn->query("SELECT h.tipo_dia, COUNT(*) AS t FROM horarios h JOIN rutas r ON h.id_ruta = r.id_ruta WHERE 1=1 $w_empresa_rutas GROUP BY h.tipo_dia ORDER BY t DESC LIMIT 5");
        $d = ['labels' => [], 'data' => []];
        while ($row = $q->fetch_assoc()) { $d['labels'][] = $row['tipo_dia']; $d['data'][] = (int)$row['t']; }
        $data['tipo_dia'] = $d;
    }

    // ==========================================
    // SECCIÓN: ASIGNACIONES
    // ==========================================
    elseif ($seccion === 'asignaciones') {
        $kpi = [];
        $row = $conn->query("SELECT COUNT(*) AS total, SUM(a.estado='completado') AS completadas, SUM(a.estado='cancelado' OR a.estado='retrasado' OR a.activa=0) AS conflictivas FROM asignaciones a JOIN rutas r ON a.id_ruta = r.id_ruta WHERE 1=1 $w_empresa_asig")->fetch_assoc();
        $kpi['hoy_total'] = (int)$row['total'];
        $kpi['hoy_completadas'] = (int)$row['completadas'];
        $kpi['hoy_conflictivas'] = (int)$row['conflictivas'];
        $kpi['porcentaje'] = $kpi['hoy_total'] > 0 ? round(($kpi['hoy_completadas'] / $kpi['hoy_total']) * 100, 1) : 0;
        
        $data['kpi'] = $kpi;

        // Gráfico 1: Estado (Dona)
        $q = $conn->query("SELECT IF(a.activa=0, 'Inactiva/Cancelada', a.estado) AS estado_final, COUNT(*) as t FROM asignaciones a JOIN rutas r ON a.id_ruta = r.id_ruta WHERE 1=1 $w_empresa_asig GROUP BY estado_final");
        $d = ['labels' => [], 'data' => []];
        $em = ['programado' => 'Programado', 'en_ruta' => 'En Ruta', 'completado' => 'Completado', 'cancelado' => 'Cancelado', 'retrasado' => 'Retrasado', 'Inactiva/Cancelada' => 'Inactiva/Cancelada'];
        while ($row = $q->fetch_assoc()) { $d['labels'][] = $em[$row['estado_final']] ?? ucfirst($row['estado_final']); $d['data'][] = (int)$row['t']; }
        $data['estado_hoy'] = $d;

        // Gráfico 2: Top conductores con más carga (Barras)
        $q = $conn->query("SELECT c.nombre, COUNT(*) as t FROM asignaciones a JOIN conductores c ON a.rfc_conductor = c.rfc_conductor WHERE a.activa=1 $w_empresa_conductores GROUP BY a.rfc_conductor ORDER BY t DESC LIMIT 5");
        $d = ['labels' => [], 'data' => []];
        while ($row = $q->fetch_assoc()) {
            $n = explode(' ', $row['nombre']);
            $d['labels'][] = count($n) > 1 ? $n[0] . ' ' . substr($n[1],0,1) . '.' : $n[0]; 
            $d['data'][] = (int)$row['t']; 
        }
        $data['top_conductores'] = $d;
    }

    resp(200, $data);

} catch (Exception $e) {
    resp(500, ["error" => "Error interno: " . $e->getMessage()]);
}