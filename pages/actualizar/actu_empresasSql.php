
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
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre_empresa'];
$direccion = $_POST['direccion_empresa'];
$telefono = $_POST['telefono'];
$email = $_POST['email_empresa'];
$activo = $_POST['activo'];

// Preparar la consulta SQL
$sql = "UPDATE empresas SET nombre = ?, direccion = ?, telefono = ?, email = ?, activo = ? WHERE rfc_empresa = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
    exit();
}

// Vincular parámetros
$stmt->bind_param("ssssis", $nombre, $direccion, $telefono, $email, $activo, $rfc_empresa);

// Ejecutar consulta
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Empresa actualizada exitosamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>

