<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Iniciar el manejo de errores
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Función para enviar respuestas JSON consistentes
function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goway";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        sendResponse(500, ["error" => "Connection failed: " . $conn->connect_error]);
    }

    // Verificar si se recibieron datos JSON
    $json = file_get_contents('php://input');
    if (empty($json)) {
        sendResponse(400, ["error" => "No se recibieron datos"]);
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(400, ["error" => "JSON inválido: " . json_last_error_msg()]);
    }

    if (empty($data['email']) || empty($data['password'])) {
        sendResponse(400, ["error" => "Email y contraseña son requeridos"]);
    }

    $email = $conn->real_escape_string($data['email']);
    $inputPassword = $data['password'];

    $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendResponse(404, ["error" => "Usuario no encontrado"]);
    }

    $user = $result->fetch_assoc();

    // Verificar la contraseña (compatible con hash y sin hash)
    $password_valid = password_verify($inputPassword, $user['password']) || $inputPassword === $user['password'];
    
    if (!$password_valid) {
        sendResponse(401, ["error" => "Contraseña incorrecta"]);
    }

    sendResponse(200, [
        "success" => true,
        "user" => [
            "id" => $user['id'],
            "name" => $user['nombre']
        ]
    ]);

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
}