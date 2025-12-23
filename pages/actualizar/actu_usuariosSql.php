
<?php
header('Content-Type: application/json');

// Configuración de la base de datos
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

// Recoger datos del formulario
$id = $_POST['id_usuario'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$rol = $_POST['rol'];

// Preparar la consulta SQL
$sql = "UPDATE usuarios SET
nombre = ?,
email = ?,
password = ?,
rol = ?
WHERE id = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
    exit();
}

// Vincular parámetros
$stmt->bind_param("ssssi", $nombre, $email, $password, $rol, $id);

// Ejecutar consulta
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Usuario actualizado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error en la actualización: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

