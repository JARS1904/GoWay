<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../config/conexion_bd.php';

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    
    try {
        // Obtener y sanitizar los datos del formulario
        $id_vehiculo = isset($_POST['vehiculo']) ? (int)$_POST['vehiculo'] : 0;
        $rfc_conductor = $conexion->real_escape_string($_POST['conductor'] ?? '');
        $id_ruta = isset($_POST['ruta']) ? (int)$_POST['ruta'] : 0;
        $tipo_incidente = $conexion->real_escape_string($_POST['tipoIncidente'] ?? '');
        $fecha_incidente = $conexion->real_escape_string($_POST['fechaIncidente'] ?? '');
        $descripcion = $conexion->real_escape_string($_POST['descripcion'] ?? '');
        $gravedad = $conexion->real_escape_string($_POST['gravedad'] ?? 'media');

        // Validar datos requeridos
        if (empty($id_vehiculo) || empty($rfc_conductor) || empty($id_ruta) || empty($tipo_incidente) || empty($fecha_incidente) || empty($descripcion)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos requeridos']);
            exit;
        }

        // Preparar la consulta SQL
        $sql = "INSERT INTO reportes (id_vehiculo, rfc_conductor, id_ruta, tipo_incidente, 
                                    fecha_incidente, descripcion, gravedad) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Preparar y ejecutar la consulta
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("issssss", 
            $id_vehiculo, 
            $rfc_conductor, 
            $id_ruta, 
            $tipo_incidente, 
            $fecha_incidente, 
            $descripcion, 
            $gravedad
        );

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Reporte guardado exitosamente']);
        } else {
            throw new Exception("Error al guardar el reporte: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Si se accede sin POST, redirigir
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>
