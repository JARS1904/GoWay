
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
$stmt = $conn->prepare("INSERT INTO checadores (rfc_checador, rfc_empresa, nombre, usuario, contrasena) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $rfc_checador, $rfc_empresa, $nombre, $usuario, $contrasena);

// Establecer parámetros y ejecutar
$rfc_checador = $_POST['rfc_checador'];
$rfc_empresa = $_POST['rfc_empresa'];
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['password'];
$stmt->execute();

echo "checador guardado exitosamente";

// Redireccionar después de 2 segundos
header("Refresh: 2; URL=/GoWay/pages/rutas.php");


$stmt->close();
$conn->close();
?>
