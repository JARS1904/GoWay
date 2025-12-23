<?php
header('Content-Type: application/json');

if (!isset($_POST['rfc_conductor'])) {
    echo json_encode(["success" => false, "message" => "RFC de conductor no proporcionado."]);
    exit();
}

$rfc = $_POST['rfc_conductor'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Eliminar asignaciones relacionadas
    $sql1 = "DELETE FROM asignaciones WHERE rfc_conductor = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $rfc);
    $stmt1->execute();
    
    // 2. Eliminar el conductor
    $sql2 = "DELETE FROM conductores WHERE rfc_conductor = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $rfc);
    $stmt2->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(["success" => true, "message" => "Conductor eliminado correctamente"]);
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error al eliminar: " . $e->getMessage()]);
    exit();
}

$stmt1->close();
$stmt2->close();
$conn->close();
?>
}

$stmt1->close();
$stmt2->close();
$conn->close();
?>