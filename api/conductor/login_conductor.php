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
        sendResponse(500, ["error" => "Error interno en el servidor."]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(405, ["error" => "Método no permitido. Se espera POST."]);
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

    $rfc = trim($data['rfc']);

    // Buscar como CONDUCTOR usando sentencias preparadas
    $stmt_conductor = $conn->prepare("SELECT c.rfc_conductor, c.nombre, c.telefono, c.foto, c.activo, c.rfc_empresa, e.nombre AS empresa_nombre, e.telefono AS empresa_telefono, e.direccion AS empresa_direccion, e.email AS empresa_email FROM conductores c LEFT JOIN empresas e ON c.rfc_empresa = e.rfc_empresa WHERE c.rfc_conductor = ? AND c.activo = 1 LIMIT 1");
    if (!$stmt_conductor) {
        sendResponse(500, ["error" => "Error preparando consulta."]);
    }
    $stmt_conductor->bind_param("s", $rfc);
    $stmt_conductor->execute();
    $result_conductor = $stmt_conductor->get_result();

    if ($result_conductor->num_rows > 0) {
        $row_conductor = $result_conductor->fetch_assoc();
        
        $fotoUrl = null;
        if (!empty($row_conductor['foto'])) {
            $fotoUrl = "assets/images/profiles/" . $row_conductor['foto'];
        }
        
        $token = bin2hex(random_bytes(32));
        
        // Guardar token en BD
        guardarTokenSesion($conn, 'conductores', 'rfc_conductor', $row_conductor['rfc_conductor'], $token);
        
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
                "fecha_registro" => '',
                "rfc_empresa" => $row_conductor['rfc_empresa'],
                "empresa_nombre" => $row_conductor['empresa_nombre'],
                "empresa_telefono" => $row_conductor['empresa_telefono'],
                "empresa_direccion" => $row_conductor['empresa_direccion'],
                "empresa_email" => $row_conductor['empresa_email']
            ]
        ]);
    } else {
        sendResponse(404, ["error" => "Conductor no encontrado o inactivo"]);
    }

    $stmt_conductor->close();

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno de procesamiento."]);
}
?>
