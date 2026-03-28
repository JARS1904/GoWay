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

    // Capturar datos según el método
    if ($method === 'GET') {
        $data = $_GET;
    } elseif ($method === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            $data = $_POST;
        }
        if (empty($data)) {
            $data = $_GET; // fallback
        }
    }

    $action = isset($data['action']) ? $data['action'] : '';
    $id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : 0;

    if (!$action) {
        sendResponse(400, ["error" => "Acción no especificada"]);
    }

    // 1. Obtener notificaciones
    if ($action === 'get_notifications') {
        if (!$id_usuario) {
            sendResponse(400, ["error" => "ID de usuario requerido"]);
        }

        // Obtener notificaciones del usuario (y las globales donde id_usuario IS NULL)
        $sql = "SELECT * FROM notificaciones 
                WHERE id_usuario = ? OR id_usuario IS NULL 
                ORDER BY fecha_creacion DESC";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notificaciones = [];
        $unread_count = 0;
        
        while ($row = $result->fetch_assoc()) {
            $notificaciones[] = $row;
            if ($row['leido'] == 0) {
                $unread_count++;
            }
        }
        
        sendResponse(200, [
            "success" => true, 
            "notificaciones" => $notificaciones,
            "unread_count" => $unread_count
        ]);
        $stmt->close();
    }
    
    // 2. Marcar como leída
    else if ($action === 'mark_as_read') {
        if (!$id_usuario) {
            sendResponse(400, ["error" => "ID de usuario requerido"]);
        }
        
        $id_notificacion = isset($data['id_notificacion']) ? intval($data['id_notificacion']) : 0;
        
        if ($id_notificacion > 0) {
            // Marcar una específica
            $sql = "UPDATE notificaciones SET leido = 1 WHERE id_notificacion = ? AND (id_usuario = ? OR id_usuario IS NULL)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_notificacion, $id_usuario);
        } else {
            // Marcar todas
            $sql = "UPDATE notificaciones SET leido = 1 WHERE id_usuario = ? OR id_usuario IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_usuario);
        }
        
        if ($stmt->execute()) {
            sendResponse(200, ["success" => true, "message" => "Notificaciones actualizadas"]);
        } else {
            sendResponse(500, ["error" => "Error al actualizar la base de datos"]);
        }
        $stmt->close();
    }
    
    // 3. Crear notificación (internamente o vía POST)
    else if ($action === 'create_notification') {
        $titulo = isset($data['titulo']) ? $data['titulo'] : '';
        $mensaje = isset($data['mensaje']) ? $data['mensaje'] : '';
        $tipo = isset($data['tipo']) ? $data['tipo'] : 'general';
        
        $id_usu = ($id_usuario > 0) ? $id_usuario : null;

        if (empty($titulo) || empty($mensaje)) {
            sendResponse(400, ["error" => "El título y el mensaje son requeridos"]);
        }

        $sql = "INSERT INTO notificaciones (id_usuario, titulo, mensaje, tipo) VALUES (?, ?, ?, ?)";
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
