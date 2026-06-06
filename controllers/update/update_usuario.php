<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';
require_once '../insert/upload_foto.php';

$conn = $conexion;

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit();
}

$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
$nombre     = $_POST['nombre'] ?? '';
$email      = $_POST['email'] ?? '';
$rol        = $_POST['rol'] ?? '';
$password_raw = $_POST['password'] ?? '';

if ($id_usuario <= 0 || empty($nombre) || empty($email) || empty($rol)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
    exit();
}

// Empezar a construir la consulta
$sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?";
$types = "ssi";
$params = [$nombre, $email, $rol];

// Si el usuario ingresó una contraseña nueva, la validamos y actualizamos
if (!empty($password_raw)) {
    require_once '../../config/password_validation.php';
    if (!validarContrasenaFuerte($password_raw)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."]);
        exit();
    }
    $contrasena = password_hash($password_raw, PASSWORD_DEFAULT);
    $sql .= ", password = ?";
    $types .= "s";
    $params[] = $contrasena;
}

// Si el usuario subió una foto nueva, la subimos y actualizamos
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = uploadFoto($_FILES['foto'], 'usuario');
    if ($foto) {
        $sql .= ", foto = ?";
        $types .= "s";
        $params[] = $foto;
        
        // Actualizar foto en la sesión si es el usuario actual
        if (isset($_SESSION['id']) && $_SESSION['id'] == $id_usuario) {
            $_SESSION['foto'] = $foto;
        }
    }
}

$sql .= " WHERE id = ?";
$types .= "i";
$params[] = $id_usuario;

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit();
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
