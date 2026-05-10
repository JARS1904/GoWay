<?php
session_start();
header('Content-Type: application/json');
require_once '../config/conexion_bd.php';
require_once __DIR__ . '/upload_foto.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$rfc_checador = $_POST['rfc_checador'];
$rfc_empresa  = $_POST['rfc_empresa'];
if (isset($_SESSION['rol']) && $_SESSION['rol'] == 4) {
    $rfc_empresa = $_SESSION['rfc_empresa'];
}
$nombre       = $_POST['nombre'];
$usuario      = $_POST['usuario'];
$password_raw = $_POST['password'];

require_once '../config/password_validation.php';
if (!validarContrasenaFuerte($password_raw)) {
    echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."]);
    exit();
}

$contrasena   = password_hash($password_raw, PASSWORD_DEFAULT);
$foto         = uploadFoto($_FILES['foto'] ?? [], 'checador');

$stmt = $conn->prepare("INSERT INTO checadores (rfc_checador, rfc_empresa, nombre, usuario, contrasena, foto) VALUES (?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
    exit();
}

$stmt->bind_param("ssssss", $rfc_checador, $rfc_empresa, $nombre, $usuario, $contrasena, $foto);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Checador agregado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al insertar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
