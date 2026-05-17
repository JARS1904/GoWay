<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

$ESTADOS_PERMITIDOS = ['programado', 'en_ruta', 'completado', 'cancelado', 'retrasado'];

require_once '../../config/conexion_bd.php';

try {
    $conn = $conexion;
    if ($conn->connect_error) {
        sendResponse(500, ["error" => "Error de conexión: " . $conn->connect_error]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(405, ["error" => "Método no permitido. Use POST"]);
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (empty($data)) {
        $data = $_POST;
    }

    if (empty($data['id_asignacion'])) {
        sendResponse(400, ["error" => "El parámetro 'id_asignacion' es requerido"]);
    }
    if (empty($data['estado'])) {
        sendResponse(400, ["error" => "El parámetro 'estado' es requerido"]);
    }
    if (empty($data['rfc_checador'])) {
        sendResponse(400, ["error" => "El parámetro 'rfc_checador' es requerido"]);
    }

    $id_asignacion = (int)$data['id_asignacion'];
    $estado        = trim((string)$data['estado']);
    $rfc_checador  = trim((string)$data['rfc_checador']);

    if (!in_array($estado, $ESTADOS_PERMITIDOS, true)) {
        sendResponse(400, [
            "error"   => "Estado no válido",
            "validos" => $ESTADOS_PERMITIDOS,
        ]);
    }

    $stmt_ch = $conn->prepare("SELECT rfc_empresa FROM checadores WHERE rfc_checador = ? AND activo = 1 LIMIT 1");
    if (!$stmt_ch) {
        sendResponse(500, ["error" => "Error preparando consulta del checador"]);
    }
    $stmt_ch->bind_param("s", $rfc_checador);
    $stmt_ch->execute();
    $res_ch = $stmt_ch->get_result();
    if ($res_ch->num_rows === 0) {
        sendResponse(403, ["error" => "Checador no encontrado o inactivo"]);
    }
    $rfc_empresa_checador = $res_ch->fetch_assoc()['rfc_empresa'];
    $stmt_ch->close();

    $sql = "UPDATE asignaciones SET estado = ? WHERE id_asignacion = ? AND rfc_empresa = ? AND activa = 1";
    $stmt  = $conn->prepare($sql);
    if (!$stmt) {
        sendResponse(500, ["error" => "Error al preparar la consulta: " . $conn->error]);
    }
    $stmt->bind_param("sis", $estado, $id_asignacion, $rfc_empresa_checador);

    if (!$stmt->execute()) {
        sendResponse(500, ["error" => "Error al ejecutar la actualización: " . $stmt->error]);
    }

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        sendResponse(200, [
            "success" => true,
            "message" => "Estado actualizado correctamente",
            "estado"  => $estado,
        ]);
    }

    $stmt->close();

    $check = $conn->prepare("SELECT id_asignacion, estado FROM asignaciones WHERE id_asignacion = ? AND rfc_empresa = ? LIMIT 1");
    $check->bind_param("is", $id_asignacion, $rfc_empresa_checador);
    $check->execute();
    $row = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$row) {
        sendResponse(404, ["error" => "No se encontró la asignación o no pertenece a tu empresa"]);
    }
    if ($row['estado'] === $estado) {
        sendResponse(200, [
            "success" => true,
            "message" => "El estado ya estaba actualizado",
            "estado"  => $estado,
        ]);
    }

    sendResponse(403, ["error" => "No se pudo actualizar (asignación inactiva o sin permiso)"]);
} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
