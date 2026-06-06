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
$telefono     = trim($_POST['tel_empresa'] ?? '');
$email        = trim($_POST['email_empresa'] ?? '');
$password_raw = $_POST['password'] ?? '';

// Validaciones básicas
if (empty($rfc_empresa) || empty($nombre) || empty($email) || empty($password_raw)) {
    echo json_encode(["success" => false, "message" => "Los campos RFC, nombre, email y contraseña son obligatorios."]);
    exit();
}

require_once '../../config/password_validation.php';
if (!validarContrasenaFuerte($password_raw)) {
    echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."]);
    exit();
}

// Verificar que el RFC o email no estén ya registrados
$stmt_check = $conn->prepare("SELECT rfc_empresa FROM empresas WHERE rfc_empresa = ? OR email = ?");
$stmt_check->bind_param("ss", $rfc_empresa, $email);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "El RFC o el correo ya están registrados."]);
    $stmt_check->close();
    exit();
}
$stmt_check->close();

// Encriptar contraseña
$password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

// Insertar
$stmt = $conn->prepare("INSERT INTO empresas (rfc_empresa, nombre, direccion, telefono, email, password) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $rfc_empresa, $nombre, $direccion, $telefono, $email, $password_hash);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Empresa registrada exitosamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al guardar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
