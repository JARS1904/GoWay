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

require_once '../config/conexion_bd.php';

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

    $action     = $data['action']     ?? '';
    $id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : 0;

    if (!$action) {
        sendResponse(400, ["error" => "Acción no especificada"]);
    }

    // ── 1. Obtener notificaciones ────────────────────────────────────────
    if ($action === 'get_notifications') {
        if (!$id_usuario) {
            sendResponse(400, ["error" => "ID de usuario requerido"]);
        }

        // Un usuario recibe notificaciones si:
        // a) Es global del Super Admin (id_usuario IS NULL AND rfc_empresa IS NULL)
        // b) Está dirigida directamente a él (id_usuario = ?)
        // c) Viene de una empresa cuya ruta tiene en favoritos (rfc_empresa IN (...))
        $sql = "SELECT n.* FROM notificaciones n
                WHERE n.destinatario_tipo = 'usuarios'
                  AND (
                      (n.id_usuario IS NULL AND n.rfc_empresa IS NULL)
                      OR n.id_usuario = ?
                      OR (
                          n.rfc_empresa IS NOT NULL
                          AND n.rfc_empresa IN (
                              SELECT r.rfc_empresa
                              FROM rutas_favoritas rf
                              JOIN rutas r ON rf.id_ruta = r.id_ruta
                              WHERE rf.id_usuario = ?
                          )
                      )
                  )
                ORDER BY n.fecha_creacion DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_usuario, $id_usuario);
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
        if (!$id_usuario) {
            sendResponse(400, ["error" => "ID de usuario requerido"]);
        }

        $id_notificacion = isset($data['id_notificacion']) ? intval($data['id_notificacion']) : 0;

        if ($id_notificacion > 0) {
            // Marcar una específica (cualquiera que le corresponda al usuario)
            $sql = "UPDATE notificaciones SET leido = 1
                    WHERE id_notificacion = ?
                    AND destinatario_tipo = 'usuarios'
                    AND (
                        (id_usuario IS NULL AND rfc_empresa IS NULL)
                        OR id_usuario = ?
                        OR (rfc_empresa IS NOT NULL AND rfc_empresa IN (
                            SELECT r.rfc_empresa
                            FROM rutas_favoritas rf
                            JOIN rutas r ON rf.id_ruta = r.id_ruta
                            WHERE rf.id_usuario = ?
                        ))
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $id_notificacion, $id_usuario, $id_usuario);
        } else {
            // Marcar todas las que le corresponden
            $sql = "UPDATE notificaciones SET leido = 1
                    WHERE destinatario_tipo = 'usuarios'
                    AND (
                        (id_usuario IS NULL AND rfc_empresa IS NULL)
                        OR id_usuario = ?
                        OR (rfc_empresa IS NOT NULL AND rfc_empresa IN (
                            SELECT r.rfc_empresa
                            FROM rutas_favoritas rf
                            JOIN rutas r ON rf.id_ruta = r.id_ruta
                            WHERE rf.id_usuario = ?
                        ))
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_usuario, $id_usuario);
        }

        if ($stmt->execute()) {
            sendResponse(200, ["success" => true, "message" => "Notificaciones actualizadas"]);
        } else {
            sendResponse(500, ["error" => "Error al actualizar la base de datos"]);
        }
        $stmt->close();
    }

    // ── 3. Crear notificación (vía API — solo para Super Admin o uso interno) ─
    elseif ($action === 'create_notification') {
        $titulo  = $data['titulo']  ?? '';
        $mensaje = $data['mensaje'] ?? '';
        $tipo    = $data['tipo']    ?? 'general';

        $id_usu = ($id_usuario > 0) ? $id_usuario : null;

        if (empty($titulo) || empty($mensaje)) {
            sendResponse(400, ["error" => "El título y el mensaje son requeridos"]);
        }

        $sql  = "INSERT INTO notificaciones (id_usuario, rfc_empresa, titulo, mensaje, tipo) VALUES (?, NULL, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $id_usu, $titulo, $mensaje, $tipo);

        if ($stmt->execute()) {
            sendResponse(200, ["success" => true, "message" => "Notificación creada", "id_notificacion" => $stmt->insert_id]);
        } else {
            sendResponse(500, ["error" => "Error al crear la notificación"]);
        }
        $stmt->close();
    }

    else {
        sendResponse(400, ["error" => "Acción no válida. Use: get_notifications, mark_as_read, create_notification"]);
    }

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno del servidor: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
