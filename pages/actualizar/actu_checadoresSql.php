
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
$rfc_checador = $_POST['rfc_checador'];
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['password'];
$activo = isset($_POST['activo']) ? 1 : 0;

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
die("Error en la preparación: " . $conn->error);
}

// Vincular parámetros
$stmt->bind_param("ssssis", $rfc_empresa, $nombre, $usuario, $contrasena, $activo, $rfc_checador);

// Ejecutar consulta
if ($stmt->execute()) {
echo "Checador actualizado exitosamente";

header ("Refresh: 2; URL=/GoWay/pages/checadores.php");
} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>
