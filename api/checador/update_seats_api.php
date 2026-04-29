<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Iniciar el manejo de errores
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Soporte para FormData tradicional además de JSON
        if (empty($data)) {
            $data = $_POST;
        }

        if (empty($data['id_asignacion'])) {
            sendResponse(400, ["error" => "El parámetro 'id_asignacion' es requerido"]);
        }

        if (!isset($data['asientos_disponibles'])) {
            sendResponse(400, ["error" => "El parámetro 'asientos_disponibles' es requerido"]);
        }

        $id_asignacion = (int)$data['id_asignacion'];
        $asientos_disponibles = (int)$data['asientos_disponibles'];

        if ($asientos_disponibles < 0) {
            sendResponse(400, ["error" => "La cantidad de asientos disponibles no puede ser negativa"]);
        }

        // Actualizar los asientos disponibles en la tabla asignaciones
        $sql = "UPDATE asignaciones SET asientos_disp = ? WHERE id_asignacion = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            sendResponse(500, ["error" => "Error al preparar la consulta: " . $conn->error]);
        }
        
        $stmt->bind_param("ii", $asientos_disponibles, $id_asignacion);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                sendResponse(200, [
                    "success" => true,
                    "message" => "Asientos disponibles actualizados correctamente",
                    "asientos_disponibles" => $asientos_disponibles
                ]);
            } else {
                // Verificar si la asignación existe pero ya tenía ese número de asientos
                $check_sql = "SELECT id_asignacion FROM asignaciones WHERE id_asignacion = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("i", $id_asignacion);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    sendResponse(200, [
                        "success" => true,
                        "message" => "Los asientos ya estaban actualizados a esta cantidad",
                        "asientos_disponibles" => $asientos_disponibles
                    ]);
                } else {
                    sendResponse(404, ["error" => "No se encontró ninguna asignación con el ID proporcionado"]);
                }
            }
        } else {
            sendResponse(500, ["error" => "Error al ejecutar la actualización: " . $stmt->error]);
        }
        
        $stmt->close();
    } else {
        sendResponse(405, ["error" => "Método no permitido. Use POST"]);
    }

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
