
<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goway";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Obtener y validar datos
$id_ruta = isset($_POST['id_ruta']) ? (int)$_POST['id_ruta'] : 0;
$dia_semana = isset($_POST['dia_semana']) ? trim($_POST['dia_semana']) : '';
$hora_salida = isset($_POST['hora_salida']) ? trim($_POST['hora_salida']) : '';
$hora_llegada = isset($_POST['hora_llegada']) ? trim($_POST['hora_llegada']) : '';
$frecuencia = isset($_POST['frecuencia']) ? trim($_POST['frecuencia']) : '';

// Validar campos requeridos
if (empty($id_ruta) || empty($dia_semana) || empty($hora_salida) || empty($hora_llegada)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos requeridos']);
    exit;
}

// Preparar y ejecutar consulta
$stmt = $conn->prepare("INSERT INTO horarios (id_ruta, dia_semana, hora_salida, hora_llegada, frecuencia) VALUES (?, ?, ?, ?, ?)");

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit;
}

$stmt->bind_param("issss", $id_ruta, $dia_semana, $hora_salida, $hora_llegada, $frecuencia);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Horario agregado exitosamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al insertar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
