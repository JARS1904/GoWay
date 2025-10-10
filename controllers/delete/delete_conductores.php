<?php
if (!isset($_POST['rfc_conductor'])) {
    die("RFC de conductor no proporcionado.");
}

$rfc = $_POST['rfc_conductor'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
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
    
    header("Location: /GoWay/pages/conductores.php");
    echo "Conductor eliminado correctamente.";
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    echo "Error al eliminar el conductor: " . $e->getMessage();
}

$stmt1->close();
$stmt2->close();
$conn->close();
?>