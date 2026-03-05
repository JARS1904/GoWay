<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
require_once '../../config/conexion_bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || trim($_POST['id']) === '') {
    echo json_encode(['success' => false, 'message' => 'ID del reporte no especificado']);
    exit;
}

try {
    $id = (int) $_POST['id'];

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de reporte inválido']);
        exit;
    }

    // Verificar que el reporte exista
    $check_stmt = $conexion->prepare('SELECT id FROM reportes WHERE id = ?');
    if (!$check_stmt) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar consulta: ' . $conexion->error]);
        exit;
    }
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows === 0) {
        $check_stmt->close();
        echo json_encode(['success' => false, 'message' => 'El reporte no existe']);
        exit;
    }
    $check_stmt->close();

    // Eliminar reporte
    $stmt = $conexion->prepare('DELETE FROM reportes WHERE id = ?');
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar eliminación: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reporte eliminado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $stmt->error]);
    }
    $stmt->close();

} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
}

$conexion->close();
?>