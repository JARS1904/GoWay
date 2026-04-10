<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Iniciar el manejo de errores
ini_set('display_errors', 0);
error_reporting(E_ALL);

function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../../config/conexion_bd.php';

try {
    $conn = $conexion;
    if ($conn->connect_error) {
        sendResponse(500, ["error" => "Error de conexión: " . $conn->connect_error]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $placa = isset($_GET['placa']) ? strtoupper(trim($_GET['placa'])) : '';

        if (empty($placa)) {
            sendResponse(400, ["error" => "El parámetro 'placa' es requerido"]);
        }

        // Consultamos la información del vehículo, su empresa, su asignación activa y la ruta (incluyendo ruta de retorno)
        $sql = "SELECT 
                    v.id_vehiculo,
                    v.placa,
                    v.modelo,
                    v.capacidad,
                    v.activo AS vehiculo_activo,
                    e.nombre AS empresa_nombre,
                    a.id_asignacion,
                    c.nombre AS conductor_nombre,
                    c.rfc_conductor,
                    r.id_ruta,
                    r.nombre AS ruta_nombre,
                    r.origen AS ruta_origen,
                    r.destino AS ruta_destino,
                    r.id_ruta_retorno,
                    ret.nombre AS ruta_retorno_nombre,
                    ret.origen AS ruta_retorno_origen,
                    ret.destino AS ruta_retorno_destino
                FROM vehiculos v
                LEFT JOIN empresas e ON v.rfc_empresa = e.rfc_empresa
                LEFT JOIN (
                    SELECT id_asignacion, id_vehiculo, rfc_conductor, id_ruta, activa, fecha
                    FROM asignaciones
                    WHERE activa = 1
                ) a ON v.id_vehiculo = a.id_vehiculo
                LEFT JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
                LEFT JOIN rutas r ON a.id_ruta = r.id_ruta
                LEFT JOIN rutas ret ON r.id_ruta_retorno = ret.id_ruta
                WHERE v.placa = ?
                ORDER BY a.fecha DESC
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            sendResponse(500, ["error" => "Error al preparar la consulta: " . $conn->error]);
        }
        
        $stmt->bind_param("s", $placa);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            sendResponse(404, ["error" => "No se encontró ningún vehículo con la placa '$placa'"]);
        }

        $vehiculo = $result->fetch_assoc();
        
        $stmt->close();
        
        sendResponse(200, [
            "success" => true,
            "data" => $vehiculo
        ]);
    }

    sendResponse(405, ["error" => "Método no permitido. Use GET"]);

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
}
