
<?php
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
$nombre       = $_POST['nombre'];
$usuario      = $_POST['usuario'];
$contrasena   = password_hash($_POST['password'], PASSWORD_DEFAULT);
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
