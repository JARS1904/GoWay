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

$id_ruta              = isset($_POST['id_ruta'])             ? (int)trim($_POST['id_ruta'])                    : 0;
$nombre               = isset($_POST['nombre'])              ? trim($_POST['nombre'])                          : '';
$orden                = isset($_POST['orden'])               ? (int)trim($_POST['orden'])                      : 0;
$minutos_desde_origen = isset($_POST['minutos_desde_origen'])? (int)trim($_POST['minutos_desde_origen'])       : 0;

if ($id_ruta <= 0 || $nombre === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'id_ruta y nombre son requeridos']);
    exit;
}

try {
    $stmt = $conn->prepare(
        "INSERT INTO paradas_ruta (id_ruta, nombre, orden, minutos_desde_origen)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isii", $id_ruta, $nombre, $orden, $minutos_desde_origen);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Parada agregada exitosamente', 'id_parada' => $stmt->insert_id]);
    } else {
        // Código 1062 = entrada duplicada (id_ruta + orden ya existe)
        $msg = ($conn->errno === 1062)
            ? 'Ya existe una parada con ese orden en esta ruta'
            : 'Error al insertar: ' . $stmt->error;
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $msg]);
    }

    $stmt->close();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
