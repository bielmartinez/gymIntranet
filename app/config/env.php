<?php
/**
 * Carga variables de entorno desde el archivo .env
 */
function loadEnvVariables() {
    $envFile = dirname(__DIR__) . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignorar comentarios
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Separar clave=valor
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Establecer variable de entorno
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Cargar variables de entorno
loadEnvVariables();
?>