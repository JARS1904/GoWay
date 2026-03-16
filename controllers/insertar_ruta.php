<?php
// insertar_ruta.php
header('Content-Type: application/json');
require_once '../config/conexion_bd.php';

// Crear conexión
$conn = $conexion;

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Recoger datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$origen = isset($_POST['origen']) ? trim($_POST['origen']) : '';
$destino = isset($_POST['destino']) ? trim($_POST['destino']) : '';
$id_ruta_retorno = isset($_POST['id_ruta_retorno']) && $_POST['id_ruta_retorno'] !== '' ? (int)$_POST['id_ruta_retorno'] : null;
$rfc_empresa = isset($_POST['rfc_empresa']) ? trim($_POST['rfc_empresa']) : '';

// Validar datos
if (empty($nombre) || empty($origen) || empty($destino) || empty($rfc_empresa)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos requeridos']);
    exit;
}

// Preparar la consulta SQL (añadiendo rfc_empresa e id_ruta_retorno)
$sql = "INSERT INTO rutas (rfc_empresa, nombre, origen, destino, id_ruta_retorno)
        VALUES (?, ?, ?, ?, ?)";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit;
}

// Vincular parámetros (usando 'i' para el int nullable)
$stmt->bind_param("ssssi", $rfc_empresa, $nombre, $origen, $destino, $id_ruta_retorno);

// Ejecutar consulta
if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Ruta agregada exitosamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al insertar: ' . $stmt->error]);
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>