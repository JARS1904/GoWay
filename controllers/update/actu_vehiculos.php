
<?php
// actualizar_vehiculo.php

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goway";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Recoger datos del formulario
$id_vehiculo = $_POST['id_vehiculo'];
$placa = $_POST['placa'];
$modelo = $_POST['modelo'];
$capacidad = $_POST['capacidad'];
$activo = $_POST['activo'];
$rfc_empresa = $_POST['rfc_empresa'];

// Preparar la consulta SQL
$sql = "UPDATE vehiculos SET
placa = ?,
modelo = ?,
capacidad = ?,
activo = ?,
rfc_empresa = ?
WHERE id_vehiculo = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
die("Error en la preparación: " . $conn->error);
}

// Vincular parámetros
$stmt->bind_param("ssiisi", $placa, $modelo, $capacidad, $activo, $rfc_empresa, $id_vehiculo);

// Ejecutar consulta
if ($stmt->execute()) {
echo "Vehículo actualizado exitosamente";

header ("Refresh: 2; URL=/GoWay/pages/vehiculos.php");
} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>

