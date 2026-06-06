<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/conexion_bd.php';
require_once '../controllers/insert/upload_foto.php';

// Crear conexión
$conn = $conexion;

// Obtener método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Interceptar method override para PUT enviado desde Flutter
if ($method === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
    $method = 'PUT';
}

switch ($method) {
    case 'GET':
        // Obtener usuarios (excluyendo contraseñas por seguridad)
        $sql = "SELECT id, nombre, email, foto FROM usuarios";
        $result = $conn->query($sql);
        
        $usuarios = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Convertir filename a URL relativa accesible
                if (!empty($row['foto'])) {
                    $row['foto_url'] = 'assets/images/profiles/' . $row['foto'];
                } else {
                    $row['foto_url'] = null;
                }
                $usuarios[] = $row;
            }
        }
        echo json_encode($usuarios);
        break;
        
    case 'POST':
        // Soporta multipart/form-data (con foto) o JSON (sin foto)
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'multipart/form-data') !== false) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents("php://input"), true) ?? [];
        }

        // Validar datos requeridos
        if (empty($data['nombre']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Datos incompletos"]);
            break;
        }
        
        $nombre = $data['nombre'];
        $email  = $data['email'];

        // Validar fortaleza de la contraseña
        require_once '../config/password_validation.php';
        if (!validarContrasenaFuerte($data['password'])) {
            http_response_code(422);
            echo json_encode(["error" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."]);
            break;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Verificar si el email ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            http_response_code(409);
            echo json_encode(["error" => "El email ya está registrado"]);
            break;
        }
        $stmt->close();

        $foto = uploadFoto($_FILES['foto'] ?? [], 'usuario');

        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, foto) VALUES (?, ?, ?, 2, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $hashedPassword, $foto);
        
        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            http_response_code(201);
            echo json_encode(["message" => "Usuario creado correctamente", "id" => $new_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear usuario: " . $conn->error]);
        }
        
        $stmt->close();
        break;
        
    case 'PUT':
        // Soporta multipart/form-data (con foto) o JSON (sin foto)
        // Nota: PHP no puebla $_FILES en PUT; para subir foto en PUT usar POST con _method=PUT
        // o enviar como multipart con method override
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // Soporte para method override: POST con campo _method=PUT
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
            $data = $_POST;
            $files = $_FILES;
        } elseif (strpos($contentType, 'application/json') !== false) {
            $data = json_decode(file_get_contents("php://input"), true) ?? [];
            $files = [];
        } else {
            $data = json_decode(file_get_contents("php://input"), true) ?? [];
            $files = [];
        }

        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID de usuario no proporcionado"]);
            break;
        }
        
        $id = intval($data['id']);

        // Verificar si el usuario existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0) {
            $stmt->close();
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
            break;
        }
        $stmt->close();
        
        // Actualizar solo los campos proporcionados
        $params  = [];
        $types   = '';
        $setClauses = [];

        if (isset($data['nombre'])) {
            $setClauses[] = "nombre = ?";
            $types .= 's';
            $params[] = $data['nombre'];
        }
        if (isset($data['email'])) {
            $setClauses[] = "email = ?";
            $types .= 's';
            $params[] = $data['email'];
        }

        $nueva_foto = uploadFoto($files['foto'] ?? [], 'usuario');
        if ($nueva_foto !== null) {
            $setClauses[] = "foto = ?";
            $types .= 's';
            $params[] = $nueva_foto;
        }

        if (empty($setClauses)) {
            http_response_code(400);
            echo json_encode(["error" => "No hay datos para actualizar"]);
            break;
        }

        $types .= 'i';
        $params[] = $id;

        $sql = "UPDATE usuarios SET " . implode(", ", $setClauses) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Usuario actualizado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar usuario"]);
        }
        $stmt->close();
        break;
        
    case 'DELETE':
        // Eliminar usuario de forma segura
        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID de usuario no proporcionado"]);
            break;
        }
        
        $id = intval($_GET['id']);
        
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0) {
            $stmt->close();
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
            break;
        }
        $stmt->close();
        
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Usuario eliminado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar usuario"]);
        }
        
        $stmt->close();
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}

$conn->close();
?>