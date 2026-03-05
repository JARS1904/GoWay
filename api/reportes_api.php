<?php
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
                    v.placa         AS vehiculo_placa,
                    v.modelo        AS vehiculo_modelo,
                    c.nombre        AS conductor_nombre,
                    c.rfc_conductor AS conductor_rfc,
                    r.nombre        AS ruta_nombre,
                    r.origen,
                    r.destino
                FROM asignaciones a
                JOIN vehiculos   v ON a.id_vehiculo  = v.id_vehiculo
                JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
                JOIN rutas       r ON a.id_ruta       = r.id_ruta";

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

        if (!in_array($action, ['get_assignment_data', 'get_reports'])) {
            sendResponse(400, ["error" => "Acción no válida. Use action=get_assignment_data o action=get_reports"]);
        }

        // ── GET REPORTS ──────────────────────────────────────────
        if ($action === 'get_reports') {
            $id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;

            if ($id_usuario <= 0) {
                sendResponse(400, ["error" => "id_usuario es requerido y debe ser un entero positivo"]);
            }

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
                        WHERE rep.id_usuario = ?
                        ORDER BY rep.fecha_incidente DESC";

            $stmt_rep = $conn->prepare($sql_rep);
            if (!$stmt_rep) {
                sendResponse(500, ["error" => "Error al preparar consulta: " . $conn->error]);
            }
            $stmt_rep->bind_param("i", $id_usuario);
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
    //   id_usuario     (int)     — quién hace el reporte
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
        $campos_requeridos = ['id_usuario', 'tipo_incidente', 'fecha_hora', 'descripcion', 'gravedad'];
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

        $id_usuario     = intval($data['id_usuario']);
        $tipo_incidente = trim($data['tipo_incidente']);
        $fecha_hora     = trim($data['fecha_hora']);
        $descripcion    = trim($data['descripcion']);
        $gravedad       = strtolower(trim($data['gravedad']));

        if ($id_usuario <= 0) {
            sendResponse(400, ["error" => "id_usuario debe ser un entero positivo"]);
        }

        // Validar nivel de gravedad
        $gravedad_validos = ['baja', 'media', 'alta', 'critica'];
        if (!in_array($gravedad, $gravedad_validos)) {
            sendResponse(400, ["error" => "Gravedad inválida. Valores aceptados: baja, media, alta, critica"]);
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
        $id_ruta       = intval($asig['id_ruta']);

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

        // Insertar el reporte
        $sql_insert = "INSERT INTO reportes
                           (id_vehiculo, rfc_conductor, id_ruta, tipo_incidente,
                            fecha_incidente, descripcion, gravedad, id_usuario)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_insert = $conn->prepare($sql_insert);
        if (!$stmt_insert) {
            sendResponse(500, ["error" => "Error al preparar inserción: " . $conn->error]);
        }

        // i = int, s = string
        // id_vehiculo(i), rfc_conductor(s), id_ruta(i), tipo_incidente(s),
        // fecha_incidente(s), descripcion(s), gravedad(s), id_usuario(i)
        $stmt_insert->bind_param(
            "isissssi",
            $id_vehiculo,
            $rfc_conductor,
            $id_ruta,
            $tipo_incidente,
            $fecha_hora,
            $descripcion,
            $gravedad,
            $id_usuario
        );

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
