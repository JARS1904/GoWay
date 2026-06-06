<?php
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';
require_once __DIR__ . '/upload_foto.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$nombre   = $_POST['nombre'];
$email    = $_POST['email'];
$password_raw = $_POST['password'];

require_once '../../config/password_validation.php';
if (!validarContrasenaFuerte($password_raw)) {
    echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."]);
    exit();
}

$password = password_hash($password_raw, PASSWORD_DEFAULT);
$rol      = $_POST['rol'];
$foto     = uploadFoto($_FILES['foto'] ?? [], 'usuario');

$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, foto) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nombre, $email, $password, $rol, $foto);

if ($stmt->execute()) {
    $id = $conn->insert_id;
    echo json_encode([
        "success" => true, 
        "message" => "Usuario agregado correctamente",
        "nuevoRegistro" => [
            "id" => $id,
            "nombre" => $nombre,
            "email" => $email,
            "rol" => $rol,
            "foto" => $foto
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Error al insertar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
