<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goway";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Obtener método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obtener usuarios (excluyendo contraseñas por seguridad)
        $sql = "SELECT id, nombre, email FROM usuarios";
        $result = $conn->query($sql);
        
        $usuarios = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        echo json_encode($usuarios);
        break;
        
    case 'POST':
        // Añadir nuevo usuario con protección contra SQL injection
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validar datos requeridos
        if (empty($data['nombre']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Datos incompletos"]);
            break;
        }
        
        $nombre = $conn->real_escape_string($data['nombre']);
        $email = $conn->real_escape_string($data['email']);
        // Hashear la contraseña
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Verificar si el email ya existe
        $checkEmail = $conn->query("SELECT id FROM usuarios WHERE email = '$email'");
        if ($checkEmail->num_rows > 0) {
            http_response_code(409);
            echo json_encode(["error" => "El email ya está registrado"]);
            break;
        }
        
        // Usar consulta preparada para mayor seguridad
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 2)");
        $stmt->bind_param("sss", $nombre, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Usuario creado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear usuario: " . $conn->error]);
        }
        
        $stmt->close();
        break;
        
    case 'PUT':
        // Actualizar usuario
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID de usuario no proporcionado"]);
            break;
        }
        
        $id = intval($data['id']);
        $nombre = $conn->real_escape_string($data['nombre']);
        $email = $conn->real_escape_string($data['email']);
        
        // Verificar si el usuario existe
        $checkUser = $conn->query("SELECT id FROM usuarios WHERE id = $id");
        if ($checkUser->num_rows == 0) {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
            break;
        }
        
        // Actualizar solo los campos proporcionados
        $updates = [];
        if (isset($data['nombre'])) $updates[] = "nombre = '$nombre'";
        if (isset($data['email'])) $updates[] = "email = '$email'";
        
        if (!empty($updates)) {
            $sql = "UPDATE usuarios SET " . implode(", ", $updates) . " WHERE id = $id";
            
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Usuario actualizado correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar usuario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "No hay datos para actualizar"]);
        }
        break;
        
    case 'DELETE':
        // Eliminar usuario de forma segura
        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID de usuario no proporcionado"]);
            break;
        }
        
        $id = intval($_GET['id']);
        
        // Verificar si el usuario existe
        $checkUser = $conn->query("SELECT id FROM usuarios WHERE id = $id");
        if ($checkUser->num_rows == 0) {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
            break;
        }
        
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