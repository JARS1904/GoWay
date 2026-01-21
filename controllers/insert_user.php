
<?php
header('Content-Type: application/json');
require_once '../config/conexion_bd.php';

// Crear conexión
$conn = $conexion;

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $email, $password, $rol);

// Establecer parámetros y ejecutar
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$rol = $_POST['rol'];

$stmt->execute();

echo json_encode(["success" => true, "message" => "Usuario agregado correctamente"]);

$stmt->close();
$conn->close();
?>
