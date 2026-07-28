<?php
/**
 * api_middleware.php — Middleware centralizado de Seguridad, CORS y Autenticación de Tokens para APIs
 */

// 1. Manejo Seguro de CORS
function aplicarCorsGoWay() {
    // Si se desea limitar a un dominio en producción, configurar en .env (ej. APP_URL=https://tudominio.com)
    $origen_permitido = getenv('APP_URL') !== false ? getenv('APP_URL') : ($_ENV['APP_URL'] ?? '*');
    
    // Si la petición viene con un Origin específico, se puede reflejar en vez de asterisco ciego si es seguro
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    } else {
        header("Access-Control-Allow-Origin: " . $origen_permitido);
    }
    
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Token");
    header("Access-Control-Allow-Credentials: true");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// 2. Helper para guardar tokens en la base de datos (Checadores o Conductores)
function guardarTokenSesion($conn, $tabla, $columna_id, $id_valor, $token) {
    // Verificar que la tabla tenga la columna api_token
    $check = $conn->query("SHOW COLUMNS FROM `$tabla` LIKE 'api_token'");
    if ($check && $check->num_rows === 0) {
        $conn->query("ALTER TABLE `$tabla` ADD COLUMN api_token VARCHAR(128) NULL DEFAULT NULL");
    }
    
    $stmt = $conn->prepare("UPDATE `$tabla` SET api_token = ? WHERE `$columna_id` = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $token, $id_valor);
        $stmt->execute();
        $stmt->close();
    }
}

// 3. Helper para validar peticiones autenticadas desde móvil/API
function verificarApiToken($conn, $tabla = null, $columna_id = null) {
    $token = null;
    $headers = getallheaders();
    
    // Buscar en cabecera Authorization: Bearer <token>
    if (isset($headers['Authorization']) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        $token = $matches[1];
    } elseif (isset($headers['authorization']) && preg_match('/Bearer\s(\S+)/', $headers['authorization'], $matches)) {
        $token = $matches[1];
    } elseif (isset($_GET['token'])) {
        $token = trim($_GET['token']);
    } elseif (isset($_POST['token'])) {
        $token = trim($_POST['token']);
    }

    if (empty($token)) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "No autorizado. Token de autenticación requerido."], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($tabla && $columna_id) {
        $stmt = $conn->prepare("SELECT `$columna_id` FROM `$tabla` WHERE api_token = ? AND activo = 1 LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) {
                $stmt->close();
                http_response_code(401);
                echo json_encode(["success" => false, "error" => "Token inválido o expirado."], JSON_UNESCAPED_UNICODE);
                exit();
            }
            $row = $res->fetch_assoc();
            $stmt->close();
            return $row[$columna_id];
        }
    }
    return $token;
}
?>
