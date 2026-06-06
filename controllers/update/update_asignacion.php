<?php
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$id_asignacion = $_POST['id_asignacion'];
$rfc_empresa = $_POST['rfc_empresa'];
if (isset($_SESSION['rol']) && $_SESSION['rol'] == 4) {
    $rfc_empresa = $_SESSION['rfc_empresa'];
}
$id_vehiculo = $_POST['id_vehiculo'];
$rfc_conductor = $_POST['rfc_conductor'];
$id_ruta = $_POST['id_ruta'];
$id_horario = $_POST['id_horario'];
$fecha = $_POST['fecha'];
$estado = $_POST['estado'];
$asientos_disp = $_POST['asientos_disp'];
$activa = $_POST['activa'];

// Obtener el vehículo actual
$stmt_check = $conn->prepare("SELECT id_vehiculo FROM asignaciones WHERE id_asignacion = ?");
$stmt_check->bind_param("i", $id_asignacion);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row = $result_check->fetch_assoc();
$current_vehiculo = $row['id_vehiculo'];
$stmt_check->close();

if ($current_vehiculo != $id_vehiculo) {
    // Vehículo cambió, actualizar asientos_disp
    $query = "UPDATE asignaciones a 
              JOIN vehiculos v ON v.id_vehiculo = ?
              SET a.rfc_empresa = ?,
                  a.rfc_conductor = ?, 
                  a.id_vehiculo = ?, 
                  a.id_ruta = ?,
                  a.id_horario = ?,
                  a.fecha = ?, 
                  a.estado = ?,
                  a.activa = ?,
                  a.asientos_disp = v.capacidad
              WHERE a.id_asignacion = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssiissii", $id_vehiculo, $rfc_empresa, $rfc_conductor, $id_vehiculo, $id_ruta, $id_horario, $fecha, $estado, $activa, $id_asignacion);
} else {
    // Vehículo es el mismo, actualizar asientos_disp con el valor proporcionado
    $query = "UPDATE asignaciones 
              SET rfc_empresa = ?,
                  rfc_conductor = ?, 
                  id_ruta = ?,
                  id_horario = ?,
                  fecha = ?, 
                  estado = ?,
                  activa = ?,
                  asientos_disp = ?
              WHERE id_asignacion = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiissiii", $rfc_empresa, $rfc_conductor, $id_ruta, $id_horario, $fecha, $estado, $activa, $asientos_disp, $id_asignacion);
}

if ($stmt->execute()) {
    // Fetch the updated record
    $sql_updated = "SELECT a.*, v.placa, r.nombre as nombre_ruta, h.tipo_dia, h.hora_salida 
                  FROM asignaciones a 
                  LEFT JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo 
                  LEFT JOIN rutas r ON a.id_ruta = r.id_ruta
                  LEFT JOIN horarios h ON a.id_horario = h.id_horario
                  WHERE a.id_asignacion = ?";
    $stmt_updated = $conn->prepare($sql_updated);
    $stmt_updated->bind_param("i", $id_asignacion);
    $stmt_updated->execute();
    $result_updated = $stmt_updated->get_result();
    $registroActualizado = $result_updated->fetch_assoc();
    $stmt_updated->close();

    echo json_encode(["success" => true, "message" => "Asignación actualizada correctamente", "registroActualizado" => $registroActualizado]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar la asignación: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
