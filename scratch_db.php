<?php
require_once 'config/conexion_bd.php';
$sql = "ALTER TABLE empresas ADD COLUMN password VARCHAR(255) NULL AFTER email";
if ($conexion->query($sql) === TRUE) {
    echo "Columna password agregada exitosamente.\n";
    
    // Add default password to existing companies (e.g. '123456')
    $default_hash = password_hash('123456', PASSWORD_DEFAULT);
    $conexion->query("UPDATE empresas SET password = '$default_hash' WHERE password IS NULL");
    echo "Contraseñas por defecto asignadas.\n";
} else {
    echo "Error: " . $conexion->error;
}
?>
