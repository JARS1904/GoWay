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

    if (!isset($data['action'])) {
        http_response_code(400);
        echo json_encode(["error" => "No se proporcionó ninguna acción."]);
        exit;
    }

    switch ($data['action']) {
        case 'get_routes':
            // Lógica existente de get_routes
            if (empty($data['rfc_conductor'])) {
                http_response_code(400);
                echo json_encode(["error" => "El campo rfc_conductor no puede estar vacío."]);
                exit;
            }

            $rfc_conductor = $data['rfc_conductor'];
            
            $stmt_empresa = $conexion->prepare("SELECT rfc_empresa FROM conductores WHERE rfc_conductor = ?");
            $stmt_empresa->bind_param("s", $rfc_conductor);
            $stmt_empresa->execute();
            $result_empresa = $stmt_empresa->get_result();

            if ($result_empresa->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["error" => "Conductor no encontrado o no tiene empresa asociada."]);
                exit;
            }

            $row_empresa = $result_empresa->fetch_assoc();
            $rfc_empresa = $row_empresa['rfc_empresa'];
            
            $stmt_rutas = $conexion->prepare("SELECT id_ruta, nombre, origen, destino FROM rutas WHERE rfc_empresa = ? AND activa = 1");
            $stmt_rutas->bind_param("s", $rfc_empresa);
            $stmt_rutas->execute();
            $result_rutas = $stmt_rutas->get_result();
            
            $rutas = [];
            
            while ($ruta = $result_rutas->fetch_assoc()) {
                $id_ruta = $ruta['id_ruta'];
                
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
                
                $ruta['paradas'] = $paradas;
                $rutas[] = $ruta;
            }
            
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "rutas" => $rutas
            ]);
            break;

        case 'get_schedules':
            if (empty($data['rfc_conductor'])) {
                http_response_code(400);
                echo json_encode(["error" => "Falta rfc_conductor."]);
                exit;
            }
            
            $rfc_conductor = $data['rfc_conductor'];
            
            // Set timezone to ensure correct day
            date_default_timezone_set('America/Mexico_City');
            $day_of_week = date('N'); // 1 (Mon) - 7 (Sun)
            
            if ($day_of_week >= 1 && $day_of_week <= 5) {
                $dia_filtro = 'Lunes a Viernes';
            } else if ($day_of_week == 6) {
                $dia_filtro = 'Sábado';
            } else {
                $dia_filtro = 'Domingo';
            }
            
            // Consultar asignaciones del día filtradas por el día de la semana actual
            $query = "SELECT a.id_asignacion, a.id_ruta, a.id_horario, a.asientos_disp, a.estado, a.fecha,
                             r.nombre as ruta_nombre, h.hora_salida, h.hora_llegada, h.tipo_dia, v.capacidad, v.placa, v.modelo as marca
                      FROM asignaciones a
                      JOIN rutas r ON a.id_ruta = r.id_ruta
                      JOIN horarios h ON a.id_horario = h.id_horario
                      JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo
                      WHERE a.rfc_conductor = ? AND a.activa = 1 AND h.tipo_dia = ?
                      ORDER BY h.hora_salida ASC";
                      
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ss", $rfc_conductor, $dia_filtro);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $horarios = [];
            while ($row = $result->fetch_assoc()) {
                // Obtener paradas para la ruta de este horario
                $stmt_paradas = $conexion->prepare("SELECT nombre, latitud, longitud FROM paradas_ruta WHERE id_ruta = ? ORDER BY orden ASC");
                $stmt_paradas->bind_param("i", $row['id_ruta']);
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
                $row['paradas'] = $paradas;
                $horarios[] = $row;
            }
            
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "horarios" => $horarios
            ]);
            break;

        case 'update_schedule_status':
            if (empty($data['id_asignacion']) || empty($data['estado'])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan parámetros (id_asignacion, estado)."]);
                exit;
            }
            
            $id_asignacion = $data['id_asignacion'];
            $estado = $data['estado']; // programado, en_ruta, completado, cancelado, retrasado
            
            $stmt = $conexion->prepare("UPDATE asignaciones SET estado = ? WHERE id_asignacion = ?");
            $stmt->bind_param("si", $estado, $id_asignacion);
            
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(["success" => true, "message" => "Estado actualizado a $estado"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudo actualizar el estado."]);
            }
            break;

        case 'update_seats':
            if (empty($data['id_asignacion']) || !isset($data['asientos_disp'])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan parámetros (id_asignacion, asientos_disp)."]);
                exit;
            }
            
            $id_asignacion = $data['id_asignacion'];
            $asientos = (int)$data['asientos_disp'];
            
            $stmt = $conexion->prepare("UPDATE asignaciones SET asientos_disp = ? WHERE id_asignacion = ?");
            $stmt->bind_param("ii", $asientos, $id_asignacion);
            
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(["success" => true, "message" => "Asientos actualizados"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudo actualizar los asientos."]);
            }
            break;

        case 'get_schedule_status':
            if (empty($data['id_asignacion'])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan parámetros (id_asignacion)."]);
                exit;
            }
            
            $id_asignacion = $data['id_asignacion'];
            
            $stmt = $conexion->prepare("SELECT estado FROM asignaciones WHERE id_asignacion = ?");
            $stmt->bind_param("i", $id_asignacion);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                http_response_code(200);
                echo json_encode(["success" => true, "estado" => $row['estado']]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Asignación no encontrada."]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(["error" => "Acción no reconocida."]);
            break;
    }

} catch (Exception $e) {
    // 500 para errores del servidor
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor: " . $e->getMessage()]);
}
?>
