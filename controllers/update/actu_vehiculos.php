
<?php
header('Content-Type: application/json');

// actualizar_vehiculo.php

// Configuración de la base de datos
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

// Recoger datos del formulario
$id_vehiculo = $_POST['id_vehiculo'];
$placa = $_POST['placa'];
$modelo = $_POST['modelo'];
$capacidad = $_POST['capacidad'];
$activo = $_POST['activo'];
$rfc_empresa = $_POST['rfc_empresa'];

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
    echo json_encode(['success' => true, 'message' => 'Vehículo actualizado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>

