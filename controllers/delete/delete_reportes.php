<?php
require_once '../../config/conexion_bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    try {
        $id = $conexion->real_escape_string($_POST['id']);

        // Verificar que el reporte exista
        $check_sql = "SELECT id FROM reportes WHERE id = ?";
        $check_stmt = $conexion->prepare($check_sql);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'El reporte no existe'
            ]);
            exit;
        }
        $check_stmt->close();

        // Eliminar reporte
        $sql = "DELETE FROM reportes WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Reporte eliminado exitosamente'
            ]);
        } else {
            throw new Exception("Error al eliminar: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID del reporte no especificado'
    ]);
}

$conexion->close();
?>