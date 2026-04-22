<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/conexion_bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $archivado = isset($_POST['archivado']) ? (int)$_POST['archivado'] : 1;

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de reporte inválido']);
        exit();
    }

    $stmt = $conexion->prepare("UPDATE reportes SET archivado = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $archivado, $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $statusText = $archivado ? 'archivado' : 'desarchivado';
                echo json_encode(['success' => true, 'message' => 'Reporte ' . $statusText . ' exitosamente']);
            } else {
                echo json_encode(['success' => true, 'message' => 'El reporte ya se encontraba en ese estado']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
$conexion->close();
?>
