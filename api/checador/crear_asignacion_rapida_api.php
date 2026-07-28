<?php
require_once '../../config/api_middleware.php';
aplicarCorsGoWay();

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
        sendResponse(500, ["success" => false, "error" => "Error interno en la conexión con base de datos"]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(405, ["success" => false, "error" => "Método no permitido. Use POST"]);
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (empty($data)) {
        $data = $_POST;
    }

    if (empty($data['rfc_checador']) && empty($data['rfc_empresa'])) {
        sendResponse(400, ["success" => false, "error" => "El parámetro 'rfc_checador' o 'rfc_empresa' es requerido"]);
    }

    $rfc_empresa = '';
    if (!empty($data['rfc_checador'])) {
        $rfc_checador = trim($data['rfc_checador']);
        $stmt_ch = $conn->prepare("SELECT rfc_empresa FROM checadores WHERE rfc_checador = ? AND activo = 1 LIMIT 1");
        if ($stmt_ch) {
            $stmt_ch->bind_param("s", $rfc_checador);
            $stmt_ch->execute();
            $res_ch = $stmt_ch->get_result();
            if ($res_ch->num_rows > 0) {
                $rfc_empresa = $res_ch->fetch_assoc()['rfc_empresa'];
            }
            $stmt_ch->close();
        }
    }
    if (empty($rfc_empresa) && !empty($data['rfc_empresa'])) {
        $rfc_empresa = trim($data['rfc_empresa']);
    }
    if (empty($rfc_empresa)) {
        sendResponse(403, ["success" => false, "error" => "Empresa o checador no válido/encontrado"]);
    }

    $id_vehiculo = 0;
    $capacidad   = 42;
    if (!empty($data['id_vehiculo']) && is_numeric($data['id_vehiculo'])) {
        $id_vehiculo = (int)$data['id_vehiculo'];
        $resV = $conn->query("SELECT capacidad FROM vehiculos WHERE id_vehiculo = $id_vehiculo LIMIT 1");
        if ($resV && $resV->num_rows > 0) $capacidad = (int)$resV->fetch_assoc()['capacidad'];
    } elseif (!empty($data['placa'])) {
        $placa = $conn->real_escape_string(strtoupper(trim($data['placa'])));
        $resV = $conn->query("SELECT id_vehiculo, capacidad FROM vehiculos WHERE placa = '$placa' AND rfc_empresa = '$rfc_empresa' LIMIT 1");
        if ($resV && $resV->num_rows > 0) {
            $rowV = $resV->fetch_assoc();
            $id_vehiculo = (int)$rowV['id_vehiculo'];
            $capacidad   = (int)$rowV['capacidad'];
        }
    }

    if ($id_vehiculo <= 0) {
        sendResponse(400, ["success" => false, "error" => "Vehículo no válido o no especificado (id_vehiculo o placa requeridos)"]);
    }

    $id_ruta = !empty($data['id_ruta']) && is_numeric($data['id_ruta']) ? (int)$data['id_ruta'] : 0;
    if ($id_ruta <= 0) {
        $resR = $conn->query("SELECT id_ruta FROM rutas WHERE rfc_empresa = '$rfc_empresa' LIMIT 1");
        if ($resR && $resR->num_rows > 0) $id_ruta = (int)$resR->fetch_assoc()['id_ruta'];
    }

    $rfc_conductor = '';
    if (!empty($data['rfc_conductor'])) {
        $rfc_conductor = trim($data['rfc_conductor']);
    } elseif (!empty($data['id_conductor'])) {
        $idc = $conn->real_escape_string(trim($data['id_conductor']));
        $resC = $conn->query("SELECT rfc_conductor FROM conductores WHERE rfc_conductor = '$idc' OR id_conductor = '$idc' LIMIT 1");
        if ($resC && $resC->num_rows > 0) $rfc_conductor = $resC->fetch_assoc()['rfc_conductor'];
        else $rfc_conductor = $idc;
    } elseif (!empty($data['conductor'])) {
        $nom = $conn->real_escape_string(trim($data['conductor']));
        $resC = $conn->query("SELECT rfc_conductor FROM conductores WHERE nombre LIKE '%$nom%' LIMIT 1");
        if ($resC && $resC->num_rows > 0) $rfc_conductor = $resC->fetch_assoc()['rfc_conductor'];
    }

    $id_horario = 0;
    if (!empty($data['hora_salida']) && $id_ruta > 0) {
        $hora_salida = date('H:i:s', strtotime(trim($data['hora_salida'])));
        date_default_timezone_set('America/Mexico_City');
        $numeroDia = (int)date('N');
        if ($numeroDia >= 1 && $numeroDia <= 5) $tipo_dia = 'Lunes a Viernes';
        elseif ($numeroDia === 6)               $tipo_dia = 'Sábado';
        else                                    $tipo_dia = 'Domingo';

        $resH = $conn->query("SELECT id_horario FROM horarios WHERE id_ruta = $id_ruta AND hora_salida = '$hora_salida' AND tipo_dia = '$tipo_dia' LIMIT 1");
        if ($resH && $resH->num_rows > 0) {
            $id_horario = (int)$resH->fetch_assoc()['id_horario'];
        } else {
            $conn->query("INSERT INTO horarios (id_ruta, hora_salida, tipo_dia) VALUES ($id_ruta, '$hora_salida', '$tipo_dia')");
            if ($conn->insert_id > 0) $id_horario = $conn->insert_id;
        }
    }

    $fecha = date('Y-m-d');
    $estado = !empty($data['estado']) ? trim($data['estado']) : 'programado';
    $activa = 1;

    $stmt = $conn->prepare("INSERT INTO asignaciones (rfc_empresa, id_vehiculo, rfc_conductor, id_ruta, id_horario, fecha, estado, asientos_disp, activa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        sendResponse(500, ["success" => false, "error" => "Error preparando inserción de asignación: " . $conn->error]);
    }
    $stmt->bind_param("sisiissii", $rfc_empresa, $id_vehiculo, $rfc_conductor, $id_ruta, $id_horario, $fecha, $estado, $capacidad, $activa);
    
    if ($stmt->execute()) {
        $insert_id = $conn->insert_id;
        $stmt->close();
        sendResponse(200, [
            "success" => true,
            "id_asignacion" => (string)$insert_id,
            "message" => "Asignación rápida creada exitosamente"
        ]);
    } else {
        sendResponse(500, ["success" => false, "error" => "Error al insertar asignación rápida: " . $stmt->error]);
    }

} catch (Exception $e) {
    sendResponse(500, ["success" => false, "error" => "Error interno: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
