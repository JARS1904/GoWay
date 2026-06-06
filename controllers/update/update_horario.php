<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

$conn = $conexion;
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

$id_horario   = isset($_POST['id_horario'])   ? (int)trim($_POST['id_horario']) : 0;
$id_ruta      = isset($_POST['id_ruta'])      ? (int)trim($_POST['id_ruta']) : 0;
$tipo_dia     = isset($_POST['tipo_dia'])     ? trim($_POST['tipo_dia']) : '';
$hora_salida  = isset($_POST['hora_salida'])  ? trim($_POST['hora_salida']) : '';
$hora_llegada = isset($_POST['hora_llegada']) ? trim($_POST['hora_llegada']) : '';
$frecuencia   = isset($_POST['frecuencia'])   ? trim($_POST['frecuencia']) : '';

if ($id_horario <= 0 || $id_ruta <= 0 || $tipo_dia === '' || $hora_salida === '' || $hora_llegada === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
    exit;
}

try {
    $stmt = $conn->prepare(
        "UPDATE horarios
         SET    id_ruta = ?, tipo_dia = ?, hora_salida = ?, hora_llegada = ?, frecuencia = ?
         WHERE  id_horario = ?"
    );
    $stmt->bind_param("issssi", $id_ruta, $tipo_dia, $hora_salida, $hora_llegada, $frecuencia, $id_horario);

    if ($stmt->execute()) {
        $conn->query('SET NAMES utf8');
        $resRuta = $conn->query("SELECT nombre FROM rutas WHERE id_ruta = $id_ruta");
        $nombreRuta = $resRuta->fetch_assoc()['nombre'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Horario actualizado exitosamente',
            'registroActualizado' => [
                'id_horario' => $id_horario,
                'id_ruta' => $id_ruta,
                'ruta' => $nombreRuta,
                'tipo_dia' => $tipo_dia,
                'hora_salida' => $hora_salida,
                'hora_llegada' => $hora_llegada,
                'frecuencia' => $frecuencia
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
