
<?php
header('Content-Type: application/json');
require_once '../config/conexion_bd.php';
require_once __DIR__ . '/upload_foto.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$nombre   = $_POST['nombre'];
$email    = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$rol      = $_POST['rol'];
$foto     = uploadFoto($_FILES['foto'] ?? [], 'usuario');

$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, foto) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nombre, $email, $password, $rol, $foto);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Usuario agregado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al insertar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
