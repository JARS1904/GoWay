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

    // Manejar solicitud GET para obtener favoritas
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_favorites') {
        $id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
        
        // Si no hay id_usuario, devolver array vacío en lugar de error
        if (!$id_usuario) {
            sendResponse(200, []);
        }
        
        $sql = "SELECT rf.id_favorita, r.id_ruta, r.nombre, r.origen, r.destino, 
                       r.rfc_empresa, r.paradas,
                       e.nombre AS empresa_nombre, e.telefono AS empresa_telefono,
                       e.direccion AS empresa_direccion, e.email AS empresa_email,
                       rf.fecha_agregada
                FROM rutas_favoritas rf
                JOIN rutas r ON rf.id_ruta = r.id_ruta
                JOIN empresas e ON r.rfc_empresa = e.rfc_empresa
                WHERE rf.id_usuario = ?
                ORDER BY rf.fecha_agregada DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $favoritas = [];
        while ($row = $result->fetch_assoc()) {
            // Obtener horarios para cada ruta
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
            while ($horario = $result_h->fetch_assoc()) {
                $horarios[] = $horario;
            }
            
            $row['horarios'] = $horarios;
            
            // Procesar paradas como array
            $row['paradas'] = explode(', ', $row['paradas']);
            
            $favoritas[] = $row;
            $stmt_h->close();
        }
        
        sendResponse(200, $favoritas);
        $stmt->close();
    }

    // Manejar solicitud POST para agregar/eliminar favoritas
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Intentar obtener datos de JSON primero
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Si no es JSON válido, intentar obtener de POST tradicional
        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            $data = $_POST;
        }
        
        // Si aún no hay datos, intentar obtener de GET (para casos especiales)
        if (empty($data)) {
            $data = $_GET;
        }
        
        if (empty($data['action'])) {
            sendResponse(400, ["error" => "Acción no especificada"]);
        }

        $action = $data['action'];
        // Obtener id_usuario de múltiples fuentes
        $id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : 
                     (isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0);
        $id_ruta = isset($data['id_ruta']) ? intval($data['id_ruta']) : 0;

        if (!$id_usuario) {
            sendResponse(400, ["error" => "ID de usuario requerido. Envía id_usuario en el body (JSON o FormData) o en URL (?id_usuario=123). Datos recibidos: " . json_encode($data)]);
        }
        
        if (!$id_ruta) {
            sendResponse(400, ["error" => "ID de ruta requerido (id_ruta)"]);
        }

        // Agregar a favoritas
        if ($action === 'add_favorite') {
            // Verificar que la ruta existe
            $sql_check = "SELECT id_ruta FROM rutas WHERE id_ruta = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $id_ruta);
            $stmt_check->execute();
            
            if ($stmt_check->get_result()->num_rows === 0) {
                sendResponse(404, ["error" => "Ruta no encontrada"]);
            }
            $stmt_check->close();
            
            $sql = "INSERT INTO rutas_favoritas (id_usuario, id_ruta) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE fecha_agregada = CURRENT_TIMESTAMP";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_usuario, $id_ruta);
            
            if ($stmt->execute()) {
                sendResponse(200, ["success" => true, "message" => "Ruta agregada a favoritas"]);
            } else {
                sendResponse(500, ["error" => "Error al agregar favorita"]);
            }
            $stmt->close();
        }
        // Eliminar de favoritas
        else if ($action === 'remove_favorite') {
            $sql = "DELETE FROM rutas_favoritas WHERE id_usuario = ? AND id_ruta = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_usuario, $id_ruta);
            
            if ($stmt->execute()) {
                sendResponse(200, ["success" => true, "message" => "Ruta eliminada de favoritas"]);
            } else {
                sendResponse(500, ["error" => "Error al eliminar favorita"]);
            }
            $stmt->close();
        }
        // Verificar si es favorita
        else if ($action === 'is_favorite') {
            $sql = "SELECT id_favorita FROM rutas_favoritas WHERE id_usuario = ? AND id_ruta = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_usuario, $id_ruta);
            $stmt->execute();
            
            $is_favorite = $stmt->get_result()->num_rows > 0;
            sendResponse(200, ["is_favorite" => $is_favorite, "id_ruta" => $id_ruta]);
            $stmt->close();
        }
        else {
            sendResponse(400, ["error" => "Acción no válida. Use: add_favorite, remove_favorite, is_favorite"]);
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
