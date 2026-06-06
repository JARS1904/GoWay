<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

// Crear conexión
$conn = $conexion;

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Obtener y validar datos
$id_ruta = isset($_POST['id_ruta']) ? (int)$_POST['id_ruta'] : 0;
$tipo_dia = isset($_POST['tipo_dia']) ? trim($_POST['tipo_dia']) : '';
$hora_salida = isset($_POST['hora_salida']) ? trim($_POST['hora_salida']) : '';
$hora_llegada = isset($_POST['hora_llegada']) ? trim($_POST['hora_llegada']) : '';
$frecuencia = isset($_POST['frecuencia']) ? trim($_POST['frecuencia']) : '';

// Validar campos requeridos
if (empty($id_ruta) || empty($tipo_dia) || empty($hora_salida) || empty($hora_llegada)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos requeridos']);
    exit;
}

try {
    // Preparar y ejecutar consulta
    $stmt = $conn->prepare("INSERT INTO horarios (id_ruta, tipo_dia, hora_salida, hora_llegada, frecuencia) VALUES (?, ?, ?, ?, ?)");

    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("issss", $id_ruta, $tipo_dia, $hora_salida, $hora_llegada, $frecuencia);

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        
        $conn->query('SET NAMES utf8');
        $resRuta = $conn->query("SELECT nombre FROM rutas WHERE id_ruta = $id_ruta");
        $nombreRuta = $resRuta->fetch_assoc()['nombre'];
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Horario agregado exitosamente',
            'nuevoRegistro' => [
                'id_horario' => $id,
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
        echo json_encode(['success' => false, 'message' => 'Error al insertar: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
