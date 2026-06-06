<?php
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

// Crear conexión
$conn = $conexion;

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Establecer parámetros
$rfc_empresa = $_POST['rfc_empresa'];
if (isset($_SESSION['rol']) && $_SESSION['rol'] == 4) {
    $rfc_empresa = $_SESSION['rfc_empresa'];
}
$id_vehiculo = $_POST['id_vehiculo'];
$rfc_conductor = $_POST['rfc_conductor'];
$id_ruta = $_POST['id_ruta'];
$id_horario = $_POST['id_horario'];
$fecha = $_POST['fecha'];
$estado = $_POST['estado'] ?? 'programado'; // Default to programado just in case

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO asignaciones (rfc_empresa, id_vehiculo, rfc_conductor, id_ruta, id_horario, fecha, estado, asientos_disp) SELECT ?, ?, ?, ?, ?, ?, ?, capacidad FROM vehiculos WHERE id_vehiculo = ?");
$stmt->bind_param("sisisssi", $rfc_empresa, $id_vehiculo, $rfc_conductor, $id_ruta, $id_horario, $fecha, $estado, $id_vehiculo);

if ($stmt->execute()) {
    $id_insertado = $conn->insert_id;
    
    // Obtener el registro completo con JOINs para mostrar en la tabla
    $sql_nuevo = "SELECT a.*, v.placa, r.nombre as nombre_ruta, h.tipo_dia, h.hora_salida 
                  FROM asignaciones a 
                  LEFT JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo 
                  LEFT JOIN rutas r ON a.id_ruta = r.id_ruta
                  LEFT JOIN horarios h ON a.id_horario = h.id_horario
                  WHERE a.id_asignacion = ?";
    $stmt_nuevo = $conn->prepare($sql_nuevo);
    $stmt_nuevo->bind_param("i", $id_insertado);
    $stmt_nuevo->execute();
    $result_nuevo = $stmt_nuevo->get_result();
    $nuevoRegistro = $result_nuevo->fetch_assoc();
    $stmt_nuevo->close();

    echo json_encode(["success" => true, "message" => "Asignación agregada correctamente", "nuevoRegistro" => $nuevoRegistro]);
} else {
    echo json_encode(["success" => false, "message" => "Error al agregar la asignación: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
