<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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
require_once '../../controllers/insert/upload_foto.php';

try {
    $conn = $conexion;
    if ($conn->connect_error) {
        sendResponse(500, ["error" => "Error de conexión: " . $conn->connect_error]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Los datos deben venir en formato FormData (multipart/form-data)
        $rfc_checador = $_POST['rfc_checador'] ?? '';

        if (empty($rfc_checador)) {
            sendResponse(400, ["error" => "El parámetro 'rfc_checador' es requerido"]);
        }

        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            sendResponse(400, ["error" => "No se recibió ninguna imagen válida en el parámetro 'foto'"]);
        }

        // Subir la foto usando la función existente
        $foto_nombre = uploadFoto($_FILES['foto'], 'checador');

        if (!$foto_nombre) {
            sendResponse(500, ["error" => "Error al procesar o guardar la imagen. Verifique que sea JPG, PNG o WEBP y menor a 2MB."]);
        }

        // Actualizar en la base de datos
        $sql = "UPDATE checadores SET foto = ? WHERE rfc_checador = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            sendResponse(500, ["error" => "Error al preparar la consulta: " . $conn->error]);
        }
        
        $stmt->bind_param("ss", $foto_nombre, $rfc_checador);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Construir URL aproximada para facilitar su uso en la app móvil
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $domain = $_SERVER['HTTP_HOST'];
                $foto_url = "$protocol://$domain/GoWay/assets/images/profiles/$foto_nombre";

                sendResponse(200, [
                    "success" => true,
                    "message" => "Imagen actualizada correctamente",
                    "foto" => $foto_nombre,
                    "foto_url" => $foto_url
                ]);
            } else {
                // Verificar si el checador existe
                $check_sql = "SELECT rfc_checador FROM checadores WHERE rfc_checador = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("s", $rfc_checador);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    sendResponse(200, [
                        "success" => true,
                        "message" => "La imagen fue subida, pero el registro no se modificó (posible mismo archivo)",
                        "foto" => $foto_nombre
                    ]);
                } else {
                    sendResponse(404, ["error" => "No se encontró ningún checador con el RFC proporcionado"]);
                }
            }
        } else {
            sendResponse(500, ["error" => "Error al ejecutar la actualización: " . $stmt->error]);
        }
        
        $stmt->close();
    } else {
        sendResponse(405, ["error" => "Método no permitido. Use POST"]);
    }

} catch (Exception $e) {
    sendResponse(500, ["error" => "Error interno: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
