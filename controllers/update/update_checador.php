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

$rfc_checador = $_POST['rfc_checador'] ?? '';
$rfc_empresa  = $_POST['rfc_empresa'] ?? '';
$nombre       = $_POST['nombre'] ?? '';
$usuario      = $_POST['usuario'] ?? '';
$password_raw = $_POST['password'] ?? '';
$activo       = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;

if (empty($rfc_checador)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
    exit();
}

// Empezar a construir la consulta
$sql = "UPDATE checadores SET rfc_empresa = ?, nombre = ?, usuario = ?, activo = ?";
$types = "sssi";
$params = [$rfc_empresa, $nombre, $usuario, $activo];

// Si el usuario ingresó una contraseña nueva, la validamos y actualizamos
if (!empty($password_raw)) {
    require_once '../../config/password_validation.php';
    if (!validarContrasenaFuerte($password_raw)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."]);
        exit();
    }
    $contrasena = password_hash($password_raw, PASSWORD_DEFAULT);
    $sql .= ", contrasena = ?";
    $types .= "s";
    $params[] = $contrasena;
}

// Si el usuario subió una foto nueva, la subimos y actualizamos
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = uploadFoto($_FILES['foto'], 'checador');
    if ($foto) {
        $sql .= ", foto = ?";
        $types .= "s";
        $params[] = $foto;
    }
}

$sql .= " WHERE rfc_checador = ?";
$types .= "s";
$params[] = $rfc_checador;

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit();
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $response = [
        'success' => true, 
        'message' => 'Checador actualizado correctamente',
        'registroActualizado' => [
            'rfc_checador' => $rfc_checador,
            'rfc_empresa' => $rfc_empresa,
            'nombre' => $nombre,
            'usuario' => $usuario,
            'activo' => $activo
        ]
    ];
    if (isset($foto)) {
        $response['registroActualizado']['foto'] = $foto;
    }
    echo json_encode($response);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
