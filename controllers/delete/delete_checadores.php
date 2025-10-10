<?php
if (!isset($_POST['rfc_checador'])) {
    die("RFC de checador no proporcionado.");
}

$rfc = $_POST['rfc_checador'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Iniciar transacción (aunque solo es una operación, es buena práctica)
$conn->begin_transaction();

try {
    // Eliminar el checador
    $sql = "DELETE FROM checadores WHERE rfc_checador = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rfc);
    $stmt->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    header("Location: /GoWay/pages/checadores.php?success=1");
    echo "Checador eliminado correctamente.";
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    header("Location: /GoWay/pages/checadores.php?error=" . urlencode($e->getMessage()));
    exit();
}

$stmt->close();
$conn->close();
?>