<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "goway");

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener datos del formulario
$id_horario = $_POST['id_horario'];
$id_ruta = $_POST['id_ruta'];
$dia_semana = $_POST['dia_semana'];
$hora_salida = $_POST['hora_salida'];
$hora_llegada = $_POST['hora_llegada'];
$frecuencia = $_POST['frecuencia'];

// Preparar y ejecutar la consulta de actualización
$sql = "UPDATE horarios SET id_ruta = ?, dia_semana = ?, hora_salida = ?, hora_llegada = ?, frecuencia = ? WHERE id_horario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssi", $id_ruta, $dia_semana, $hora_salida, $hora_llegada, $frecuencia, $id_horario);

// Ejecutar consulta
if ($stmt->execute()) {
echo "Horario actualizado exitosamente";

header ("Refresh: 2; URL=/GoWay/pages/horarios.php");
} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}


$stmt->close();
$conn->close();
?>
