
<?php
header('Content-Type: application/json');
require_once '../../../config/conexion_bd.php';
require_once '../../../controllers/upload_foto.php';

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
$activo        = $_POST['activo'];

$nueva_foto = uploadFoto($_FILES['foto'] ?? [], 'conductor');

if ($nueva_foto !== null) {
    $stmt = $conn->prepare("UPDATE conductores SET rfc_empresa=?, nombre=?, licencia=?, telefono=?, activo=?, foto=? WHERE rfc_conductor=?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssssisss", $rfc_empresa, $nombre, $licencia, $telefono, $activo, $nueva_foto, $rfc_conductor);
} else {
    $stmt = $conn->prepare("UPDATE conductores SET rfc_empresa=?, nombre=?, licencia=?, telefono=?, activo=? WHERE rfc_conductor=?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssssis", $rfc_empresa, $nombre, $licencia, $telefono, $activo, $rfc_conductor);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Conductor actualizado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

