<?php
/**
 * fcm_helper.php — Helper central para enviar notificaciones Push y alertas silenciosas a Firebase Cloud Messaging (FCM)
 * Soporta autenticación con Service Account (config/firebase_credentials.json).
 */

if (!function_exists('obtenerTokenAccesoFCM')) {
    function obtenerTokenAccesoFCM() {
        $ruta_credenciales = __DIR__ . '/firebase_credentials.json';
        if (!file_exists($ruta_credenciales)) {
            error_log("GoWay FCM Error: No se encontró el archivo firebase_credentials.json en config/");
            return null;
        }

        $credenciales = json_decode(file_get_contents($ruta_credenciales), true);
        if (!$credenciales || !isset($credenciales['private_key'], $credenciales['client_email'])) {
            error_log("GoWay FCM Error: Archivo firebase_credentials.json inválido.");
            return null;
        }

        // Sincronizar reloj exactamente con Google NTP (para evitar rechazos por reloj desfasado +6h en XAMPP Windows)
        $now = time();
        $chHeader = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($chHeader, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chHeader, CURLOPT_HEADER, true);
        curl_setopt($chHeader, CURLOPT_NOBODY, true);
        curl_setopt($chHeader, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chHeader, CURLOPT_SSL_VERIFYHOST, false);
        $resHeader = curl_exec($chHeader);
        curl_close($chHeader);
        if ($resHeader && preg_match('/^date:\s*(.+)$/im', $resHeader, $matches)) {
            $now = strtotime(trim($matches[1]));
        }

        // Crear JWT (Header + Claim + Signature)
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $claim = json_encode([
            'iss' => $credenciales['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now - 10
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlClaim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claim));
        $signatureInput = $base64UrlHeader . "." . $base64UrlClaim;

        $signature = '';
        if (!openssl_sign($signatureInput, $signature, $credenciales['private_key'], 'SHA256')) {
            error_log("GoWay FCM Error: Falló la firma OpenSSL al generar JWT.");
            return null;
        }
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $jwt = $signatureInput . "." . $base64UrlSignature;

        // Solicitar access_token a Google OAuth2
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));
        $respuesta = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($respuesta, true);
        return $data['access_token'] ?? null;
    }
}

if (!function_exists('enviarPushGoWay')) {
    /**
     * Envía una notificación push o mensaje silencioso a un token FCM específico
     * 
     * @param string $fcm_token Token del dispositivo destino
     * @param string $titulo Título visible de la alerta (vacío para push silencioso de solo datos)
     * @param string $mensaje Cuerpo visible de la alerta
     * @param array $datos_extra Datos clave-valor para procesar en Flutter (ej. ['accion' => 'reload_asignaciones'])
     * @return bool True si se envió correctamente, False si hubo error
     */
    function enviarPushGoWay($fcm_token, $titulo = '', $mensaje = '', $datos_extra = []) {
        if (empty($fcm_token)) return false;

        $access_token = obtenerTokenAccesoFCM();
        if (!$access_token) return false;

        $ruta_credenciales = __DIR__ . '/firebase_credentials.json';
        $credenciales = json_decode(file_get_contents($ruta_credenciales), true);
        $project_id = $credenciales['project_id'] ?? null;
        if (!$project_id) return false;

        $url = "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send";

        $messagePayload = [
            'token' => $fcm_token
        ];

        // Si se especificó título/mensaje, adjuntar bloque 'notification'
        if (!empty($titulo) || !empty($mensaje)) {
            $messagePayload['notification'] = [
                'title' => (string)$titulo,
                'body' => (string)$mensaje
            ];
        }

        // Convertir todos los valores de datos_extra a string (FCM v1 requiere strings en data)
        if (!empty($datos_extra) && is_array($datos_extra)) {
            $stringData = [];
            foreach ($datos_extra as $key => $val) {
                $stringData[(string)$key] = is_array($val) || is_object($val) ? json_encode($val) : (string)$val;
            }
            $messagePayload['data'] = $stringData;
        }

        $headers = [
            "Authorization: Bearer {$access_token}",
            "Content-Type: application/json; charset=UTF-8"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $messagePayload]));
        
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code >= 200 && $http_code < 300) {
            return true;
        } else {
            error_log("GoWay FCM Error de envío ($http_code): " . $result);
            return false;
        }
    }
}

if (!function_exists('enviarPushMasivoGoWay')) {
    /**
     * Envía notificaciones a un arreglo de tokens FCM
     * 
     * @param array $lista_tokens Lista de tokens FCM de los dispositivos destino
     * @param string $titulo Título de la alerta
     * @param string $mensaje Mensaje visible
     * @param array $datos_extra Datos clave-valor de control
     */
    function enviarPushMasivoGoWay($lista_tokens, $titulo = '', $mensaje = '', $datos_extra = []) {
        if (empty($lista_tokens) || !is_array($lista_tokens)) return 0;
        
        $exitosos = 0;
        // Para no saturar con cURL en un solo loop enorme, enviamos a cada token único
        $tokens_unicos = array_unique(array_filter($lista_tokens));
        foreach ($tokens_unicos as $token) {
            if (enviarPushGoWay($token, $titulo, $mensaje, $datos_extra)) {
                $exitosos++;
            }
        }
        return $exitosos;
    }
}
?>
