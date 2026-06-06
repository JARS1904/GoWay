<?php
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

if (!isset($_POST['rfc_conductor'])) {
    echo json_encode(["success" => false, "message" => "RFC de conductor no proporcionado."]);
    exit();
}

$rfc = $_POST['rfc_conductor'];

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

try {
    // Intentar eliminar el conductor
    $sql = "DELETE FROM conductores WHERE rfc_conductor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rfc);
    $stmt->execute();
    
    echo json_encode(["success" => true, "message" => "Conductor eliminado correctamente"]);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1451) {
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "message" => "No se puede eliminar este conductor porque tiene asignaciones activas. Por favor, elimine o reasigne dichas asignaciones primero."
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error de base de datos: " . $e->getMessage()]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error inesperado: " . $e->getMessage()]);
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>