<?php
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

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

$destinatario_tipo = 'usuarios';
$id_usu = null;
$rfc_empresa = null;

if ($is_empresa) {
    $rfc_empresa = $_SESSION['rfc_empresa'];
    $destinatario_empresa = $_POST['destinatario_empresa'] ?? 'favoritos';
    if ($destinatario_empresa === 'checadores') {
        $destinatario_tipo = 'checadores';
    }
} else {
    // Super Admin: puede enviar a un usuario específico o globalmente a usuarios/checadores
    $id_usuario  = $_POST['id_usuario'] ?? 'todos';
    
    if ($id_usuario === 'todos_checadores') {
        $destinatario_tipo = 'checadores';
    } else {
        $id_usu = ($id_usuario === 'todos' || empty($id_usuario)) ? null : (int)$id_usuario;
    }
}

$sql  = "INSERT INTO notificaciones (id_usuario, rfc_empresa, titulo, mensaje, tipo, destinatario_tipo) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("isssss", $id_usu, $rfc_empresa, $titulo, $mensaje, $tipo, $destinatario_tipo);
    if ($stmt->execute()) {
        $id_insertado = $stmt->insert_id;
        
        $sql_nuevo = "SELECT n.*, u.nombre AS usuario_nombre 
                      FROM notificaciones n 
                      LEFT JOIN usuarios u ON n.id_usuario = u.id 
                      WHERE n.id_notificacion = ?";
        $stmt_nuevo = $conn->prepare($sql_nuevo);
        $stmt_nuevo->bind_param("i", $id_insertado);
        $stmt_nuevo->execute();
        $result_nuevo = $stmt_nuevo->get_result();
        $nuevoRegistro = $result_nuevo->fetch_assoc();
        $stmt_nuevo->close();

        echo json_encode(['success' => true, 'message' => 'Notificación enviada correctamente', 'nuevoRegistro' => $nuevoRegistro]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error de preparación: ' . $conn->error]);
}
$conn->close();
?>
