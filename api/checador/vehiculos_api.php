<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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
        sendResponse(500, ["error" => "Error de conexión: " . $conn->connect_error]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $placa = isset($_GET['placa']) ? strtoupper(trim($_GET['placa'])) : '';

        if (empty($placa)) {
            sendResponse(400, ["error" => "El parámetro 'placa' es requerido"]);
        }

        // ── Zona horaria y tipo de día actual ────────────────────────────
        date_default_timezone_set('America/Mexico_City');
        $numeroDia = (int)date('N');
        if ($numeroDia >= 1 && $numeroDia <= 5) $tipo_dia = 'Lunes a Viernes';
        elseif ($numeroDia === 6)               $tipo_dia = 'Sábado';
        else                                    $tipo_dia = 'Domingo';

        // ── 1. Info básica del vehículo ──────────────────────────────────
        $sql_v = "SELECT
                      v.id_vehiculo,
                      v.placa,
                      v.modelo,
                      v.capacidad,
                      v.activo AS vehiculo_activo,
                      e.nombre AS empresa_nombre
                  FROM vehiculos v
                  LEFT JOIN empresas e ON v.rfc_empresa = e.rfc_empresa
                  WHERE v.placa = ?
                  LIMIT 1";

        $stmt_v = $conn->prepare($sql_v);
        if (!$stmt_v) {
            sendResponse(500, ["error" => "Error preparando consulta de vehículo: " . $conn->error]);
        }
        $stmt_v->bind_param("s", $placa);
        $stmt_v->execute();
        $result_v = $stmt_v->get_result();

        if ($result_v->num_rows === 0) {
            sendResponse(404, ["error" => "No se encontró ningún vehículo con la placa '$placa'"]);
        }

        $vehiculo = $result_v->fetch_assoc();
        $stmt_v->close();

        // ── 2. Todas las asignaciones activas del vehículo para hoy ──────
        $sql_a = "SELECT
                      a.id_asignacion,
                      h.hora_salida,
                      h.tipo_dia,
                      r.nombre  AS ruta_nombre,
                      r.origen  AS ruta_origen,
                      r.destino AS ruta_destino,
                      r.id_ruta_retorno,
                      ret.nombre  AS ruta_retorno_nombre,
                      ret.origen  AS ruta_retorno_origen,
                      ret.destino AS ruta_retorno_destino,
                      c.nombre        AS conductor_nombre,
                      c.rfc_conductor,
                      a.asientos_disp AS asientos_disponibles
                  FROM asignaciones a
                  JOIN horarios h
                      ON  h.id_horario = a.id_horario
                      AND h.id_ruta    = a.id_ruta
                      AND h.tipo_dia   = ?
                  JOIN rutas r ON r.id_ruta = a.id_ruta
                  LEFT JOIN rutas ret ON r.id_ruta_retorno = ret.id_ruta
                  LEFT JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
                  WHERE a.id_vehiculo = ?
                    AND a.activa = 1
                  ORDER BY h.hora_salida ASC";

        $stmt_a = $conn->prepare($sql_a);
        if (!$stmt_a) {
            sendResponse(500, ["error" => "Error preparando consulta de asignaciones: " . $conn->error]);
        }
        $stmt_a->bind_param("si", $tipo_dia, $vehiculo['id_vehiculo']);
        $stmt_a->execute();
        $result_a = $stmt_a->get_result();

        $asignaciones = [];
        while ($row = $result_a->fetch_assoc()) {
            $asignaciones[] = $row;
        }
        $stmt_a->close();

        // ── 3. Construir respuesta ───────────────────────────────────────
        $vehiculo['asignaciones'] = $asignaciones;

        sendResponse(200, [
            "success" => true,
            "data"    => $vehiculo
        ]);
    }

    sendResponse(405, ["error" => "Método no permitido. Use GET"]);

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
}
