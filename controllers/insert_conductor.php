
<?php
header('Content-Type: application/json');
require_once '../config/conexion_bd.php';
require_once __DIR__ . '/upload_foto.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$rfc_conductor = $_POST['rfc_conductor'];
$rfc_empresa   = $_POST['rfc_empresa'];
$nombre        = $_POST['nombre'];
$licencia      = $_POST['licencia'];
$telefono      = $_POST['telefono'];
$foto          = uploadFoto($_FILES['foto'] ?? [], 'conductor');

$stmt = $conn->prepare("INSERT INTO conductores (rfc_conductor, rfc_empresa, nombre, licencia, telefono, foto) VALUES (?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
    exit();
}

$stmt->bind_param("ssssss", $rfc_conductor, $rfc_empresa, $nombre, $licencia, $telefono, $foto);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Conductor agregado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al insertar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
