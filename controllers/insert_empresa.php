
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

// Establecer parámetros
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre_empresa'];
$direccion = $_POST['direccion_empresa'];
$telefono = $_POST['tel_empresa'];
$email = $_POST['email_empresa'];

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO empresas (rfc_empresa, nombre, direccion, telefono, email) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $rfc_empresa, $nombre, $direccion, $telefono, $email);

// Ejecutar
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Empresa creada exitosamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al guardar la empresa: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
