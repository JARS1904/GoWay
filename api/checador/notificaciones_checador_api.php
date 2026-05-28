<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

    $method = $_SERVER['REQUEST_METHOD'];
    $data = [];

    if ($method === 'GET') {
        $data = $_GET;
    } elseif ($method === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            $data = $_POST;
        }
        if (empty($data)) {
            $data = $_GET;
        }
    }

    $action = $data['action'] ?? '';
    $rfc_checador = $data['rfc_checador'] ?? '';

    if (!$action) {
        sendResponse(400, ["error" => "Acción no especificada"]);
    }

    // ── 1. Obtener notificaciones ────────────────────────────────────────
    if ($action === 'get_notifications') {
        if (empty($rfc_checador)) {
            sendResponse(400, ["error" => "RFC de checador requerido"]);
        }

        // Un checador recibe notificaciones si:
        // a) Es global del Super Admin (rfc_empresa IS NULL y para checadores)
        // b) Viene de la empresa a la que pertenece el checador
        // c) Está dirigida directamente a él
        $sql = "SELECT n.* FROM notificaciones n
                WHERE n.destinatario_tipo = 'checadores'
                AND (
                    (n.rfc_empresa IS NULL AND n.rfc_checador IS NULL)
                    OR n.rfc_checador = ?
                    OR (
                        n.rfc_empresa IS NOT NULL
                        AND n.rfc_empresa = (
                            SELECT rfc_empresa FROM checadores WHERE rfc_checador = ? LIMIT 1
                        )
                    )
                )
                ORDER BY n.fecha_creacion DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $rfc_checador, $rfc_checador);
        $stmt->execute();
        $result = $stmt->get_result();

        $notificaciones = [];
        $unread_count   = 0;

        while ($row = $result->fetch_assoc()) {
            $notificaciones[] = $row;
            if ($row['leido'] == 0) {
                $unread_count++;
            }
        }

        sendResponse(200, [
            "success"        => true,
            "notificaciones" => $notificaciones,
            "unread_count"   => $unread_count
        ]);
        $stmt->close();
    }

    // ── 2. Marcar como leída ─────────────────────────────────────────────
    elseif ($action === 'mark_as_read') {
        if (empty($rfc_checador)) {
            sendResponse(400, ["error" => "RFC de checador requerido"]);
        }

        $id_notificacion = isset($data['id_notificacion']) ? intval($data['id_notificacion']) : 0;

        if ($id_notificacion > 0) {
            $sql = "UPDATE notificaciones SET leido = 1
                    WHERE id_notificacion = ? AND destinatario_tipo = 'checadores'
                    AND (
                        (rfc_empresa IS NULL AND rfc_checador IS NULL)
                        OR rfc_checador = ?
                        OR (rfc_empresa IS NOT NULL AND rfc_empresa = (
                            SELECT rfc_empresa FROM checadores WHERE rfc_checador = ? LIMIT 1
                        ))
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $id_notificacion, $rfc_checador, $rfc_checador);
        } else {
            $sql = "UPDATE notificaciones SET leido = 1
                    WHERE destinatario_tipo = 'checadores'
                    AND (
                        (rfc_empresa IS NULL AND rfc_checador IS NULL)
                        OR rfc_checador = ?
                        OR (rfc_empresa IS NOT NULL AND rfc_empresa = (
                            SELECT rfc_empresa FROM checadores WHERE rfc_checador = ? LIMIT 1
                        ))
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $rfc_checador, $rfc_checador);
        }

        if ($stmt->execute()) {
            sendResponse(200, ["success" => true, "message" => "Notificaciones actualizadas"]);
        } else {
            sendResponse(500, ["error" => "Error al actualizar la base de datos"]);
        }
        $stmt->close();
    }
    else {
        sendResponse(400, ["error" => "Acción no válida. Use: get_notifications, mark_as_read"]);
    }

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno del servidor: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
