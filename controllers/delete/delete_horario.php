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
    // Insertar notificación de sistema
    $titulo_notif = "Horario Eliminado";
    $mensaje_notif = "El administrador ha eliminado un horario. Por favor revisa las actualizaciones.";
    $tipo_notif = "horario";
    $sql_notif = "INSERT INTO notificaciones (id_usuario, titulo, mensaje, tipo) VALUES (NULL, ?, ?, ?)";
    if ($stmt_notif = $conn->prepare($sql_notif)) {
        $stmt_notif->bind_param("sss", $titulo_notif, $mensaje_notif, $tipo_notif);
        $stmt_notif->execute();
        $stmt_notif->close();
    }

    header("Location: /GoWay/pages/admin/horarios.php?mensaje=Horario eliminado exitosamente");
    exit();
} else {
    echo "Error al eliminar el horario: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
