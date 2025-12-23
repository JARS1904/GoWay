<?php
header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(["success" => false, "message" => "ID de usuario no proporcionado."]);
    exit();
}

$id = $_POST['id'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar el usuario: " . $stmt->error);
    }
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(["success" => true, "message" => "Usuario eliminado correctamente"]);
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