<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "goway");

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Obtener y validar datos del formulario
$id_horario = isset($_POST['id_horario']) ? (int)$_POST['id_horario'] : 0;
$id_ruta = isset($_POST['id_ruta']) ? (int)$_POST['id_ruta'] : 0;
$dia_semana = isset($_POST['dia_semana']) ? trim($_POST['dia_semana']) : '';
$hora_salida = isset($_POST['hora_salida']) ? trim($_POST['hora_salida']) : '';
$hora_llegada = isset($_POST['hora_llegada']) ? trim($_POST['hora_llegada']) : '';
$frecuencia = isset($_POST['frecuencia']) ? trim($_POST['frecuencia']) : '';

// Validar campos requeridos
if (empty($id_horario) || empty($id_ruta) || empty($dia_semana) || empty($hora_salida) || empty($hora_llegada)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos requeridos']);
    exit;
}

// Preparar y ejecutar la consulta de actualización
$sql = "UPDATE horarios SET id_ruta = ?, dia_semana = ?, hora_salida = ?, hora_llegada = ?, frecuencia = ? WHERE id_horario = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit;
}

$stmt->bind_param("issssi", $id_ruta, $dia_semana, $hora_salida, $hora_llegada, $frecuencia, $id_horario);

// Ejecutar consulta
if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Horario actualizado exitosamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
