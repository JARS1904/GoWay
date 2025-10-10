
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
$stmt = $conn->prepare("INSERT INTO horarios (id_ruta, dia_semana, hora_salida, hora_llegada, frecuencia) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $id_ruta, $dia_semana, $hora_salida, $hora_llegada, $frecuencia);

// Establecer parámetros y ejecutar
$id_ruta = $_POST['id_ruta'];
$dia_semana = $_POST['dia_semana'];
$hora_salida = $_POST['hora_salida'];
$hora_llegada = $_POST['hora_llegada'];
$frecuencia = $_POST['frecuencia'];
$stmt->execute();

echo "Horario guardado exitosamente";

// Redireccionar después de 2 segundos
header("Refresh: 2; URL=/GoWay/pages/paradas.php");


$stmt->close();
$conn->close();
?>
