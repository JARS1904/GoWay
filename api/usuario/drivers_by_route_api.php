<?php
// /api/usuario/drivers_by_route_api.php
require_once '../../config/conexion_bd.php';
header('Content-Type: application/json');

if (!isset($_GET['id_ruta'])) {
    echo json_encode([]);
    exit;
}

$id_ruta = intval($_GET['id_ruta']);

// Obtener los conductores que tienen una asignación activa (estado='en_ruta' o activa=1, dependiendo de la regla de negocio)
// Asignaciones usa 'activa' y también 'estado'. Nos guiaremos por 'activa = 1'.
$sql = "SELECT DISTINCT rfc_conductor FROM asignaciones WHERE id_ruta = ? AND activa = 1 AND rfc_conductor IS NOT NULL";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_ruta);
$stmt->execute();
$result = $stmt->get_result();

$conductores = [];
while ($row = $result->fetch_assoc()) {
    $conductores[] = $row['rfc_conductor'];
}

echo json_encode($conductores);
