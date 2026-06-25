<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");
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
        sendResponse(500, ["error" => "Connection failed: " . $conn->connect_error]);
    }

    $json = file_get_contents('php://input');
    if (empty($json)) {
        sendResponse(400, ["error" => "No se recibieron datos"]);
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(400, ["error" => "JSON inválido: " . json_last_error_msg()]);
    }

    if (empty($data['rfc'])) {
        sendResponse(400, ["error" => "El RFC es requerido"]);
    }

    $rfc = $conn->real_escape_string($data['rfc']);

    // Buscar como CONDUCTOR
    $stmt_conductor = $conn->prepare("SELECT rfc_conductor, nombre, telefono, foto, activo FROM conductores WHERE rfc_conductor = ? AND activo = 1");
    $stmt_conductor->bind_param("s", $rfc);
    $stmt_conductor->execute();
    $result_conductor = $stmt_conductor->get_result();

    if ($result_conductor->num_rows > 0) {
        $row_conductor = $result_conductor->fetch_assoc();
        
        // Cargar URL de la foto
        $fotoUrl = null;
        if (!empty($row_conductor['foto'])) {
            $fotoUrl = "assets/images/profiles/" . $row_conductor['foto'];
        }
        
        $token = bin2hex(random_bytes(32));
        
        sendResponse(200, [
            "success" => true,
            "token" => $token,
            "user" => [
                "id" => $row_conductor['rfc_conductor'],
                "name" => $row_conductor['nombre'],
                "tipo_cuenta" => "conductor",
                "rol" => "conductor",
                "foto_url" => $fotoUrl,
                "telefono" => $row_conductor['telefono'],
                "fecha_registro" => ''
            ]
        ]);
    } else {
        sendResponse(404, ["error" => "Conductor no encontrado o inactivo"]);
    }

    $stmt_conductor->close();

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
}
?>
