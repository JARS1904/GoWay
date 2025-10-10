
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goway";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO empresas (rfc_empresa, nombre, direccion, telefono, email) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $rfc_empresa, $nombre, $direccion, $telefono, $email);

// Establecer parámetros y ejecutar
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre_empresa'];
$direccion = $_POST['direccion_empresa'];
$telefono = $_POST['tel_empresa'];
$email = $_POST['email_empresa'];
$stmt->execute();


echo "Nueva empresa creada exitosamente";
// Redireccionar después de 2 segundos
header("Refresh: 2; URL=/GoWay/pages/vehiculos.php");


$stmt->close();
$conn->close();
?>
