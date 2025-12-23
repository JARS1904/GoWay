
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
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Obtener parámetros
$rfc_conductor = $_POST['rfc_conductor'];
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre'];
$licencia = $_POST['licencia'];
$telefono = $_POST['telefono'];

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO conductores (rfc_conductor, rfc_empresa, nombre, licencia, telefono) VALUES (?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
    exit();
}

$stmt->bind_param("sssss", $rfc_conductor, $rfc_empresa, $nombre, $licencia, $telefono);

// Ejecutar
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Conductor agregado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al insertar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
