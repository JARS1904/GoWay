
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
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $email, $password, $rol);

// Establecer parámetros y ejecutar
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$rol = $_POST['rol'];

$stmt->execute();


echo "Nuevo user creado exitosamente";
// Redireccionar después de 2 segundos
header("Refresh: 2; URL=/GoWay/pages/usuarios.php");


$stmt->close();
$conn->close();
?>
