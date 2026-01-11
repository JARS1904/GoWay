-- Tabla para almacenar rutas favoritas de usuarios
CREATE TABLE IF NOT EXISTS rutas_favoritas (
    id_favorita INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_ruta INT NOT NULL,
    fecha_agregada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorita (id_usuario, id_ruta),
    KEY idx_usuario_favoritas (id_usuario),
    KEY idx_ruta_favoritas (id_ruta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
