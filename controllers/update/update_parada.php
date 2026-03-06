<?php
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

$conn = $conexion;
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

$id_parada            = isset($_POST['id_parada'])           ? (int)trim($_POST['id_parada'])                  : 0;
$nombre               = isset($_POST['nombre'])              ? trim($_POST['nombre'])                          : '';
$orden                = isset($_POST['orden'])               ? (int)trim($_POST['orden'])                      : 0;
$minutos_desde_origen = isset($_POST['minutos_desde_origen'])? (int)trim($_POST['minutos_desde_origen'])       : 0;

if ($id_parada <= 0 || $nombre === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'id_parada y nombre son requeridos']);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE paradas_ruta
     SET    nombre = ?, orden = ?, minutos_desde_origen = ?
     WHERE  id_parada = ?"
);
$stmt->bind_param("siii", $nombre, $orden, $minutos_desde_origen, $id_parada);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Parada actualizada exitosamente']);
} else {
    $msg = ($conn->errno === 1062)
        ? 'Ya existe una parada con ese orden en esta ruta'
        : 'Error al actualizar: ' . $stmt->error;
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $msg]);
}

$stmt->close();
$conn->close();
