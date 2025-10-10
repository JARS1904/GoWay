
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
$stmt = $conn->prepare("INSERT INTO vehiculos (placa, rfc_empresa, modelo, capacidad) VALUES (?, ?, ?, ? )");
$stmt->bind_param("sssi", $placa, $rfc_empresa, $modelo, $capacidad);

// Establecer parámetros y ejecutar
$placa = $_POST['placa'];
$rfc_empresa = $_POST['rfc_empresa'];
$modelo = $_POST['modelo'];
$capacidad = $_POST['capacidad'];
$stmt->execute();

echo "Vehículo guardado exitosamente";
// Redireccionar después de 2 segundos
header("Refresh: 2; URL=/GoWay/pages/conductores.php");



$stmt->close();
$conn->close();
?>
