
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
$stmt = $conn->prepare("INSERT INTO asignaciones (rfc_empresa, id_vehiculo, rfc_conductor, id_ruta, id_horario, fecha) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissss", $rfc_empresa, $id_vehiculo, $rfc_conductor, $id_ruta, $id_horario, $fecha);

// Establecer parámetros y ejecutar
$rfc_empresa = $_POST['rfc_empresa'];
$id_vehiculo = $_POST['id_vehiculo'];
$rfc_conductor = $_POST['rfc_conductor'];
$id_ruta = $_POST['id_ruta'];
$id_horario = $_POST['id_horario'];
$fecha = $_POST['fecha'];
$stmt->execute();

echo "Asignación guardada exitosamente";

// Redireccionar después de 2 segundos
header("Refresh: 2; URL=/GoWay/pages/usuarios.php");

$stmt->close();
$conn->close();
?>
