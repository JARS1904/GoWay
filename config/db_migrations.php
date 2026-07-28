<?php
/**
 * db_migrations.php — Script único de migraciones y actualización de estructura de Base de Datos
 * Elimina la necesidad de ejecutar SHOW COLUMNS o ALTER TABLE dentro de cada petición de API en tiempo de ejecución.
 * Ejecutar una sola vez o incluir en scripts de instalación/despliegue.
 */

if (!isset($conexion) || $conexion->connect_error) {
    require_once __DIR__ . '/conexion_bd.php';
}

function verificarYCrearColumna($conn, $tabla, $columna, $definicion) {
    $res = $conn->query("SHOW COLUMNS FROM `$tabla` LIKE '$columna'");
    if ($res && $res->num_rows === 0) {
        $conn->query("ALTER TABLE `$tabla` ADD COLUMN `$columna` $definicion");
    }
}

// 1. Columnas en tabla asignaciones
verificarYCrearColumna($conexion, 'asignaciones', 'hora_checado_real', 'DATETIME NULL DEFAULT NULL AFTER estado');
verificarYCrearColumna($conexion, 'asignaciones', 'checado_por', 'VARCHAR(100) NULL DEFAULT NULL AFTER hora_checado_real');

// 2. Columnas en tabla checadores y conductores para tokens de sesión (API)
verificarYCrearColumna($conexion, 'checadores', 'api_token', 'VARCHAR(128) NULL DEFAULT NULL');
verificarYCrearColumna($conexion, 'conductores', 'api_token', 'VARCHAR(128) NULL DEFAULT NULL');

// 3. Columnas en tabla reportes
verificarYCrearColumna($conexion, 'reportes', 'updated_at', 'DATETIME NULL DEFAULT NULL AFTER created_at');

// 4. Columnas para notificaciones Push de Firebase (fcm_token) en usuarios y checadores
verificarYCrearColumna($conexion, 'usuarios', 'fcm_token', 'VARCHAR(255) NULL DEFAULT NULL');
verificarYCrearColumna($conexion, 'checadores', 'fcm_token', 'VARCHAR(255) NULL DEFAULT NULL');

echo "✅ Migraciones de la base de datos completadas con éxito. Todas las columnas están al día.";
?>
