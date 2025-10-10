
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
$stmt = $conn->prepare("INSERT INTO conductores (rfc_conductor, rfc_empresa, nombre, licencia, telefono) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $rfc_conductor, $rfc_empresa, $nombre, $licencia, $telefono);

// Establecer parámetros y ejecutar
$rfc_conductor = $_POST['rfc_conductor'];
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre'];
$licencia = $_POST['licencia'];
$telefono = $_POST['telefono'];
$stmt->execute();

echo "Conductor guardado exitosamente";

// Redireccionar después de 2 segundos
header("Refresh: 2; URL=/GoWay/pages/checadores.php");


$stmt->close();
$conn->close();
?>
