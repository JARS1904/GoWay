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

    if (empty($data['email']) || empty($data['password'])) {
        sendResponse(400, ["error" => "Email/Usuario y contraseña son requeridos"]);
    }

    $email = $conn->real_escape_string($data['email']);
    $inputPassword = $data['password'];

    $userType = null;
    $userData = null;
    $hashedPassword = null;

    // Buscar como CHECADOR
    $stmt_checador = $conn->prepare("SELECT rfc_checador, nombre, usuario, contrasena, activo, foto FROM checadores WHERE usuario = ? AND activo = 1");
    $stmt_checador->bind_param("s", $email);
    $stmt_checador->execute();
    $result_checador = $stmt_checador->get_result();

    if ($result_checador->num_rows > 0) {
        $row_checador = $result_checador->fetch_assoc();
        $userType = "checador";
        
        // Cargar URL de la foto de la carpeta donde realmente existen
        $fotoUrl = null;
        if (!empty($row_checador['foto'])) {
            $fotoUrl = "assets/images/profiles/" . $row_checador['foto'];
        }
        
        $userData = [
            "id" => $row_checador['rfc_checador'],
            "name" => $row_checador['nombre'],
            "rol" => "checador",
            "foto_url" => $fotoUrl,
            "telefono" => '',
            "fecha_registro" => ''
        ];
        $hashedPassword = $row_checador['contrasena'];
    }
    $stmt_checador->close();

    if ($userType === null) {
        sendResponse(404, ["error" => "Checador no encontrado o inactivo"]);
    }

    // Verificar contraseña
    $password_valid = false;
    if (str_starts_with($hashedPassword, '$2y$')) {
        $password_valid = password_verify($inputPassword, $hashedPassword);
    } else {
        $password_valid = ($inputPassword === $hashedPassword);
    }
    
    if (!$password_valid) {
        sendResponse(401, ["error" => "Contraseña incorrecta"]);
    }

    $token = bin2hex(random_bytes(32));
    
    sendResponse(200, [
        "success" => true,
        "token" => $token,
        "user" => [
            "id" => $userData['id'],
            "name" => $userData['name'],
            "tipo_cuenta" => $userType,
            "rol" => $userData['rol'],
            "foto_url" => $userData['foto_url'],
            "telefono" => $userData['telefono'],
            "fecha_registro" => $userData['fecha_registro']
        ]
    ]);

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
}
?>