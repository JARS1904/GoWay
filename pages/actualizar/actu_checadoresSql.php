
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
$rfc_checador = $_POST['rfc_checador'];
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['password'];
$activo = $_POST['activo'];

// Preparar la consulta SQL
$sql = "UPDATE checadores SET
rfc_empresa = ?,
nombre = ?,
usuario = ?,
contrasena = ?,
activo = ?
WHERE rfc_checador = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
    exit();
}

// Vincular parámetros
$stmt->bind_param("ssssis", $rfc_empresa, $nombre, $usuario, $contrasena, $activo, $rfc_checador);

// Ejecutar consulta
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Checador actualizado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
