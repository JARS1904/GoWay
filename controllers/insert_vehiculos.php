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
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit;
}

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO vehiculos (placa, rfc_empresa, modelo, capacidad, activo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssii", $placa, $rfc_empresa, $modelo, $capacidad, $activo);

// Establecer parámetros y ejecutar
$placa = $_POST['placa'];
$rfc_empresa = $_POST['rfc_empresa'];
$modelo = $_POST['modelo'];
$capacidad = $_POST['capacidad'];
$activo = $_POST['activo'];

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Vehículo guardado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
