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

    if (empty($data['email']) || empty($data['password'])) {
        sendResponse(400, ["error" => "Email/Usuario y contraseña son requeridos"]);
    }

    $email = trim($data['email']);
    $inputPassword = $data['password'];

    $userType = null;
    $userData = null;
    $hashedPassword = null;

    // Buscar como CHECADOR usando sentencias preparadas
    $stmt_checador = $conn->prepare("SELECT rfc_checador, nombre, usuario, contrasena, activo, foto FROM checadores WHERE usuario = ? AND activo = 1 LIMIT 1");
    if (!$stmt_checador) {
        sendResponse(500, ["error" => "Error preparando consulta."]);
    }
    $stmt_checador->bind_param("s", $email);
    $stmt_checador->execute();
    $result_checador = $stmt_checador->get_result();

    if ($result_checador->num_rows > 0) {
        $row_checador = $result_checador->fetch_assoc();
        $userType = "checador";
        
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

    // Verificar contraseña con soporte de actualización automática si es en texto plano
    $password_valid = false;
    if (str_starts_with($hashedPassword, '$2y$')) {
        $password_valid = password_verify($inputPassword, $hashedPassword);
    } else {
        $password_valid = ($inputPassword === $hashedPassword);
        // Migración automática: si inició sesión con contraseña en texto plano, encriptarla de inmediato
        if ($password_valid) {
            $newHash = password_hash($inputPassword, PASSWORD_DEFAULT);
            $stmt_update_pwd = $conn->prepare("UPDATE checadores SET contrasena = ? WHERE rfc_checador = ?");
            if ($stmt_update_pwd) {
                $stmt_update_pwd->bind_param("ss", $newHash, $userData['id']);
                $stmt_update_pwd->execute();
                $stmt_update_pwd->close();
            }
        }
    }
    
    if (!$password_valid) {
        sendResponse(401, ["error" => "Contraseña incorrecta"]);
    }

    $token = bin2hex(random_bytes(32));
    
    // Guardar token en BD para verificar en llamadas subsiguientes (crear_asignacion_rapida, etc.)
    guardarTokenSesion($conn, 'checadores', 'rfc_checador', $userData['id'], $token);
    
    // Guardar fcm_token para notificaciones Push
    if (!empty($data['fcm_token'])) {
        $stmtFcm = $conn->prepare("UPDATE checadores SET fcm_token = ? WHERE rfc_checador = ?");
        if ($stmtFcm) {
            $stmtFcm->bind_param("ss", $data['fcm_token'], $userData['id']);
            $stmtFcm->execute();
            $stmtFcm->close();
        }
    }

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
    sendResponse(500, ["error" => "Error interno de procesamiento."]);
}
?>