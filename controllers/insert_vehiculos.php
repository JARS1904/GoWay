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
$stmt = $conn->prepare("INSERT INTO vehiculos (placa, rfc_empresa, modelo, capacidad, activo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssii", $placa, $rfc_empresa, $modelo, $capacidad, $activo);

// Establecer parámetros y ejecutar
$placa = $_POST['placa'];
$rfc_empresa = $_POST['rfc_empresa'];
$modelo = $_POST['modelo'];
$capacidad = $_POST['capacidad'];
$activo = $_POST['activo'];
$stmt->execute();

echo "Vehículo guardado exitosamente";
// Redireccionar después de 2 segundos
header("Refresh: 2; URL=/GoWay/pages/vehiculos.php");

$stmt->close();
$conn->close();
?>
