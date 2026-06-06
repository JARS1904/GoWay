<?php
session_start();
header('Content-Type: application/json');

// actualizar_vehiculo.php
require_once '../../config/conexion_bd.php';

// Crear conexión
$conn = $conexion;

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit;
}

// Recoger datos del formulario
$id_vehiculo = $_POST['id_vehiculo'] ?? '';
$placa = $_POST['placa'] ?? '';
$modelo = $_POST['modelo'] ?? '';
$capacidad = isset($_POST['capacidad']) ? (int)$_POST['capacidad'] : 0;
$activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;
$rfc_empresa = $_POST['rfc_empresa'] ?? '';

if (isset($_SESSION['rol']) && $_SESSION['rol'] == 4) {
    $rfc_empresa = $_SESSION['rfc_empresa'] ?? '';
}

if (empty($id_vehiculo)) {
    echo json_encode(['success' => false, 'message' => 'ID de vehículo no proporcionado.']);
    exit;
}

// Preparar la consulta SQL
$sql = "UPDATE vehiculos SET
placa = ?,
modelo = ?,
capacidad = ?,
activo = ?,
rfc_empresa = ?
WHERE id_vehiculo = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit;
}

// Vincular parámetros
$stmt->bind_param("ssiisi", $placa, $modelo, $capacidad, $activo, $rfc_empresa, $id_vehiculo);

// Ejecutar consulta
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Vehículo actualizado exitosamente',
        'registroActualizado' => [
            'id_vehiculo' => $id_vehiculo,
            'placa' => $placa,
            'modelo' => $modelo,
            'capacidad' => $capacidad,
            'rfc_empresa' => $rfc_empresa,
            'activo' => $activo
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>
