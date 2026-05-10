<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../../config/conexion_bd.php';
require_once '../../config/opciones_reportes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$id_usuario = $_SESSION['id'] ?? 0;
$id_vehiculo = isset($_POST['vehiculo']) ? (int) $_POST['vehiculo'] : 0;
$rfc_conductor = isset($_POST['conductor']) ? $conexion->real_escape_string($_POST['conductor']) : '';
$id_ruta = isset($_POST['ruta']) ? (int) $_POST['ruta'] : 0;
$tipo_incidente = isset($_POST['tipoIncidente']) ? $conexion->real_escape_string($_POST['tipoIncidente']) : '';
$fecha_incidente = isset($_POST['fechaIncidente']) ? $conexion->real_escape_string($_POST['fechaIncidente']) : '';
$descripcion = isset($_POST['descripcion']) ? $conexion->real_escape_string($_POST['descripcion']) : '';
$gravedad = isset($_POST['gravedad']) ? $conexion->real_escape_string($_POST['gravedad']) : '';
$estado = isset($_POST['estado']) ? $conexion->real_escape_string($_POST['estado']) : '';

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

global $TIPOS_INCIDENCIA, $NIVELES_GRAVEDAD;
if (!array_key_exists($tipo_incidente, $TIPOS_INCIDENCIA)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de incidente inválido']);
    exit;
}

if (!array_key_exists($gravedad, $NIVELES_GRAVEDAD)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nivel de gravedad inválido']);
    exit;
}

try {
    // Verificar existencia
    $check_stmt = $conexion->prepare('SELECT id FROM reportes WHERE id = ?');
    if (!$check_stmt) {
        echo json_encode(['success' => false, 'message' => 'Error preparando consulta: ' . $conexion->error]);
        exit;
    }
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'El reporte no existe']);
        exit;
    }
    $check_stmt->close();

    // Actualizar
    $sql = 'UPDATE reportes SET id_vehiculo = ?, rfc_conductor = ?, id_ruta = ?, tipo_incidente = ?, fecha_incidente = ?, descripcion = ?, gravedad = ?, estado = ? WHERE id = ?';
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error preparando actualización: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param('isssssssi',
        $id_vehiculo,
        $rfc_conductor,
        $id_ruta,
        $tipo_incidente,
        $fecha_incidente,
        $descripcion,
        $gravedad,
        $estado,
        $id
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reporte actualizado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error]);
    }

    $stmt->close();
} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
}

$conexion->close();