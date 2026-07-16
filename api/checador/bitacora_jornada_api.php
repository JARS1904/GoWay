<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

ini_set('display_errors', 0);
error_reporting(E_ALL);

function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../../config/conexion_bd.php';

try {
    $conn = $conexion;
    if ($conn->connect_error) {
        sendResponse(500, ["success" => false, "error" => "Error de conexión: " . $conn->connect_error]);
    }

    // Auto-check de columnas para resiliencia del endpoint
    $check_h = $conn->query("SHOW COLUMNS FROM asignaciones LIKE 'hora_checado_real'");
    if ($check_h && $check_h->num_rows === 0) {
        $conn->query("ALTER TABLE asignaciones ADD COLUMN hora_checado_real DATETIME NULL DEFAULT NULL AFTER estado");
    }
    $check_c = $conn->query("SHOW COLUMNS FROM asignaciones LIKE 'checado_por'");
    if ($check_c && $check_c->num_rows === 0) {
        $conn->query("ALTER TABLE asignaciones ADD COLUMN checado_por VARCHAR(100) NULL DEFAULT NULL AFTER hora_checado_real");
    }
    $check_hl = $conn->query("SHOW COLUMNS FROM horarios LIKE 'hora_llegada'");
    if ($check_hl && $check_hl->num_rows === 0) {
        $conn->query("ALTER TABLE horarios ADD COLUMN hora_llegada TIME NULL DEFAULT NULL AFTER hora_salida");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        sendResponse(405, ["success" => false, "error" => "Método no permitido. Use GET"]);
    }

    $rfc_checador = isset($_GET['rfc_checador']) ? trim($_GET['rfc_checador']) : '';
    $placa_filtro = isset($_GET['placa']) ? strtoupper(trim($_GET['placa'])) : '';

    $rfc_empresa_checador = '';

    if (!empty($rfc_checador)) {
        $stmt_ch = $conn->prepare("SELECT rfc_empresa FROM checadores WHERE rfc_checador = ? AND activo = 1 LIMIT 1");
        if (!$stmt_ch) {
            sendResponse(500, ["success" => false, "error" => "Error preparando consulta del checador"]);
        }
        $stmt_ch->bind_param("s", $rfc_checador);
        $stmt_ch->execute();
        $result_ch = $stmt_ch->get_result();
        if ($result_ch->num_rows > 0) {
            $rfc_empresa_checador = $result_ch->fetch_assoc()['rfc_empresa'];
        }
        $stmt_ch->close();
    }

    date_default_timezone_set('America/Mexico_City');
    $numeroDia = (int)date('N');
    if ($numeroDia >= 1 && $numeroDia <= 5) $tipo_dia = 'Lunes a Viernes';
    elseif ($numeroDia === 6)               $tipo_dia = 'Sábado';
    else                                    $tipo_dia = 'Domingo';

    $conditions = ["a.activa = 1"];
    $params = [];
    $types  = "";

    if (!empty($rfc_empresa_checador)) {
        $conditions[] = "v.rfc_empresa = ?";
        $params[] = $rfc_empresa_checador;
        $types   .= "s";
    }

    if (!empty($placa_filtro)) {
        $conditions[] = "v.placa = ?";
        $params[] = $placa_filtro;
        $types   .= "s";
    }

    $conditions[] = "(h.tipo_dia = ? OR (h.id_horario IS NULL AND (a.fecha = CURDATE() OR a.fecha IS NULL)))";
    $params[] = $tipo_dia;
    $types   .= "s";

    $whereClause = implode(" AND ", $conditions);

    // Conteo total de registros
    $sql_count = "SELECT COUNT(*) AS total
                  FROM asignaciones a
                  JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo
                  LEFT JOIN horarios h ON a.id_horario = h.id_horario
                  WHERE $whereClause";
    $stmt_count = $conn->prepare($sql_count);
    if ($stmt_count) {
        if (!empty($params)) {
            $stmt_count->bind_param($types, ...$params);
        }
        $stmt_count->execute();
        $total_records = (int)($stmt_count->get_result()->fetch_assoc()['total'] ?? 0);
        $stmt_count->close();
    } else {
        $total_records = 0;
    }

    // Parámetros de paginación opcionales (por defecto devuelve todos si limit <= 0)
    $page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
    $total_pages = ($limit > 0 && $total_records > 0) ? (int)ceil($total_records / $limit) : 1;

    $sql = "SELECT
                a.id_asignacion,
                v.id_vehiculo AS num_unidad,
                v.modelo,
                v.placa,
                c.nombre AS conductor,
                e.nombre AS empresa,
                r.nombre AS ruta,
                IFNULL(h.hora_salida, DATE_FORMAT(a.created_at, '%H:%i:%s')) AS hora_salida,
                IFNULL(h.hora_llegada, '--:--') AS hora_llegada,
                h.tipo_dia,
                a.hora_checado_real,
                v.capacidad,
                a.estado,
                a.checado_por
            FROM asignaciones a
            JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo
            LEFT JOIN empresas e ON v.rfc_empresa = e.rfc_empresa
            LEFT JOIN horarios h ON a.id_horario = h.id_horario
            LEFT JOIN rutas r ON a.id_ruta = r.id_ruta
            LEFT JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
            WHERE $whereClause
            ORDER BY h.hora_salida ASC, a.id_asignacion DESC";

    if ($limit > 0) {
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types   .= "ii";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        sendResponse(500, ["success" => false, "error" => "Error preparando consulta de bitácora: " . $conn->error]);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $hora_checado = !empty($row['hora_checado_real']) ? date('H:i:s', strtotime($row['hora_checado_real'])) : "Pendiente";
        $checado_por  = !empty($row['checado_por']) ? trim($row['checado_por']) : "Checador en turno";
        
        // Ajuste en formato hora_salida y hora_llegada
        $hora_salida = !empty($row['hora_salida']) ? date('H:i:s', strtotime($row['hora_salida'])) : "00:00:00";
        $hora_llegada = !empty($row['hora_llegada']) && $row['hora_llegada'] !== '--:--' ? date('H:i:s', strtotime($row['hora_llegada'])) : "--:--";
        $modelo_mostrar = !empty($row['modelo']) ? trim($row['modelo']) : (string)($row['num_unidad'] ?? "");
        $dia_mostrar = !empty($row['tipo_dia']) ? trim($row['tipo_dia']) : $tipo_dia;

        $data[] = [
            "id_asignacion" => (string)$row['id_asignacion'],
            "num_unidad"    => $modelo_mostrar,
            "modelo"        => $modelo_mostrar,
            "id_vehiculo"   => (string)($row['num_unidad'] ?? ""),
            "placa"         => $row['placa'] ?? "",
            "conductor"     => !empty($row['conductor']) ? trim($row['conductor']) : "Sin Conductor Asignado",
            "empresa"       => !empty($row['empresa']) ? trim($row['empresa']) : "Transportes GoWay",
            "ruta"          => !empty($row['ruta']) ? trim($row['ruta']) : "Sin Ruta Asignada",
            "hora_salida"   => $hora_salida,
            "hora_llegada"  => $hora_llegada,
            "tipo_dia"      => $dia_mostrar,
            "hora_checado"  => $hora_checado,
            "capacidad"     => (int)($row['capacidad'] ?? 0),
            "estado"        => !empty($row['estado']) ? $row['estado'] : "programado",
            "checado_por"   => $checado_por
        ];
    }
    $stmt->close();

    sendResponse(200, [
        "success"       => true,
        "page"          => $page,
        "limit"         => $limit,
        "total_records" => $total_records,
        "total_pages"   => $total_pages,
        "data"          => $data
    ]);

} catch (Exception $e) {
    sendResponse(500, ["success" => false, "error" => "Error interno: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
