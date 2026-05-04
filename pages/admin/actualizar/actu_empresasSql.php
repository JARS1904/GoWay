<?php
header('Content-Type: application/json');
require_once '../../../config/conexion_bd.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$rfc_empresa = $_POST['rfc_empresa'];
$nombre      = $_POST['nombre_empresa'];
$direccion   = $_POST['direccion_empresa'];
$telefono    = $_POST['telefono'];
$email       = $_POST['email_empresa'];
$activo      = $_POST['activo'];
$password    = trim($_POST['password'] ?? '');

// Si se proporcionó una nueva contraseña, actualizarla también
if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql  = "UPDATE empresas SET nombre = ?, direccion = ?, telefono = ?, email = ?, activo = ?, password = ? WHERE rfc_empresa = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssssiss", $nombre, $direccion, $telefono, $email, $activo, $password_hash, $rfc_empresa);
} else {
    $sql  = "UPDATE empresas SET nombre = ?, direccion = ?, telefono = ?, email = ?, activo = ? WHERE rfc_empresa = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssssis", $nombre, $direccion, $telefono, $email, $activo, $rfc_empresa);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Empresa actualizada exitosamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
