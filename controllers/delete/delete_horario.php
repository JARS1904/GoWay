<?php
require_once '../../config/conexion_bd.php';

if (!isset($_GET['id'])) {
    die("ID de horario no proporcionado.");
}

$id = $_GET['id'];

$conn = $conexion;

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "DELETE FROM horarios WHERE id_horario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: /GoWay/pages/admin/horarios.php?mensaje=Horario eliminado exitosamente");
    exit();
} else {
    echo "Error al eliminar el horario: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
