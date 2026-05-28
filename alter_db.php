<?php
require_once 'config/conexion_bd.php';

$res = $conexion->query("DESCRIBE notificaciones");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

// Add column if not exists
$check = $conexion->query("SHOW COLUMNS FROM notificaciones LIKE 'destinatario_tipo'");
if ($check->num_rows == 0) {
    $conexion->query("ALTER TABLE notificaciones ADD COLUMN destinatario_tipo VARCHAR(20) DEFAULT 'usuarios' AFTER rfc_empresa");
    echo "Columna destinatario_tipo agregada.\n";
}

$check2 = $conexion->query("SHOW COLUMNS FROM notificaciones LIKE 'rfc_checador'");
if ($check2->num_rows == 0) {
    $conexion->query("ALTER TABLE notificaciones ADD COLUMN rfc_checador VARCHAR(13) DEFAULT NULL AFTER id_usuario");
    echo "Columna rfc_checador agregada.\n";
}
?>
