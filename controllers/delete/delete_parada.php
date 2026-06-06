<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

$conn = $conexion;
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

$id_parada = isset($_POST['id_parada']) ? (int)trim($_POST['id_parada']) : 0;

if ($id_parada <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'id_parada requerido']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM paradas_ruta WHERE id_parada = ?");
    $stmt->bind_param("i", $id_parada);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Parada eliminada exitosamente']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
