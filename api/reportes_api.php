<?php
// Iniciar sesión para detectar el rol y la empresa del usuario logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar preflight CORS
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

require_once '../config/conexion_bd.php';
require_once '../config/opciones_reportes.php';

try {
    $conn = $conexion;

    if ($conn->connect_error) {
        sendResponse(500, ["error" => "Error de conexión: " . $conn->connect_error]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Función auxiliar: obtiene datos completos de una asignación por ID o placa
    // ────────────────────────────────────────────────────────────────────────
    function getAsignacionData($conn, $by, $value) {
        $base_sql = "SELECT
                    a.id_asignacion,
                    a.id_vehiculo,
                    a.rfc_conductor,
                    a.id_ruta,
                    r.id_ruta_retorno,
                    v.placa         AS vehiculo_placa,
                    v.modelo        AS vehiculo_modelo,
                    c.nombre        AS conductor_nombre,
                    c.rfc_conductor AS conductor_rfc,
                    r.nombre        AS ruta_nombre,
                    r.origen,
                    r.destino,
                    ret.nombre      AS ruta_retorno_nombre,
                    ret.origen      AS ruta_retorno_origen,
                    ret.destino     AS ruta_retorno_destino
                FROM asignaciones a
                JOIN vehiculos   v ON a.id_vehiculo  = v.id_vehiculo
                JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
                JOIN rutas       r ON a.id_ruta       = r.id_ruta
                LEFT JOIN rutas  ret ON r.id_ruta_retorno = ret.id_ruta";

        if ($by === 'id') {
            $sql  = $base_sql . " WHERE a.id_asignacion = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) return null;
            $stmt->bind_param("i", $value);
        } else {
            // Por placa: trae la asignación activa más reciente del vehículo
            $sql  = $base_sql . " WHERE v.placa = ? AND a.activa = 1 ORDER BY a.fecha DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            if (!$stmt) return null;
            $stmt->bind_param("s", $value);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->num_rows > 0 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    // ─────────────────────────────────────────────────────────────
    // GET ?action=get_assignment_data&placa=ABC123
    //  ó  ?action=get_assignment_data&id_asignacion=X
    // Devuelve: placa del vehículo, nombre del conductor y datos de ruta
    //
    // GET ?action=get_reports&id_usuario=X
    // Devuelve los reportes creados por ese usuario (solo si rol = 2)
    // ─────────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if (!in_array($action, ['get_assignment_data', 'get_reports', 'get_summary', 'get_options'])) {
            sendResponse(400, ["error" => "Acción no válida."]);
        }

        // ── GET OPTIONS ──────────────────────────────────────────
        if ($action === 'get_options') {
            global $TIPOS_INCIDENCIA, $NIVELES_GRAVEDAD;
            
            // Format for app consumption (array of objects)
            $tipos_arr = [];
            foreach ($TIPOS_INCIDENCIA as $key => $val) {
                $tipos_arr[] = ["id" => $key, "nombre" => $val];
            }
            
            $gravedades_arr = [];
            foreach ($NIVELES_GRAVEDAD as $key => $val) {
                $gravedades_arr[] = ["id" => $key, "nombre" => $val];
            }

            sendResponse(200, [
                "success" => true,
                "tipos_incidencia" => $tipos_arr,
                "niveles_gravedad" => $gravedades_arr
            ]);
        }

        // ── GET SUMMARY ──────────────────────────────────────────
        if ($action === 'get_summary') {
            // Determinar filtro multi-tenant
            $is_empresa = isset($_SESSION['rol']) && $_SESSION['rol'] == 4 && !empty($_SESSION['rfc_empresa']);
            $rfc_filter = $is_empresa ? $_SESSION['rfc_empresa'] : null;

            // $base_where = JOIN + WHERE para queries sin join de vehiculos propio
            // $where_and  = solo la condición AND adicional (para queries que ya hacen JOIN vehiculos v)
            if ($rfc_filter) {
                $base_where = "JOIN vehiculos v_f ON rep.id_vehiculo = v_f.id_vehiculo WHERE rep.archivado = 0 AND v_f.rfc_empresa = '$rfc_filter'";
                $where_and  = "AND v.rfc_empresa = '$rfc_filter'";
            } else {
                $base_where = "WHERE rep.archivado = 0";
                $where_and  = "";
            }

            // Totales generales
            $r = $conn->query("SELECT COUNT(*) AS total,
                SUM(rep.estado='pendiente')  AS pendientes,
                SUM(rep.estado='en-proceso') AS en_proceso,
                SUM(rep.estado='resuelto')   AS resueltos
                FROM reportes rep $base_where");
            $totales = $r->fetch_assoc();

            // Por gravedad
            $r = $conn->query("SELECT rep.gravedad, COUNT(*) AS total FROM reportes rep $base_where GROUP BY rep.gravedad ORDER BY FIELD(rep.gravedad,'critica','alta','media','baja')");
            $por_gravedad = [];
            while ($row = $r->fetch_assoc()) $por_gravedad[] = $row;

            // Por tipo de incidente
            $r = $conn->query("SELECT rep.tipo_incidente, COUNT(*) AS total FROM reportes rep $base_where GROUP BY rep.tipo_incidente ORDER BY total DESC");
            $por_tipo = [];
            while ($row = $r->fetch_assoc()) $por_tipo[] = $row;

            // Top 5 conductores con más incidentes
            $r = $conn->query("SELECT c.nombre, COUNT(*) AS total
                FROM reportes rep
                JOIN conductores c ON rep.rfc_conductor = c.rfc_conductor
                JOIN vehiculos v ON rep.id_vehiculo = v.id_vehiculo
                WHERE rep.archivado = 0 $where_and
                GROUP BY rep.rfc_conductor ORDER BY total DESC LIMIT 5");
            $top_conductores = [];
            while ($row = $r->fetch_assoc()) $top_conductores[] = $row;

            // Top 5 rutas con más incidentes
            $r = $conn->query("SELECT ru.nombre, COUNT(*) AS total
                FROM reportes rep
                JOIN rutas ru ON rep.id_ruta = ru.id_ruta
                JOIN vehiculos v ON rep.id_vehiculo = v.id_vehiculo
                WHERE rep.archivado = 0 $where_and
                GROUP BY rep.id_ruta ORDER BY total DESC LIMIT 5");
            $top_rutas = [];
            while ($row = $r->fetch_assoc()) $top_rutas[] = $row;

            // Top 5 vehículos con más incidentes
            $r = $conn->query("SELECT CONCAT(v.placa, ' - ', v.modelo) AS vehiculo, COUNT(*) AS total
                FROM reportes rep JOIN vehiculos v ON rep.id_vehiculo = v.id_vehiculo
                WHERE rep.archivado = 0 $where_and
                GROUP BY rep.id_vehiculo ORDER BY total DESC LIMIT 5");
            $top_vehiculos = [];
            while ($row = $r->fetch_assoc()) $top_vehiculos[] = $row;

            // Incidentes por día de la semana (1=Dom…7=Sáb en MySQL)
            $r = $conn->query("SELECT DAYOFWEEK(rep.fecha_incidente) AS dia_num, COUNT(*) AS total
                FROM reportes rep $base_where GROUP BY dia_num ORDER BY dia_num");
            $por_dia_raw = [];
            while ($row = $r->fetch_assoc()) $por_dia_raw[$row['dia_num']] = (int)$row['total'];
            $dias_labels = [2=>'Lun',3=>'Mar',4=>'Mié',5=>'Jue',6=>'Vie',7=>'Sáb',1=>'Dom'];
            $por_dia_semana = [];
            foreach ($dias_labels as $num => $label) {
                $por_dia_semana[] = ['dia' => $label, 'total' => $por_dia_raw[$num] ?? 0];
            }

            // Tiempo promedio de resolución (días)
            $tiempo_res = null;
            $cols = $conn->query("SHOW COLUMNS FROM reportes LIKE 'updated_at'");
            if ($cols && $cols->num_rows > 0) {
                $r = $conn->query("SELECT ROUND(AVG(DATEDIFF(rep.updated_at, rep.created_at)), 1) AS promedio_dias
                    FROM reportes rep
                    JOIN vehiculos v ON rep.id_vehiculo = v.id_vehiculo
                    WHERE rep.estado = 'resuelto' AND rep.archivado = 0 AND rep.updated_at IS NOT NULL AND rep.updated_at != rep.created_at $where_and");
                if ($r) $tiempo_res = $r->fetch_assoc()['promedio_dias'];
            }

            sendResponse(200, [
                "success"          => true,
                "generado_en"      => date('Y-m-d H:i:s'),
                "totales"          => $totales,
                "por_gravedad"     => $por_gravedad,
                "por_tipo"         => $por_tipo,
                "top_conductores"  => $top_conductores,
                "top_rutas"        => $top_rutas,
                "top_vehiculos"    => $top_vehiculos,
                "por_dia_semana"   => $por_dia_semana,
                "tiempo_resolucion"=> $tiempo_res,
                "rango_fechas"     => $rango ?? null
            ]);
        }


        // ── GET REPORTS ──────────────────────────────────────────
        if ($action === 'get_reports') {
            $id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
            $rfc_checador = isset($_GET['rfc_checador']) ? trim($_GET['rfc_checador']) : '';

            if ($id_usuario <= 0 && empty($rfc_checador)) {
                sendResponse(400, ["error" => "id_usuario o rfc_checador es requerido"]);
            }

            if ($id_usuario > 0) {
                // Verificar que el usuario existe y tiene rol = 2 (usuario normal)
                $stmt_rol = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND rol = 2");
                if (!$stmt_rol) {
                    sendResponse(500, ["error" => "Error al preparar consulta: " . $conn->error]);
                }
                $stmt_rol->bind_param("i", $id_usuario);
                $stmt_rol->execute();
                if ($stmt_rol->get_result()->num_rows === 0) {
                    sendResponse(403, ["error" => "Usuario no encontrado o no tiene permisos para consultar reportes"]);
                }
                $stmt_rol->close();

                $condicion = "rep.id_usuario = ?";
                $param_type = "i";
                $param_val = $id_usuario;
            } else {
                // Verificar que el checador existe
                $stmt_checador = $conn->prepare("SELECT rfc_checador FROM checadores WHERE rfc_checador = ?");
                if (!$stmt_checador) {
                    sendResponse(500, ["error" => "Error al preparar consulta: " . $conn->error]);
                }
                $stmt_checador->bind_param("s", $rfc_checador);
                $stmt_checador->execute();
                if ($stmt_checador->get_result()->num_rows === 0) {
                    sendResponse(403, ["error" => "Checador no encontrado"]);
                }
                $stmt_checador->close();

                $condicion = "rep.rfc_checador = ?";
                $param_type = "s";
                $param_val = $rfc_checador;
            }

            $sql_rep = "SELECT
                            rep.tipo_incidente,
                            rep.fecha_incidente,
                            rep.descripcion,
                            rep.gravedad,
                            v.placa         AS vehiculo_placa,
                            v.modelo        AS vehiculo_modelo,
                            c.nombre        AS conductor_nombre,
                            r.nombre        AS ruta_nombre,
                            r.origen,
                            r.destino
                        FROM reportes rep
                        JOIN vehiculos   v ON rep.id_vehiculo  = v.id_vehiculo
                        JOIN conductores c ON rep.rfc_conductor = c.rfc_conductor
                        JOIN rutas       r ON rep.id_ruta       = r.id_ruta
                        WHERE $condicion
                        ORDER BY rep.fecha_incidente DESC";

            $stmt_rep = $conn->prepare($sql_rep);
            if (!$stmt_rep) {
                sendResponse(500, ["error" => "Error al preparar consulta: " . $conn->error]);
            }
            $stmt_rep->bind_param($param_type, $param_val);
            $stmt_rep->execute();
            $result_rep = $stmt_rep->get_result();

            $reportes = [];
            while ($row = $result_rep->fetch_assoc()) {
                $reportes[] = $row;
            }
            $stmt_rep->close();

            sendResponse(200, [
                "success"  => true,
                "total"    => count($reportes),
                "reportes" => $reportes
            ]);
        }

        // ── GET ASSIGNMENT DATA ───────────────────────────────────
        if ($action !== 'get_assignment_data') {
            sendResponse(400, ["error" => "Acción no válida"]);
        }

        $placa         = isset($_GET['placa'])         ? strtoupper(trim($_GET['placa'])) : '';
        $id_asignacion = isset($_GET['id_asignacion']) ? intval($_GET['id_asignacion'])   : 0;

        if (empty($placa) && $id_asignacion <= 0) {
            sendResponse(400, ["error" => "Debes enviar 'placa' o 'id_asignacion'"]);
        }

        // Placa tiene prioridad si se envían ambos
        if (!empty($placa)) {
            $asignacion = getAsignacionData($conn, 'placa', $placa);
            if (!$asignacion) {
                sendResponse(404, ["error" => "No se encontró ninguna asignación activa para la placa '$placa'"]);
            }
        } else {
            $asignacion = getAsignacionData($conn, 'id', $id_asignacion);
            if (!$asignacion) {
                sendResponse(404, ["error" => "No se encontró ninguna asignación con id_asignacion=$id_asignacion"]);
            }
        }

        sendResponse(200, [
            "success" => true,
            "data"    => $asignacion
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST — Crear un nuevo reporte
    // Body JSON esperado (usa placa O id_asignacion, no ambos):
    //   placa          (string)  — placa del vehículo (recomendado para usuarios)
    //   id_asignacion  (int)     — alternativa directa si ya se conoce
    //   id_usuario     (int)     — quién hace el reporte (opcional si se envía rfc_checador)
    //   rfc_checador   (string)  — quién hace el reporte (opcional si se envía id_usuario)
    //   tipo_incidente (string)
    //   fecha_hora     (string)  — formato YYYY-MM-DD HH:MM:SS
    //   descripcion    (string)
    //   gravedad       (string)  — baja | media | alta | critica
    // ─────────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');

        if (empty($json)) {
            sendResponse(400, ["error" => "No se recibieron datos en el cuerpo de la solicitud"]);
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            sendResponse(400, ["error" => "JSON inválido: " . json_last_error_msg()]);
        }

        // Validar campos comunes siempre requeridos
        $campos_requeridos = ['tipo_incidente', 'fecha_hora', 'descripcion', 'gravedad'];
        foreach ($campos_requeridos as $campo) {
            if (!isset($data[$campo]) || (is_string($data[$campo]) && trim($data[$campo]) === '')) {
                sendResponse(400, ["error" => "El campo '$campo' es requerido"]);
            }
        }

        $placa_post    = isset($data['placa'])         ? strtoupper(trim($data['placa'])) : '';
        $id_asignacion = isset($data['id_asignacion']) ? intval($data['id_asignacion'])   : 0;

        if (empty($placa_post) && $id_asignacion <= 0) {
            sendResponse(400, ["error" => "Debes enviar 'placa' o 'id_asignacion'"]);
        }

        $id_usuario     = isset($data['id_usuario']) ? intval($data['id_usuario']) : 0;
        $rfc_checador   = isset($data['rfc_checador']) ? trim($data['rfc_checador']) : '';
        $tipo_incidente = trim($data['tipo_incidente']);
        $fecha_hora     = trim($data['fecha_hora']);
        $descripcion    = trim($data['descripcion']);
        $gravedad       = strtolower(trim($data['gravedad']));
        $es_retorno     = isset($data['es_retorno']) ? filter_var($data['es_retorno'], FILTER_VALIDATE_BOOLEAN) : false;

        if ($id_usuario <= 0 && empty($rfc_checador)) {
            sendResponse(400, ["error" => "id_usuario o rfc_checador es requerido"]);
        }

        // Validar tipo de incidente
        global $TIPOS_INCIDENCIA, $NIVELES_GRAVEDAD;
        if (!array_key_exists($tipo_incidente, $TIPOS_INCIDENCIA)) {
            sendResponse(400, ["error" => "Tipo de incidente inválido. Valores aceptados: " . implode(', ', array_keys($TIPOS_INCIDENCIA))]);
        }

        // Validar nivel de gravedad
        if (!array_key_exists($gravedad, $NIVELES_GRAVEDAD)) {
            sendResponse(400, ["error" => "Gravedad inválida. Valores aceptados: " . implode(', ', array_keys($NIVELES_GRAVEDAD))]);
        }

        // Resolver asignación: placa tiene prioridad
        if (!empty($placa_post)) {
            $asig = getAsignacionData($conn, 'placa', $placa_post);
            if (!$asig) {
                sendResponse(404, ["error" => "No se encontró ninguna asignación activa para la placa '$placa_post'"]);
            }
        } else {
            $asig = getAsignacionData($conn, 'id', $id_asignacion);
            if (!$asig) {
                sendResponse(404, ["error" => "No se encontró ninguna asignación con id_asignacion=$id_asignacion"]);
            }
        }

        $id_vehiculo   = intval($asig['id_vehiculo']);
        $rfc_conductor = $asig['rfc_conductor'];
        $id_ruta       = ($es_retorno && !empty($asig['id_ruta_retorno'])) ? intval($asig['id_ruta_retorno']) : intval($asig['id_ruta']);

        if ($id_usuario > 0) {
            // Verificar que el usuario existe
            $stmt_usr = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
            if (!$stmt_usr) {
                sendResponse(500, ["error" => "Error al preparar consulta: " . $conn->error]);
            }
            $stmt_usr->bind_param("i", $id_usuario);
            $stmt_usr->execute();
            $result_usr = $stmt_usr->get_result();
            if ($result_usr->num_rows === 0) {
                sendResponse(404, ["error" => "Usuario no encontrado"]);
            }
            $stmt_usr->close();

            // Insertar el reporte con id_usuario
            $sql_insert = "INSERT INTO reportes
                               (id_vehiculo, rfc_conductor, id_ruta, tipo_incidente,
                                fecha_incidente, descripcion, gravedad, id_usuario)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            if (!$stmt_insert) {
                sendResponse(500, ["error" => "Error al preparar inserción: " . $conn->error]);
            }
            $stmt_insert->bind_param("isissssi", $id_vehiculo, $rfc_conductor, $id_ruta, $tipo_incidente, $fecha_hora, $descripcion, $gravedad, $id_usuario);
        } else {
            // Verificar que el checador existe
            $stmt_checador = $conn->prepare("SELECT rfc_checador FROM checadores WHERE rfc_checador = ?");
            if (!$stmt_checador) {
                sendResponse(500, ["error" => "Error al preparar consulta: " . $conn->error]);
            }
            $stmt_checador->bind_param("s", $rfc_checador);
            $stmt_checador->execute();
            if ($stmt_checador->get_result()->num_rows === 0) {
                sendResponse(404, ["error" => "Checador no encontrado"]);
            }
            $stmt_checador->close();

            // Insertar el reporte con rfc_checador
            $sql_insert = "INSERT INTO reportes
                               (id_vehiculo, rfc_conductor, id_ruta, tipo_incidente,
                                fecha_incidente, descripcion, gravedad, rfc_checador)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            if (!$stmt_insert) {
                sendResponse(500, ["error" => "Error al preparar inserción: " . $conn->error]);
            }
            $stmt_insert->bind_param("isisssss", $id_vehiculo, $rfc_conductor, $id_ruta, $tipo_incidente, $fecha_hora, $descripcion, $gravedad, $rfc_checador);
        }

        if ($stmt_insert->execute()) {
            $id_reporte = $stmt_insert->insert_id;
            $stmt_insert->close();

            sendResponse(201, [
                "success"    => true,
                "message"    => "Reporte creado exitosamente",
                "id_reporte" => $id_reporte
            ]);
        } else {
            throw new Exception("Error al insertar el reporte: " . $stmt_insert->error);
        }
    }

    // Método no soportado
    sendResponse(405, ["error" => "Método no permitido. Use GET o POST"]);

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno del servidor: " . $e->getMessage()]);
}
