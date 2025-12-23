<?php
// actualizar_ruta.php

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goway";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Recoger datos del formulario
$id_ruta = isset($_POST['id_ruta']) ? (int)$_POST['id_ruta'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$origen = isset($_POST['origen']) ? trim($_POST['origen']) : '';
$destino = isset($_POST['destino']) ? trim($_POST['destino']) : '';
$paradas = isset($_POST['paradas']) ? trim($_POST['paradas']) : '';
$activa = isset($_POST['activa']) ? (int)$_POST['activa'] : 0;
$rfc_empresa = isset($_POST['rfc_empresa']) ? trim($_POST['rfc_empresa']) : '';

// Validar datos
if (empty($id_ruta) || empty($nombre) || empty($origen) || empty($destino) || empty($rfc_empresa)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos requeridos']);
    exit;
}

// Preparar la consulta SQL
$sql = "UPDATE rutas SET 
        rfc_empresa = ?,
        nombre = ?,
        origen = ?,
        destino = ?,
        paradas = ?,
        activa = ?
        WHERE id_ruta = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la preparación: ' . $conn->error]);
    exit;
}

// Vincular parámetros
$stmt->bind_param("sssssii", $rfc_empresa, $nombre, $origen, $destino, $paradas, $activa, $id_ruta);

// Ejecutar consulta
if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Ruta actualizada exitosamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error]);
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>
