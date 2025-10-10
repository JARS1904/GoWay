
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
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre_empresa'];
$direccion = $_POST['direccion_empresa'];
$telefono = $_POST['telefono'];
$email = $_POST['email_empresa'];
$activo = isset($_POST['activo']) ? 1 : 0;

// Preparar la consulta SQL
$sql = "UPDATE empresas SET
nombre = ?,
direccion = ?,
telefono = ?,
email = ?,
activo = ?
WHERE rfc_empresa = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
die("Error en la preparación: " . $conn->error);
}

// Vincular parámetros
$stmt->bind_param("ssssis", $nombre, $direccion, $telefono, $email, $activo, $rfc_empresa);

// Ejecutar consulta
if ($stmt->execute()) {
echo "Empresa actualizada exitosamente";

header ("Refresh: 2; URL=/GoWay/pages/empresas.php");
} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>

