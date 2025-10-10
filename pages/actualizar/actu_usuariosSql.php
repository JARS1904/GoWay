
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
$id = $_POST['id_usuario'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$rol = $_POST['rol'];


// Preparar la consulta SQL
$sql = "UPDATE usuarios SET
nombre = ?,
email = ?,
password = ?,
rol = ?
WHERE id = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
die("Error en la preparación: " . $conn->error);
}

// Vincular parámetros
$stmt->bind_param("ssssi", $nombre, $email, $password, $rol, $id);

// Ejecutar consulta
if ($stmt->execute()) {
echo "Usuario actualizado exitosamente";

header ("Refresh: 2; URL=/GoWay/pages/usuarios.php");
} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>
