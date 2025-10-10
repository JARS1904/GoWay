
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
$rfc_conductor = $_POST['rfc_conductor'];
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre'];
$licencia = $_POST['licencia'];
$telefono = $_POST['telefono'];
$activo = isset($_POST['activo']) ? 1 : 0;

// Preparar la consulta SQL
$sql = "UPDATE conductores SET

rfc_empresa = ?,
nombre = ?,
licencia = ?,
telefono = ?,
activo = ?
WHERE rfc_conductor = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
die("Error en la preparación: " . $conn->error);
}

// Vincular parámetros
$stmt->bind_param("ssssis", $rfc_empresa, $nombre, $licencia, $telefono, $activo, $rfc_conductor);

// Ejecutar consulta
if ($stmt->execute()) {
echo "Conductor actualizado exitosamente";

header ("Refresh: 2; URL=/GoWay/pages/conductores.php");
} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>

