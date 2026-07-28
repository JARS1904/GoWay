<?php
/**
 * conexion_bd.php — Conexión a Base de Datos con soporte para .env (Hostinger / Producción / Local)
 */

// Función auxiliar interna para cargar .env sin dependencias externas
if (!function_exists('cargarEnvGoWay')) {
    function cargarEnvGoWay($ruta) {
        if (!file_exists($ruta)) return;
        $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (strpos($linea, '#') === 0 || empty($linea)) continue;
            if (strpos($linea, '=') !== false) {
                list($nombre, $valor) = explode('=', $linea, 2);
                $nombre = trim($nombre);
                $valor = trim(trim($valor), '"\'');
                $_ENV[$nombre] = $valor;
                putenv("$nombre=$valor");
            }
        }
    }
}

// Intentar cargar .env desde la raíz del proyecto o un nivel arriba (fuera de public_html en Hostinger)
cargarEnvGoWay(__DIR__ . '/../.env');
cargarEnvGoWay(__DIR__ . '/../../.env');

$db_host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : ($_ENV['DB_HOST'] ?? 'localhost');
$db_user = getenv('DB_USER') !== false ? getenv('DB_USER') : ($_ENV['DB_USER'] ?? 'root');
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');
$db_name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : ($_ENV['DB_NAME'] ?? 'goway');
$db_port = getenv('DB_PORT') !== false ? getenv('DB_PORT') : ($_ENV['DB_PORT'] ?? '3306');

// Suprimir errores de advertencia al intentar conectar en producción
mysqli_report(MYSQLI_REPORT_OFF);
$conexion = @new mysqli($db_host, $db_user, $db_pass, $db_name, (int)$db_port);

// Verificar conexión sin filtrar rutas del servidor ni credenciales en texto plano
if ($conexion->connect_error) {
    error_log("GoWay Error de conexión BD: " . $conexion->connect_error);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
    }
    die(json_encode(["success" => false, "error" => "Error interno del servidor. No se pudo conectar a la base de datos."], JSON_UNESCAPED_UNICODE));
}

// Configurar el juego de caracteres a UTF-8
$conexion->set_charset('utf8');
?>
