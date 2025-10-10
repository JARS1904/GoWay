<?php
if (!isset($_POST['id_vehiculo'])) {
    die("ID de vehículo no proporcionado.");
}

$id = $_POST['id_vehiculo'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
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

    header("Location: /GoWay/pages/vehiculos.php");
    echo "Vehículo eliminado correctamente.";
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    echo "Error al eliminar el vehículo: " . $e->getMessage();
}

$stmt1->close();
$stmt2->close();
$conn->close();
?>