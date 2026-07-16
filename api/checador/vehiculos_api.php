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
        $placa       = isset($_GET['placa'])        ? strtoupper(trim($_GET['placa']))   : '';
        $rfc_checador = isset($_GET['rfc_checador']) ? trim($_GET['rfc_checador'])        : '';

        if (empty($rfc_checador)) {
            sendResponse(400, ["error" => "El parámetro 'rfc_checador' es requerido"]);
        }

        // ── Obtener rfc_empresa del checador ────────────────────────────
        $stmt_ch = $conn->prepare("SELECT rfc_empresa FROM checadores WHERE rfc_checador = ? AND activo = 1 LIMIT 1");
        if (!$stmt_ch) sendResponse(500, ["error" => "Error preparando consulta del checador"]);
        $stmt_ch->bind_param("s", $rfc_checador);
        $stmt_ch->execute();
        $result_ch = $stmt_ch->get_result();
        if ($result_ch->num_rows === 0) {
            sendResponse(403, ["error" => "Checador no encontrado o inactivo"]);
        }
        $rfc_empresa_checador = $result_ch->fetch_assoc()['rfc_empresa'];
        $stmt_ch->close();

        // ── Zona horaria y tipo de día actual ────────────────────────────
        date_default_timezone_set('America/Mexico_City');
        $numeroDia = (int)date('N');
        if ($numeroDia >= 1 && $numeroDia <= 5) $tipo_dia = 'Lunes a Viernes';
        elseif ($numeroDia === 6)               $tipo_dia = 'Sábado';
        else                                    $tipo_dia = 'Domingo';

        // Si se especificó una placa particular
        if (!empty($placa)) {
            // ── 1. Info básica del vehículo (filtrado por empresa del checador) ──
            $sql_v = "SELECT
                          v.id_vehiculo,
                          v.placa,
                          v.modelo,
                          v.capacidad,
                          v.activo AS vehiculo_activo,
                          e.nombre AS empresa_nombre
                      FROM vehiculos v
                      LEFT JOIN empresas e ON v.rfc_empresa = e.rfc_empresa
                      WHERE v.placa = ? AND v.rfc_empresa = ?
                      LIMIT 1";

            $stmt_v = $conn->prepare($sql_v);
            if (!$stmt_v) {
                sendResponse(500, ["error" => "Error preparando consulta de vehículo: " . $conn->error]);
            }
            $stmt_v->bind_param("ss", $placa, $rfc_empresa_checador);
            $stmt_v->execute();
            $result_v = $stmt_v->get_result();

            if ($result_v->num_rows === 0) {
                sendResponse(404, ["error" => "No se encontró el vehículo con placa '$placa' en tu empresa"]);
            }

            $vehiculo = $result_v->fetch_assoc();
            $stmt_v->close();

            // ── 2. Todas las asignaciones activas del vehículo para hoy ──────
            $sql_a = "SELECT
                          a.id_asignacion,
                          h.hora_salida,
                          h.hora_llegada,
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
                          a.estado,
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
        } else {
            // ── Si no se envió placa, retornar TODOS los vehículos de la empresa (o paginados si limit > 0) ──
            $sql_count = "SELECT COUNT(*) AS total FROM vehiculos WHERE rfc_empresa = ? AND activo = 1";
            $stmt_count = $conn->prepare($sql_count);
            if ($stmt_count) {
                $stmt_count->bind_param("s", $rfc_empresa_checador);
                $stmt_count->execute();
                $total_records = (int)($stmt_count->get_result()->fetch_assoc()['total'] ?? 0);
                $stmt_count->close();
            } else {
                $total_records = 0;
            }

            $page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
            $total_pages = ($limit > 0 && $total_records > 0) ? (int)ceil($total_records / $limit) : 1;

            $sql_v = "SELECT
                          v.id_vehiculo,
                          v.placa,
                          v.modelo,
                          v.capacidad,
                          v.activo AS vehiculo_activo,
                          e.nombre AS empresa_nombre
                      FROM vehiculos v
                      LEFT JOIN empresas e ON v.rfc_empresa = e.rfc_empresa
                      WHERE v.rfc_empresa = ? AND v.activo = 1
                      ORDER BY v.placa ASC";

            if ($limit > 0) {
                $offset = ($page - 1) * $limit;
                $sql_v .= " LIMIT ? OFFSET ?";
                $stmt_v = $conn->prepare($sql_v);
                if (!$stmt_v) sendResponse(500, ["error" => "Error preparando consulta de vehículos: " . $conn->error]);
                $stmt_v->bind_param("sii", $rfc_empresa_checador, $limit, $offset);
            } else {
                $stmt_v = $conn->prepare($sql_v);
                if (!$stmt_v) sendResponse(500, ["error" => "Error preparando consulta de vehículos: " . $conn->error]);
                $stmt_v->bind_param("s", $rfc_empresa_checador);
            }

            $stmt_v->execute();
            $result_v = $stmt_v->get_result();

            $vehiculos_map = [];
            while ($row_v = $result_v->fetch_assoc()) {
                $id_v = (int)$row_v['id_vehiculo'];
                $row_v['asignaciones'] = [];
                $vehiculos_map[$id_v] = $row_v;
            }
            $stmt_v->close();

            $sql_a = "SELECT
                          a.id_asignacion,
                          a.id_vehiculo,
                          h.hora_salida,
                          h.hora_llegada,
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
                          a.estado,
                          a.asientos_disp AS asientos_disponibles,
                          v.placa,
                          v.modelo,
                          v.capacidad,
                          v.activo AS vehiculo_activo,
                          e.nombre AS empresa_nombre
                      FROM asignaciones a
                      JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo
                      LEFT JOIN empresas e ON v.rfc_empresa = e.rfc_empresa
                      JOIN horarios h
                          ON  h.id_horario = a.id_horario
                          AND h.id_ruta    = a.id_ruta
                          AND h.tipo_dia   = ?
                      JOIN rutas r ON r.id_ruta = a.id_ruta
                      LEFT JOIN rutas ret ON r.id_ruta_retorno = ret.id_ruta
                      LEFT JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
                      WHERE v.rfc_empresa = ?
                        AND a.activa = 1
                      ORDER BY h.hora_salida ASC";

            $stmt_a = $conn->prepare($sql_a);
            if (!$stmt_a) {
                sendResponse(500, ["error" => "Error preparando consulta de asignaciones: " . $conn->error]);
            }
            $stmt_a->bind_param("ss", $tipo_dia, $rfc_empresa_checador);
            $stmt_a->execute();
            $result_a = $stmt_a->get_result();

            $horarios_planos = [];
            while ($row_a = $result_a->fetch_assoc()) {
                $id_v = (int)$row_a['id_vehiculo'];
                $asignacion = [
                    'id_asignacion'       => $row_a['id_asignacion'],
                    'hora_salida'         => $row_a['hora_salida'],
                    'hora_llegada'        => $row_a['hora_llegada'],
                    'tipo_dia'            => $row_a['tipo_dia'],
                    'ruta_nombre'         => $row_a['ruta_nombre'],
                    'ruta_origen'         => $row_a['ruta_origen'],
                    'ruta_destino'        => $row_a['ruta_destino'],
                    'id_ruta_retorno'     => $row_a['id_ruta_retorno'],
                    'ruta_retorno_nombre' => $row_a['ruta_retorno_nombre'],
                    'ruta_retorno_origen' => $row_a['ruta_retorno_origen'],
                    'ruta_retorno_destino'=> $row_a['ruta_retorno_destino'],
                    'conductor_nombre'    => $row_a['conductor_nombre'],
                    'rfc_conductor'       => $row_a['rfc_conductor'],
                    'estado'              => $row_a['estado'],
                    'asientos_disponibles'=> $row_a['asientos_disponibles']
                ];

                if (isset($vehiculos_map[$id_v])) {
                    $vehiculos_map[$id_v]['asignaciones'][] = $asignacion;
                } elseif ($limit <= 0) {
                    $vehiculos_map[$id_v] = [
                        'id_vehiculo'     => $row_a['id_vehiculo'],
                        'placa'           => $row_a['placa'],
                        'modelo'          => $row_a['modelo'],
                        'capacidad'       => $row_a['capacidad'],
                        'vehiculo_activo' => $row_a['vehiculo_activo'],
                        'empresa_nombre'  => $row_a['empresa_nombre'],
                        'asignaciones'    => [$asignacion]
                    ];
                }

                $row_plano = $asignacion;
                $row_plano['id_vehiculo']     = $row_a['id_vehiculo'];
                $row_plano['placa']           = $row_a['placa'];
                $row_plano['modelo']          = $row_a['modelo'];
                $row_plano['capacidad']       = $row_a['capacidad'];
                $row_plano['vehiculo_activo'] = $row_a['vehiculo_activo'];
                $row_plano['empresa_nombre']  = $row_a['empresa_nombre'];
                $row_plano['vehiculo']        = [
                    'id_vehiculo'     => $row_a['id_vehiculo'],
                    'placa'           => $row_a['placa'],
                    'modelo'          => $row_a['modelo'],
                    'capacidad'       => $row_a['capacidad'],
                    'vehiculo_activo' => $row_a['vehiculo_activo'],
                    'empresa_nombre'  => $row_a['empresa_nombre']
                ];

                $horarios_planos[] = $row_plano;
            }
            $stmt_a->close();

            sendResponse(200, [
                "success"       => true,
                "page"          => $page,
                "limit"         => $limit,
                "total_records" => $total_records,
                "total_pages"   => $total_pages,
                "tipo_dia"      => $tipo_dia,
                "data"          => array_values($vehiculos_map),
                "horarios"      => $horarios_planos
            ]);
        }
    }

    sendResponse(405, ["error" => "Método no permitido. Use GET"]);

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
}
