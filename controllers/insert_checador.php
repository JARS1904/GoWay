
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
$rfc_checador = $_POST['rfc_checador'];
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['password'];

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO checadores (rfc_checador, rfc_empresa, nombre, usuario, contrasena) VALUES (?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
    exit();
}

$stmt->bind_param("sssss", $rfc_checador, $rfc_empresa, $nombre, $usuario, $contrasena);

// Ejecutar
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Checador agregado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al insertar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
