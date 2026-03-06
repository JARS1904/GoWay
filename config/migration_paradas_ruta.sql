-- =============================================================
-- Migración: Tabla de paradas estructuradas por ruta
-- Fecha: 2026-03-06
--
-- Problema resuelto:
--   Las paradas se almacenaban como texto libre (campo `paradas`
--   en la tabla rutas). Esto impide:
--     1. Calcular tiempos de llegada por parada.
--     2. Buscar rutas parciales (usuario sube en una parada
--        intermedia, no necesariamente en el origen de la ruta).
--
-- Estrategia:
--   1. Crear tabla `paradas_ruta` con orden y tiempo estimado.
--   2. La búsqueda de rutas se extiende: si el origen o destino
--      indicado coincide con una parada registrada, se muestra
--      la ruta con los tiempos ajustados a ese tramo.
--
-- Cómo agregar paradas a una ruta existente:
--   Incluir SIEMPRE la parada de inicio (orden=0, minutos=0)
--   y la de fin (orden=N, minutos=tiempo_total_ruta).
--   Las paradas intermedias van entre ellas con su orden y
--   tiempo estimado en minutos desde el punto de partida.
--
-- Ejemplo para ruta 9 (Jalpa → Cunduacán, ~45 min):
--   INSERT INTO paradas_ruta (id_ruta, nombre, orden, minutos_desde_origen) VALUES
--     (9, 'Jalpa de Méndez',      0, 0),
--     (9, 'Cupilco',             1, 15),
--     (9, 'Crucero de Cunduacán',2, 30),
--     (9, 'Cunduacán',           3, 45);
-- =============================================================

CREATE TABLE IF NOT EXISTS `paradas_ruta` (
  `id_parada`              INT(11)      NOT NULL AUTO_INCREMENT,
  `id_ruta`                INT(11)      NOT NULL,
  `nombre`                 VARCHAR(255) NOT NULL,
  `orden`                  INT(11)      NOT NULL DEFAULT 0,
  `minutos_desde_origen`   INT(11)      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_parada`),
  UNIQUE KEY  `uk_ruta_orden`   (`id_ruta`, `orden`),
  KEY         `idx_ruta_parada` (`id_ruta`),
  CONSTRAINT  `fk_parada_ruta`
    FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
