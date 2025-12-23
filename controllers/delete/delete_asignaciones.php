<?php
header('Content-Type: application/json');

if (!isset($_POST['id_asignacion'])) {
    echo json_encode(["success" => false, "message" => "ID de asignación no proporcionado."]);
    exit();
}

$id = $_POST['id_asignacion'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Eliminar la asignación
    $sql = "DELETE FROM asignaciones WHERE id_asignacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar la asignación: " . $stmt->error);
    }
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(["success" => true, "message" => "Asignación eliminada correctamente"]);
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit();
}

$stmt->close();
$conn->close();
?>