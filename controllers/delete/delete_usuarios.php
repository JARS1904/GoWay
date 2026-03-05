<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
require_once '../../config/conexion_bd.php';

if (!isset($_POST['id'])) {
    echo json_encode(["success" => false, "message" => "ID de usuario no proporcionado."]);
    exit();
}

$id = $_POST['id'];
$conn = $conexion;

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
} catch (\Throwable $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit();
}

$stmt->close();
$conn->close();
?>