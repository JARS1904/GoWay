<?php
if (!isset($_POST['id'])) {
    die("ID de usuario no proporcionado.");
}

$id = $_POST['id'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Eliminar la asignación
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar el usuario: " . $stmt->error);
    }
    
    // Confirmar transacción
    $conn->commit();
    
    header("Location: /GoWay/pages/usuarios.php?success=1");
    echo" Usuario eliminado correctamente.";
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    header("Location: /GoWay/pages/usuarios.php?error=" . urlencode($e->getMessage()));
    exit();
}

$stmt->close();
$conn->close();
?>