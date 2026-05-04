<?php
session_start();
header('Content-Type: application/json');
require_once '../config/conexion_bd.php';

$titulo  = trim($_POST['titulo']  ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');
$tipo    = trim($_POST['tipo']    ?? 'General');

if (empty($titulo) || empty($mensaje)) {
    echo json_encode(['success' => false, 'message' => 'El título y el mensaje son obligatorios']);
    exit;
}

$conn = $conexion;

// Determinar si es Super Admin o Empresa
$is_empresa = isset($_SESSION['rol']) && $_SESSION['rol'] == 4 && !empty($_SESSION['rfc_empresa']);

if ($is_empresa) {
    // Empresa: siempre envía a sus suscriptores (id_usuario = NULL, rfc_empresa = su RFC)
    $id_usu     = null;
    $rfc_empresa = $_SESSION['rfc_empresa'];
} else {
    // Super Admin: puede enviar a un usuario específico o globalmente
    $id_usuario  = $_POST['id_usuario'] ?? 'todos';
    $id_usu      = ($id_usuario === 'todos' || empty($id_usuario)) ? null : (int)$id_usuario;
    $rfc_empresa = null; // global
}

$sql  = "INSERT INTO notificaciones (id_usuario, rfc_empresa, titulo, mensaje, tipo) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("issss", $id_usu, $rfc_empresa, $titulo, $mensaje, $tipo);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Notificación enviada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error de preparación: ' . $conn->error]);
}
$conn->close();
?>
