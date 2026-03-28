<?php
require_once 'c:/xampp/htdocs/GoWay/config/conexion_bd.php';

$sql = "CREATE TABLE IF NOT EXISTS notificaciones (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT DEFAULT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo VARCHAR(50) DEFAULT 'general',
    leido TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);";

if ($conexion->query($sql) === TRUE) {
    echo "Tabla 'notificaciones' creada exitosamente.\n";
} else {
    echo "Error creando la tabla: " . $conexion->error . "\n";
}
$conexion->close();
?>
