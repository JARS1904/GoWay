<?php
header('Content-Type: application/json');

if (!isset($_POST['rfc_checador'])) {
    echo json_encode(["success" => false, "message" => "RFC de checador no proporcionado."]);
    exit();
}

$rfc = $_POST['rfc_checador'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Eliminar el checador
    $sql = "DELETE FROM checadores WHERE rfc_checador = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rfc);
    $stmt->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(["success" => true, "message" => "Checador eliminado correctamente"]);
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error al eliminar: " . $e->getMessage()]);
    exit();
}

$stmt->close();
$conn->close();
?>