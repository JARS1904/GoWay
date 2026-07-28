<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Manejar preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Iniciar el manejo de errores
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Función para enviar respuestas JSON consistentes
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

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
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

    // Generar token de sesión único
    $token = bin2hex(random_bytes(32));

    // Si el cliente (Flutter) envió un fcm_token, guardarlo en la tabla usuarios
    if (!empty($data['fcm_token'])) {
        $stmtFcm = $conn->prepare("UPDATE usuarios SET fcm_token = ? WHERE id = ?");
        if ($stmtFcm) {
            $stmtFcm->bind_param("si", $data['fcm_token'], $user['id']);
            $stmtFcm->execute();
            $stmtFcm->close();
        }
    }

    // Formatear foto_url
    $fotoUrl = null;
    if (!empty($user['foto_url'])) {
        $fotoUrl = $user['foto_url'];
    } elseif (!empty($user['foto'])) {
        if (strpos($user['foto'], 'http://') === 0 || strpos($user['foto'], 'https://') === 0 || strpos($user['foto'], '/') === 0 || strpos($user['foto'], 'assets/') === 0 || strpos($user['foto'], 'uploads/') === 0) {
            $fotoUrl = $user['foto'];
        } else {
            $fotoUrl = "assets/images/profiles/" . $user['foto'];
        }
    }

    // Mapear rol a tipo_usuario o tomar de BD si existe
    $tipoUsuario = null;
    if (isset($user['tipo_usuario']) && $user['tipo_usuario'] !== '') {
        $tipoUsuario = $user['tipo_usuario'];
    } elseif (isset($user['rol'])) {
        if ($user['rol'] == 1 || $user['rol'] === '1' || strcasecmp((string)$user['rol'], 'admin') === 0 || strcasecmp((string)$user['rol'], 'administrador') === 0) {
            $tipoUsuario = 'administrador';
        } elseif ($user['rol'] == 2 || $user['rol'] === '2' || strcasecmp((string)$user['rol'], 'usuario') === 0 || strcasecmp((string)$user['rol'], 'estandar') === 0) {
            $tipoUsuario = 'estandar';
        } else {
            $tipoUsuario = (string)$user['rol'];
        }
    }

    sendResponse(200, [
        "success" => true,
        "token" => $token,
        "user" => [
            "id" => isset($user['id']) ? (is_numeric($user['id']) ? intval($user['id']) : $user['id']) : null,
            "name" => $user['name'] ?? $user['nombre'] ?? null,
            "email" => $user['email'] ?? null,
            "foto_url" => $fotoUrl,
            "telefono" => $user['telefono'] ?? null,
            "fecha_registro" => $user['fecha_registro'] ?? $user['created_at'] ?? $user['fecha_creacion'] ?? null,
            "tipo_usuario" => $tipoUsuario
        ]
    ]);

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
}