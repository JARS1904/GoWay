<?php
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Suma N minutos a una cadena de tiempo "HH:MM:SS" → "HH:MM"
function _addMinutes(string $timeStr, int $minutes): string {
    $parts        = explode(':', $timeStr);
    $totalMinutes = ((int)$parts[0]) * 60 + ((int)$parts[1]) + $minutes;
    return sprintf('%02d:%02d', intdiv($totalMinutes, 60) % 24, $totalMinutes % 60);
}

require_once '../config/conexion_bd.php';

try {
    $conn = $conexion;
    
    if ($conn->connect_error) {
        sendResponse(500, ["error" => "Error de conexión: " . $conn->connect_error]);
    }

    // Manejar solicitud GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {

        // ── Obtener lista de ubicaciones (orígenes, destinos y paradas registradas) ──
        if ($_GET['action'] === 'locations') {
            $sql = "SELECT DISTINCT ubicacion
                    FROM (
                        SELECT origen  AS ubicacion FROM rutas WHERE activa = 1
                        UNION
                        SELECT destino AS ubicacion FROM rutas WHERE activa = 1
                        UNION
                        SELECT pr.nombre
                        FROM   paradas_ruta pr
                        INNER JOIN rutas r ON pr.id_ruta = r.id_ruta
                        WHERE  r.activa = 1
                    ) AS todas
                    ORDER BY ubicacion ASC";
            $result = $conn->query($sql);

            if (!$result) {
                sendResponse(500, ["error" => "Error en consulta: " . $conn->error]);
            }

            $locations = [];
            while ($row = $result->fetch_assoc()) {
                $locations[] = $row["ubicacion"];
            }

            sendResponse(200, $locations);
        }

        // ── Obtener paradas de una ruta específica (para panel de admin) ──
        if ($_GET['action'] === 'paradas' && isset($_GET['id_ruta'])) {
            $id_ruta = (int)$_GET['id_ruta'];
            $stmt = $conn->prepare(
                "SELECT id_parada, id_ruta, nombre, orden, minutos_desde_origen
                 FROM   paradas_ruta
                 WHERE  id_ruta = ?
                 ORDER  BY orden ASC"
            );
            $stmt->bind_param("i", $id_ruta);
            $stmt->execute();
            $result = $stmt->get_result();

            $paradas = [];
            while ($row = $result->fetch_assoc()) {
                $paradas[] = $row;
            }
            sendResponse(200, $paradas);
        }

        // ── Obtener detalle completo de una ruta por ID (panel de favoritas) ──
        if ($_GET['action'] === 'route_detail' && isset($_GET['id_ruta'])) {
            $id_ruta = (int)$_GET['id_ruta'];

            date_default_timezone_set('America/Mexico_City');
            $numeroDia = date('N');
            if ($numeroDia >= 1 && $numeroDia <= 5) {
                $tipo_dia_actual = 'Lunes a Viernes';
            } elseif ($numeroDia == 6) {
                $tipo_dia_actual = 'Sábado';
            } else {
                $tipo_dia_actual = 'Domingo';
            }

            $sql = "SELECT r.id_ruta, r.nombre, r.origen, r.destino, r.rfc_empresa, r.paradas,
                           r.id_ruta_retorno,
                           0 AS embarque_minutos, 0 AS bajada_minutos,
                           NULL AS parada_embarque, NULL AS parada_bajada,
                           ret.nombre  AS ruta_retorno_nombre,
                           ret.origen  AS ruta_retorno_origen,
                           ret.destino AS ruta_retorno_destino,
                           e.nombre    AS empresa_nombre,
                           e.telefono  AS empresa_telefono,
                           e.direccion AS empresa_direccion,
                           e.email     AS empresa_email
                    FROM rutas r
                    JOIN empresas e ON r.rfc_empresa = e.rfc_empresa
                    LEFT JOIN rutas ret ON r.id_ruta_retorno = ret.id_ruta
                    WHERE r.id_ruta = ? AND r.activa = 1";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_ruta);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                sendResponse(404, ["error" => "Ruta no encontrada"]);
            }

            $row = $result->fetch_assoc();
            $row['es_tramo'] = 0;

            // Horarios
            $sql_h = "SELECT h.*,
                             c.nombre   AS conductor_nombre,
                             c.licencia AS conductor_licencia,
                             v.placa    AS vehiculo_placa,
                             v.modelo   AS vehiculo_modelo,
                             v.capacidad AS vehiculo_capacidad,
                             a.id_asignacion,
                             a.estado AS estado,
                             a.asientos_disp AS asientos_disponibles
                      FROM horarios h
                      LEFT JOIN asignaciones a
                             ON h.id_horario = a.id_horario
                            AND h.id_ruta    = a.id_ruta
                            AND a.activa     = 1
                            AND a.id_asignacion = (
                                SELECT id_asignacion FROM asignaciones a2
                                WHERE a2.id_horario = h.id_horario
                                  AND a2.id_ruta = h.id_ruta
                                  AND a2.activa = 1
                                ORDER BY a2.fecha DESC, a2.id_asignacion DESC
                                LIMIT 1
                            )
                      LEFT JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
                      LEFT JOIN vehiculos   v ON a.id_vehiculo   = v.id_vehiculo
                      WHERE h.id_ruta = ? AND h.tipo_dia = ?";

            $stmt_h = $conn->prepare($sql_h);
            $stmt_h->bind_param("is", $id_ruta, $tipo_dia_actual);
            $stmt_h->execute();
            $result_h = $stmt_h->get_result();

            // Paradas estructuradas
            $stmt_p = $conn->prepare(
                "SELECT nombre, orden, minutos_desde_origen
                 FROM   paradas_ruta
                 WHERE  id_ruta = ?
                 ORDER  BY orden ASC"
            );
            $stmt_p->bind_param("i", $id_ruta);
            $stmt_p->execute();
            $paradas_ruta = $stmt_p->get_result()->fetch_all(MYSQLI_ASSOC);
            $row['paradas_ruta'] = $paradas_ruta;

            $horarios = [];
            while ($horario = $result_h->fetch_assoc()) {
                if (!empty($horario['hora_salida'])) {
                    $horario['paradas_con_hora'] = array_map(function($p) use ($horario) {
                        return [
                            'nombre'               => $p['nombre'],
                            'orden'                => (int)$p['orden'],
                            'minutos_desde_origen' => (int)$p['minutos_desde_origen'],
                            'hora_estimada'        => _addMinutes($horario['hora_salida'], (int)$p['minutos_desde_origen']),
                        ];
                    }, $paradas_ruta);
                }
                $horarios[] = $horario;
            }

            $row['horarios'] = $horarios;
            $row['paradas_texto'] = $row['paradas'];
            $row['paradas'] = array_filter(
                explode(', ', $row['paradas'] ?? ''),
                fn($p) => $p !== ''
            );

            sendResponse(200, [$row]);
        }
    }

    // Manejar solicitud POST para buscar rutas
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendResponse(400, ["error" => "JSON inválido"]);
        }
        
        if (empty($data['action'])) {
            sendResponse(400, ["error" => "Acción no especificada"]);
        }

        if ($data['action'] === 'search_routes') {
            if (empty($data['origin']) || empty($data['destination'])) {
                sendResponse(400, ["error" => "Origen y destino son requeridos"]);
            }

            $origin      = trim($data['origin']);
            $destination = trim($data['destination']);

            // === Calcular el tipo de día actual basado en la fecha del servidor ===
            date_default_timezone_set('America/Mexico_City');
            $numeroDia = date('N'); // 1 = Lunes, 7 = Domingo
            if ($numeroDia >= 1 && $numeroDia <= 5) {
                $tipo_dia_actual = 'Lunes a Viernes';
            } else if ($numeroDia == 6) {
                $tipo_dia_actual = 'Sábado';
            } else {
                $tipo_dia_actual = 'Domingo';
            }

            // ── Búsqueda extendida ──────────────────────────────────────────────
            // Caso A (ruta completa): el usuario busca el origen y destino exactos
            //   de la ruta → devuelve la ruta sin ajuste de tiempo.
            //
            // Caso B (tramo): el origen o el destino coinciden con una parada
            //   registrada en paradas_ruta.  Se devuelve la ruta con los minutos
            //   desde el origen para embarque y bajada, y es_tramo = 1.
            //
            // Ambos casos se resuelven en UNA sola consulta con LEFT JOINs y
            // una condición OR en el WHERE.
            // ───────────────────────────────────────────────────────────────────
            $sql = "SELECT DISTINCT
                        r.id_ruta, r.nombre, r.origen, r.destino, r.rfc_empresa, r.paradas,
                        r.id_ruta_retorno,
                        COALESCE(bo.minutos_desde_origen, 0)  AS embarque_minutos,
                        COALESCE(bd.minutos_desde_origen, 0)  AS bajada_minutos,
                        bo.nombre                             AS parada_embarque,
                        bd.nombre                             AS parada_bajada,
                        ret.nombre  AS ruta_retorno_nombre,
                        ret.origen  AS ruta_retorno_origen,
                        ret.destino AS ruta_retorno_destino,
                        e.nombre    AS empresa_nombre,
                        e.telefono  AS empresa_telefono,
                        e.direccion AS empresa_direccion,
                        e.email     AS empresa_email
                    FROM rutas r
                    JOIN empresas e ON r.rfc_empresa = e.rfc_empresa
                    LEFT JOIN rutas ret ON r.id_ruta_retorno = ret.id_ruta
                    LEFT JOIN paradas_ruta bo ON bo.id_ruta = r.id_ruta AND bo.nombre = ?
                    LEFT JOIN paradas_ruta bd ON bd.id_ruta = r.id_ruta AND bd.nombre = ?
                    WHERE r.activa = 1
                      AND (
                            (r.origen = ? AND r.destino = ?)
                         OR (bo.id_parada IS NOT NULL AND bd.id_parada IS NOT NULL AND bo.orden < bd.orden)
                           )";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $origin, $destination, $origin, $destination);
            $stmt->execute();
            $result = $stmt->get_result();

            $routes = [];
            while ($row = $result->fetch_assoc()) {
                // Determinar si es un tramo parcial o la ruta completa
                $row['es_tramo'] = ($row['origen'] === $origin && $row['destino'] === $destination) ? 0 : 1;

                // Obtener horarios con información de conductor y vehículo
                // Se obtiene la asignación más reciente y activa para cada horario
                $sql_h = "SELECT h.*,
                                 c.nombre   AS conductor_nombre,
                                 c.licencia AS conductor_licencia,
                                 v.placa    AS vehiculo_placa,
                                 v.modelo   AS vehiculo_modelo,
                                 v.capacidad AS vehiculo_capacidad,
                                 a.id_asignacion,
                                 a.estado AS estado,
                                 a.asientos_disp AS asientos_disponibles
                          FROM horarios h
                          LEFT JOIN asignaciones a
                                 ON h.id_horario = a.id_horario
                                AND h.id_ruta    = a.id_ruta
                                AND a.activa     = 1
                                AND a.id_asignacion = (
                                    SELECT id_asignacion FROM asignaciones a2
                                    WHERE a2.id_horario = h.id_horario 
                                      AND a2.id_ruta = h.id_ruta
                                      AND a2.activa = 1
                                    ORDER BY a2.fecha DESC, a2.id_asignacion DESC
                                    LIMIT 1
                                )
                          LEFT JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
                          LEFT JOIN vehiculos   v ON a.id_vehiculo   = v.id_vehiculo
                          WHERE h.id_ruta = ? AND h.tipo_dia = ?";

                $stmt_h = $conn->prepare($sql_h);
                $stmt_h->bind_param("is", $row['id_ruta'], $tipo_dia_actual);
                $stmt_h->execute();
                $result_h = $stmt_h->get_result();

                // Paradas estructuradas (con orden y tiempos) — se obtienen UNA vez por ruta
                $stmt_p = $conn->prepare(
                    "SELECT nombre, orden, minutos_desde_origen
                     FROM   paradas_ruta
                     WHERE  id_ruta = ?
                     ORDER  BY orden ASC"
                );
                $stmt_p->bind_param("i", $row['id_ruta']);
                $stmt_p->execute();
                $paradas_ruta = $stmt_p->get_result()->fetch_all(MYSQLI_ASSOC);
                $row['paradas_ruta'] = $paradas_ruta;

                // Encontrar índices de embarque y bajada (para tramos)
                $idx_embarque = -1;
                $idx_bajada   = -1;
                if ($row['es_tramo']) {
                    foreach ($paradas_ruta as $i => $p) {
                        if ($p['nombre'] === $row['parada_embarque']) $idx_embarque = $i;
                        if ($p['nombre'] === $row['parada_bajada'])   $idx_bajada   = $i;
                    }
                }

                $horarios = [];
                while ($horario = $result_h->fetch_assoc()) {
                    if (!empty($horario['hora_salida'])) {
                        if ($row['es_tramo'] && $idx_embarque !== -1 && $idx_bajada !== -1) {
                            // Abordaje: hora_salida + minutos_acumulados de la parada de embarque
                            $horario['hora_abordaje'] = _addMinutes(
                                $horario['hora_salida'],
                                (int)$paradas_ruta[$idx_embarque]['minutos_desde_origen']
                            );
                            // Bajada: hora_salida + minutos_acumulados de la parada de bajada
                            $horario['hora_bajada'] = _addMinutes(
                                $horario['hora_salida'],
                                (int)$paradas_ruta[$idx_bajada]['minutos_desde_origen']
                            );
                        }

                        // Hora estimada por parada para este horario (útil para web y app)
                        $horario['paradas_con_hora'] = array_map(function($p) use ($horario) {
                            return [
                                'nombre'               => $p['nombre'],
                                'orden'                => (int)$p['orden'],
                                'minutos_desde_origen' => (int)$p['minutos_desde_origen'],
                                'hora_estimada'        => _addMinutes($horario['hora_salida'], (int)$p['minutos_desde_origen']),
                            ];
                        }, $paradas_ruta);
                    }
                    $horarios[] = $horario;
                }

                $row['horarios'] = $horarios;

                // Paradas legacy como array (compatibilidad)
                $row['paradas_texto'] = $row['paradas'];
                $row['paradas'] = array_filter(
                    explode(', ', $row['paradas'] ?? ''),
                    fn($p) => $p !== ''
                );

                $routes[] = $row;
            }

            sendResponse(200, $routes);
        }
    }

    sendResponse(400, ["error" => "Solicitud no válida"]);

} catch (Throwable $e) {
    sendResponse(500, ["error" => "Error interno del servidor: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>