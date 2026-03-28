<?php
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

if (!isset($_POST['id_horario'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de horario no proporcionado']);
    exit;
}

$id = (int)$_POST['id_horario'];

$conn = $conexion;

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

$sql = "DELETE FROM horarios WHERE id_horario = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);

try {
    if ($stmt->execute()) {
        // Insertar notificación
        $titulo_notif = "Horario Eliminado";
        $mensaje_notif = "El administrador ha eliminado un horario. Por favor revisa las actualizaciones.";
        $tipo_notif = "horario";
        $sql_notif = "INSERT INTO notificaciones (id_usuario, titulo, mensaje, tipo) VALUES (NULL, ?, ?, ?)";
        if ($stmt_notif = $conn->prepare($sql_notif)) {
            $stmt_notif->bind_param("sss", $titulo_notif, $mensaje_notif, $tipo_notif);
            $stmt_notif->execute();
            $stmt_notif->close();
        }

        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Horario eliminado exitosamente']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $stmt->error]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Operación fallida. Revisa si la tabla notificaciones ya existe o si el horario está ocupado. Error: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
