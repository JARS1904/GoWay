<?php
header('Content-Type: application/json');

if (!isset($_POST['id_vehiculo'])) {
    echo json_encode(['success' => false, 'message' => 'ID de vehículo no proporcionado.']);
    exit;
}

$id = $_POST['id_vehiculo'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Eliminar asignaciones relacionadas
    $sql1 = "DELETE FROM asignaciones WHERE id_vehiculo = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    
    // 2. Eliminar el vehículo
    $sql2 = "DELETE FROM vehiculos WHERE id_vehiculo = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    
    // Confirmar transacción si todo salió bien
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Vehículo eliminado exitosamente']);
    exit;
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el vehículo: ' . $e->getMessage()]);
    exit;
}

$stmt1->close();
$stmt2->close();
$conn->close();
?>