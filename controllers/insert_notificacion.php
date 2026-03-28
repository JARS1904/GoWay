<?php
header('Content-Type: application/json');
require_once '../config/conexion_bd.php';

$titulo = $_POST['titulo'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';
$tipo = $_POST['tipo'] ?? 'General';
$id_usuario = $_POST['id_usuario'] ?? 'todos';

if (empty($titulo) || empty($mensaje)) {
    echo json_encode(['success' => false, 'message' => 'El título y el mensaje son obligatorios']);
    exit;
}

$conn = $conexion;
// Si viene 'todos' o '0', lo convertimos a NULL (notificación global)
$id_usu = ($id_usuario === 'todos' || empty($id_usuario)) ? null : (int)$id_usuario;

$sql = "INSERT INTO notificaciones (id_usuario, titulo, mensaje, tipo) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("isss", $id_usu, $titulo, $mensaje, $tipo);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Notificación enviada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la configuración: ' . $conn->error]);
}
$conn->close();
?>
