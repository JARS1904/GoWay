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

$rfc_conductor = $_POST['rfc_conductor'] ?? '';
$rfc_empresa   = $_POST['rfc_empresa'] ?? '';
$nombre        = $_POST['nombre'] ?? '';
$licencia      = $_POST['licencia'] ?? '';
$telefono      = $_POST['telefono'] ?? '';
$activo        = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;

if (empty($rfc_conductor)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
    exit();
}

// Empezar a construir la consulta
$sql = "UPDATE conductores SET rfc_empresa = ?, nombre = ?, licencia = ?, telefono = ?, activo = ?";
$types = "ssssi";
$params = [$rfc_empresa, $nombre, $licencia, $telefono, $activo];

// Si el usuario subió una foto nueva, la subimos y actualizamos
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = uploadFoto($_FILES['foto'], 'conductor');
    if ($foto) {
        $sql .= ", foto = ?";
        $types .= "s";
        $params[] = $foto;
    }
}

$sql .= " WHERE rfc_conductor = ?";
$types .= "s";
$params[] = $rfc_conductor;

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
        'message' => 'Conductor actualizado correctamente',
        'registroActualizado' => [
            'rfc_conductor' => $rfc_conductor,
            'rfc_empresa' => $rfc_empresa,
            'nombre' => $nombre,
            'licencia' => $licencia,
            'telefono' => $telefono,
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
