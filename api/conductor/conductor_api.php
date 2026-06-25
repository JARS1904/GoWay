<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// El usuario especificó require_once '../config/conexion_bd.php';
// pero al estar en api/conductor/, la ruta correcta hacia config/conexion_bd.php
// es con dos niveles de subida: ../../config/conexion_bd.php
require_once '../../config/conexion_bd.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido. Se espera POST."]);
        exit;
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['action']) || $data['action'] !== 'get_routes') {
        http_response_code(400);
        echo json_encode(["error" => "Acción inválida. Se espera 'action': 'get_routes'."]);
        exit;
    }

    // Validar que el rfc_conductor no venga vacío.
    if (empty($data['rfc_conductor'])) {
        http_response_code(400);
        echo json_encode(["error" => "El campo rfc_conductor no puede estar vacío."]);
        exit;
    }

    $rfc_conductor = $data['rfc_conductor'];
    
    // Hacer una consulta a la tabla conductores para obtener el rfc_empresa asociado a ese conductor.
    $stmt_empresa = $conexion->prepare("SELECT rfc_empresa FROM conductores WHERE rfc_conductor = ?");
    $stmt_empresa->bind_param("s", $rfc_conductor);
    $stmt_empresa->execute();
    $result_empresa = $stmt_empresa->get_result();

    // Si no existe, devolver un error 404.
    if ($result_empresa->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Conductor no encontrado o no tiene empresa asociada."]);
        exit;
    }

    $row_empresa = $result_empresa->fetch_assoc();
    $rfc_empresa = $row_empresa['rfc_empresa'];
    
    // Hacer una consulta a la tabla rutas para traer las rutas activas (activa = 1) que pertenezcan ÚNICAMENTE a ese rfc_empresa.
    $stmt_rutas = $conexion->prepare("SELECT id_ruta, nombre, origen, destino FROM rutas WHERE rfc_empresa = ? AND activa = 1");
    $stmt_rutas->bind_param("s", $rfc_empresa);
    $stmt_rutas->execute();
    $result_rutas = $stmt_rutas->get_result();
    
    $rutas = [];
    
    while ($ruta = $result_rutas->fetch_assoc()) {
        $id_ruta = $ruta['id_ruta'];
        
        // Por cada ruta encontrada, hacer una consulta a la tabla paradas_ruta ordenadas por orden ASC
        $stmt_paradas = $conexion->prepare("SELECT nombre, latitud, longitud FROM paradas_ruta WHERE id_ruta = ? ORDER BY orden ASC");
        $stmt_paradas->bind_param("i", $id_ruta);
        $stmt_paradas->execute();
        $result_paradas = $stmt_paradas->get_result();
        
        $paradas = [];
        while ($parada = $result_paradas->fetch_assoc()) {
            $paradas[] = [
                "nombre" => $parada['nombre'],
                "latitud" => $parada['latitud'],
                "longitud" => $parada['longitud']
            ];
        }
        
        // y agregarlos dentro del objeto de la ruta.
        $ruta['paradas'] = $paradas;
        $rutas[] = $ruta;
    }
    
    // Devolver respuestas HTTP correctas (200 para éxito)
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "rutas" => $rutas
    ]);

} catch (Exception $e) {
    // 500 para errores del servidor
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor: " . $e->getMessage()]);
}
?>
