-- =============================================================
-- Migración: Soporte de rutas de ida y vuelta
-- Fecha: 2026-03-05
--
-- Estrategia:
--   1. Añadir columna id_ruta_retorno en rutas (auto-referencia)
--   2. Insertar las 3 rutas de regreso con origen/destino invertidos
--      y paradas en orden inverso
--   3. Enlazar cada ruta con su contraparte de regreso
--   4. Insertar horarios de regreso (salida = llegada + 15 min de buffer)
-- =============================================================

START TRANSACTION;

-- -------------------------------------------------------------
-- 1. Agregar columna de autoreferencia en rutas
-- -------------------------------------------------------------
ALTER TABLE `rutas`
  ADD COLUMN `id_ruta_retorno` INT(11) NULL DEFAULT NULL AFTER `activa`;

ALTER TABLE `rutas`
  ADD CONSTRAINT `rutas_retorno_fk`
  FOREIGN KEY (`id_ruta_retorno`) REFERENCES `rutas` (`id_ruta`);

-- -------------------------------------------------------------
-- 2. Insertar rutas de regreso
--    (El AUTO_INCREMENT de rutas está en 19, así que serán 19, 20, 21)
-- -------------------------------------------------------------

-- Regreso de ruta 9: Jalpa - Cunduacán  →  Cunduacán - Jalpa
INSERT INTO `rutas` (`rfc_empresa`, `nombre`, `origen`, `destino`, `paradas`, `activa`, `created_at`) VALUES
('XIC789456ABC7',
 'Cunduacán - Jalpa',
 'Cunduacán',
 'Jalpa de Méndez',
 'Entrada a Cunduacán, Crucero de Cupilco, Centro de Jalpa',
 1,
 NOW());

-- Regreso de ruta 10: Nacajuca - Cunduacán  →  Cunduacán - Nacajuca
INSERT INTO `rutas` (`rfc_empresa`, `nombre`, `origen`, `destino`, `paradas`, `activa`, `created_at`) VALUES
('EFE789123XYZ8',
 'Cunduacán - Nacajuca',
 'Cunduacán',
 'Nacajuca',
 'Entrada a Cunduacán, Crucero de Cupilco, Centro de Nacajuca',
 1,
 NOW());

-- Regreso de ruta 1: Indios Verdes - El Caminero  →  El Caminero - Indios Verdes
INSERT INTO `rutas` (`rfc_empresa`, `nombre`, `origen`, `destino`, `paradas`, `activa`, `created_at`) VALUES
('TUM123456ABC1',
 'Camionero - Indios',
 'El Caminero',
 'Indios Verdes',
 'Villa Basilio, Ricardo Flores Magón, La Raza, Parque del Mestizaje',
 1,
 NOW());

-- -------------------------------------------------------------
-- 3. Enlazar rutas con sus contrapartes de regreso
--    (ambas se apuntan mutuamente para poder navegar en ambos sentidos)
-- -------------------------------------------------------------

-- Ruta 9 (Jalpa→Cunduacán) ↔ Ruta 19 (Cunduacán→Jalpa)
UPDATE `rutas` SET `id_ruta_retorno` = 19 WHERE `id_ruta` = 9;
UPDATE `rutas` SET `id_ruta_retorno` = 9  WHERE `id_ruta` = 19;

-- Ruta 10 (Nacajuca→Cunduacán) ↔ Ruta 20 (Cunduacán→Nacajuca)
UPDATE `rutas` SET `id_ruta_retorno` = 20 WHERE `id_ruta` = 10;
UPDATE `rutas` SET `id_ruta_retorno` = 10 WHERE `id_ruta` = 20;

-- Ruta 1 (Indios→Camionero) ↔ Ruta 21 (Camionero→Indios)
UPDATE `rutas` SET `id_ruta_retorno` = 21 WHERE `id_ruta` = 1;
UPDATE `rutas` SET `id_ruta_retorno` = 1  WHERE `id_ruta` = 21;

-- -------------------------------------------------------------
-- 4. Insertar horarios de regreso
--    Criterio: hora_salida = hora_llegada del viaje de ida + 15 min de buffer
--              hora_llegada = hora_salida + duración del viaje de ida
--    El AUTO_INCREMENT de horarios está en 66.
-- -------------------------------------------------------------

-- ── Ruta 19: Cunduacán → Jalpa (regreso de ruta 9, duración ~45 min) ──
-- Ida lunes  : 06:00 → 06:45  |  Retorno: 07:00 → 07:45
-- Ida martes : 06:15 → 07:00  |  Retorno: 07:15 → 08:00
-- Ida miérc. : 06:30 → 07:15  |  Retorno: 07:30 → 08:15
INSERT INTO `horarios` (`id_ruta`, `dia_semana`, `hora_salida`, `hora_llegada`, `frecuencia`) VALUES
(19, 'Lunes',     '07:00:00', '07:45:00', 'Cada 30 minutos'),
(19, 'Martes',    '07:15:00', '08:00:00', 'Cada 30 minutos'),
(19, 'Miércoles', '07:30:00', '08:15:00', 'Cada 30 minutos');

-- ── Ruta 20: Cunduacán → Nacajuca (regreso de ruta 10, duración ~45 min) ──
-- Ida lunes  : 06:00 → 06:45  |  Retorno: 07:00 → 07:45
-- Ida martes : 06:15 → 07:00  |  Retorno: 07:15 → 08:00
-- Ida miérc. : 06:30 → 07:15  |  Retorno: 07:30 → 08:15
INSERT INTO `horarios` (`id_ruta`, `dia_semana`, `hora_salida`, `hora_llegada`, `frecuencia`) VALUES
(20, 'Lunes',     '07:00:00', '07:45:00', 'Cada 30 minutos'),
(20, 'Martes',    '07:15:00', '08:00:00', 'Cada 30 minutos'),
(20, 'Miércoles', '07:30:00', '08:15:00', 'Cada 30 minutos');

-- ── Ruta 21: El Caminero → Indios Verdes (regreso de ruta 1, duración ~45 min) ──
-- Ida lunes-viernes : 05:00 → 05:45  |  Retorno: 06:00 → 06:45
-- Ida sábado        : 06:00 → 06:45  |  Retorno: 07:00 → 07:45
-- Ida domingo       : 07:00 → 07:45  |  Retorno: 08:00 → 08:45
INSERT INTO `horarios` (`id_ruta`, `dia_semana`, `hora_salida`, `hora_llegada`, `frecuencia`) VALUES
(21, 'Lunes',     '06:00:00', '06:45:00', 'Cada 10 minutos'),
(21, 'Martes',    '06:00:00', '06:45:00', 'Cada 10 minutos'),
(21, 'Miércoles', '06:00:00', '06:45:00', 'Cada 10 minutos'),
(21, 'Jueves',    '06:00:00', '06:45:00', 'Cada 10 minutos'),
(21, 'Viernes',   '06:00:00', '06:45:00', 'Cada 10 minutos'),
(21, 'Sábado',    '07:00:00', '07:45:00', 'Cada 15 minutos'),
(21, 'Domingo',   '08:00:00', '08:45:00', 'Cada 20 minutos');

COMMIT;
