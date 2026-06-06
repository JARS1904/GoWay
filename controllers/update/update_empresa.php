<?php
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$rfc_empresa  = trim($_POST['rfc_empresa'] ?? '');
$nombre       = trim($_POST['nombre_empresa'] ?? '');
$direccion    = trim($_POST['direccion_empresa'] ?? '');
$telefono     = trim($_POST['telefono'] ?? '');
$email        = trim($_POST['email_empresa'] ?? '');
$activo       = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;
$password_raw = $_POST['password'] ?? '';

// Validaciones básicas
if (empty($rfc_empresa) || empty($nombre) || empty($email)) {
    echo json_encode(["success" => false, "message" => "Los campos RFC, nombre y email son obligatorios."]);
    exit();
}

// Actualizar con o sin contraseña
if (!empty($password_raw)) {
    require_once '../../config/password_validation.php';
    if (!validarContrasenaFuerte($password_raw)) {
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."]);
        exit();
    }
    
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE empresas SET nombre = ?, direccion = ?, telefono = ?, email = ?, activo = ?, password = ? WHERE rfc_empresa = ?");
    $stmt->bind_param("ssssiss", $nombre, $direccion, $telefono, $email, $activo, $password_hash, $rfc_empresa);
} else {
    $stmt = $conn->prepare("UPDATE empresas SET nombre = ?, direccion = ?, telefono = ?, email = ?, activo = ? WHERE rfc_empresa = ?");
    $stmt->bind_param("ssssis", $nombre, $direccion, $telefono, $email, $activo, $rfc_empresa);
}

if ($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Empresa actualizada exitosamente.",
        "registroActualizado" => [
            "rfc_empresa" => $rfc_empresa,
            "nombre" => $nombre,
            "direccion" => $direccion,
            "telefono" => $telefono,
            "email" => $email,
            "activo" => $activo
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
