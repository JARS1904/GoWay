<?php
header('Content-Type: application/json');
require_once '../../config/conexion_bd.php';

if (!isset($_POST['id_ruta'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de ruta no proporcionado']);
    exit;
}

$id = (int)$_POST['id_ruta'];

$conn = $conexion;

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 0. Quitar referencias de retorno
    $sql0 = "UPDATE rutas SET id_ruta_retorno = NULL WHERE id_ruta_retorno = ?";
    $stmt0 = $conn->prepare($sql0);
    $stmt0->bind_param("i", $id);
    $stmt0->execute();

    // 0.5. Eliminar paradas relacionadas
    $sql05 = "DELETE FROM paradas_ruta WHERE id_ruta = ?";
    $stmt05 = $conn->prepare($sql05);
    $stmt05->bind_param("i", $id);
    $stmt05->execute();

    // 0.75. Quitar referencias en reportes (mantener el reporte histórico, pero sin ruta)
    $sql075 = "UPDATE reportes SET id_ruta = NULL WHERE id_ruta = ?";
    $stmt075 = $conn->prepare($sql075);
    $stmt075->bind_param("i", $id);
    $stmt075->execute();

    // 3. Eliminar la ruta
    $sql3 = "DELETE FROM rutas WHERE id_ruta = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Ruta eliminada exitosamente']);
    exit;
} catch (mysqli_sql_exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    
    if ($e->getCode() == 1451) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'No se puede eliminar esta ruta porque tiene horarios o asignaciones activas. Por favor, elimínalos o reasígnalos primero.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error de base de datos al eliminar la ruta: ' . $e->getMessage()]);
    }
    exit;
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error inesperado al eliminar la ruta: ' . $e->getMessage()]);
    exit;
}