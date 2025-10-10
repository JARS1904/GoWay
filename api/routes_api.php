<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goway";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        sendResponse(500, ["error" => "Error de conexión: " . $conn->connect_error]);
    }

    // Manejar solicitud GET para obtener ubicaciones
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'locations') {
        $sql = "SELECT DISTINCT origen AS location FROM rutas WHERE activa = 1 
                UNION 
                SELECT DISTINCT destino FROM rutas WHERE activa = 1";
        $result = $conn->query($sql);
        
        if (!$result) {
            sendResponse(500, ["error" => "Error en consulta: " . $conn->error]);
        }
        
        $locations = [];
        while($row = $result->fetch_assoc()) {
            $locations[] = $row["location"];
        }
        
        sendResponse(200, $locations);
    }

    // Manejar solicitud POST para buscar rutas
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendResponse(400, ["error" => "JSON inválido"]);
        }
        
        if (empty($data['action'])) {
            sendResponse(400, ["error" => "Acción no especificada"]);
        }

        if ($data['action'] === 'search_routes') {
            if (empty($data['origin']) || empty($data['destination'])) {
                sendResponse(400, ["error" => "Origen y destino son requeridos"]);
            }
            
            $origin = $conn->real_escape_string($data['origin']);
            $destination = $conn->real_escape_string($data['destination']);
            
            $sql = "SELECT DISTINCT r.id_ruta, r.nombre, r.origen, r.destino, r.rfc_empresa, r.paradas,
                   e.nombre AS empresa_nombre, e.telefono AS empresa_telefono,
                   e.direccion AS empresa_direccion, e.email AS empresa_email
                FROM rutas r
                JOIN empresas e ON r.rfc_empresa = e.rfc_empresa
                WHERE r.origen = ? AND r.destino = ? AND r.activa = 1";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $origin, $destination);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $routes = [];
            while($row = $result->fetch_assoc()) {
                // Obtener horarios para cada ruta con información de conductor y vehículo
                $sql_horarios = "SELECT h.*, 
                                c.nombre AS conductor_nombre, 
                                c.licencia AS conductor_licencia,
                                v.placa AS vehiculo_placa, 
                                v.modelo AS vehiculo_modelo, 
                                v.capacidad AS vehiculo_capacidad
                            FROM horarios h
                            LEFT JOIN asignaciones a ON h.id_horario = a.id_horario AND h.id_ruta = a.id_ruta AND a.activa = 1
                            LEFT JOIN conductores c ON a.rfc_conductor = c.rfc_conductor
                            LEFT JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo
                            WHERE h.id_ruta = ?";

                $stmt_h = $conn->prepare($sql_horarios);
                $stmt_h->bind_param("i", $row['id_ruta']);
                $stmt_h->execute();
                $result_h = $stmt_h->get_result();
                
                $horarios = [];
                while($horario = $result_h->fetch_assoc()) {
                    $horarios[] = $horario;
                }
                
                $row['horarios'] = $horarios;
                
                // Procesar paradas como array
                $row['paradas'] = explode(', ', $row['paradas']);
                
                $routes[] = $row;
            }
            
            sendResponse(200, $routes);
        }
    }

    sendResponse(400, ["error" => "Solicitud no válida"]);

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno del servidor: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>