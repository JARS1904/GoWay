<?php
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

$conn = $conexion;
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

$id_horario   = isset($_POST['id_horario'])   ? (int)trim($_POST['id_horario']) : 0;
$id_ruta      = isset($_POST['id_ruta'])      ? (int)trim($_POST['id_ruta']) : 0;
$tipo_dia     = isset($_POST['tipo_dia'])     ? trim($_POST['tipo_dia']) : '';
$hora_salida  = isset($_POST['hora_salida'])  ? trim($_POST['hora_salida']) : '';
$hora_llegada = isset($_POST['hora_llegada']) ? trim($_POST['hora_llegada']) : '';
$frecuencia   = isset($_POST['frecuencia'])   ? trim($_POST['frecuencia']) : '';

if ($id_horario <= 0 || $id_ruta <= 0 || $tipo_dia === '' || $hora_salida === '' || $hora_llegada === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE horarios
     SET    id_ruta = ?, tipo_dia = ?, hora_salida = ?, hora_llegada = ?, frecuencia = ?
     WHERE  id_horario = ?"
);
$stmt->bind_param("issssi", $id_ruta, $tipo_dia, $hora_salida, $hora_llegada, $frecuencia, $id_horario);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Horario actualizado exitosamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
