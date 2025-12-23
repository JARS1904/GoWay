<?php
header('Content-Type: application/json');

if (!isset($_POST['id_horario'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de horario no proporcionado']);
    exit;
}

$id = (int)$_POST['id_horario'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

$sql = "DELETE FROM horarios WHERE id_horario = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Horario eliminado exitosamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
