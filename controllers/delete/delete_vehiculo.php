<?php
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

if (!isset($_POST['id_vehiculo'])) {
    echo json_encode(['success' => false, 'message' => 'ID de vehículo no proporcionado.']);
    exit;
}

$id = $_POST['id_vehiculo'];

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

try {
    // Intentar eliminar el vehículo
    $sql = "DELETE FROM vehiculos WHERE id_vehiculo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Insertar notificación de sistema
    $titulo_notif = "Unidad dada de baja";
    $mensaje_notif = "El administrador ha dado de baja una unidad. Pueden existir cambios en las rutas.";
    $tipo_notif = "unidad";
    $sql_notif = "INSERT INTO notificaciones (id_usuario, titulo, mensaje, tipo) VALUES (NULL, ?, ?, ?)";
    if ($stmt_notif = $conn->prepare($sql_notif)) {
        $stmt_notif->bind_param("sss", $titulo_notif, $mensaje_notif, $tipo_notif);
        $stmt_notif->execute();
        $stmt_notif->close();
    }

    echo json_encode(['success' => true, 'message' => 'Vehículo eliminado exitosamente']);
    exit;
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1451) {
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "message" => "No se puede eliminar esta unidad porque tiene asignaciones activas. Por favor, elimine o reasigne dichas asignaciones primero."
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error de base de datos: " . $e->getMessage()]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
    exit;
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>