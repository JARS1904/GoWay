<?php
if (!isset($_POST['id_asignacion'])) {
    die("ID de asignación no proporcionado.");
}

$id = $_POST['id_asignacion'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
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
    
    header("Location: /GoWay/pages/paradas.php?success=1");
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    header("Location: /GoWay/pages/paradas.php?error=" . urlencode($e->getMessage()));
    exit();
}

$stmt->close();
$conn->close();
?>