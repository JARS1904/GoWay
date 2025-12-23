<?php
header('Content-Type: application/json');

if (!isset($_POST['id_ruta'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de ruta no proporcionado']);
    exit;
}

$id = (int)$_POST['id_ruta'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Eliminar horarios relacionados
    $sql1 = "DELETE FROM horarios WHERE id_ruta = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    
    // 2. Eliminar asignaciones relacionadas
    $sql2 = "DELETE FROM asignaciones WHERE id_ruta = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    
    // 3. Eliminar la ruta
    $sql3 = "DELETE FROM rutas WHERE id_ruta = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Ruta eliminada exitosamente']);
    exit;
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al eliminar la ruta: ' . $e->getMessage()]);
    exit;
}

$stmt1->close();
$stmt2->close();
$stmt3->close();
$conn->close();
?>$conn->close();
?>