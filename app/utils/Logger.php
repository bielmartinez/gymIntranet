<?php
/**
 * Logger para ProvaGym
 * Este archivo maneja el registro de logs para ayudar en la depuración
 */

class Logger {
    private static $logFile = 'app_log.txt';
    private static $logPath;

    /**
     * Inicializa el logger
     */
    public static function init() {
        // Establecer la ruta del archivo de log
        self::$logPath = dirname(__FILE__) . '/' . self::$logFile;
        
        // Verificar si podemos escribir en el archivo
        $canWrite = false;
        
        if (file_exists(self::$logPath)) {
            $canWrite = is_writable(self::$logPath);
        } else {
            $canWrite = is_writable(dirname(__FILE__));
        }
        
        // Escribir mensaje inicial
        if ($canWrite) {
            $message = "\n\n" . str_repeat('-', 50) . "\n";
            $message .= "Log iniciado: " . date('Y-m-d H:i:s') . "\n";
            $message .= str_repeat('-', 50) . "\n";
            
            file_put_contents(self::$logPath, $message, FILE_APPEND);
            self::log('INFO', 'Logger inicializado correctamente');
        } else {
            // No se puede escribir el archivo de log
            echo "Error: No se puede escribir en el archivo de log. Revisa los permisos.";
        }
    }
    
    /**
     * Registra un mensaje en el archivo de log
     * 
     * @param string $level Nivel del log (INFO, WARNING, ERROR, DEBUG)
     * @param string $message Mensaje a registrar
     * @param array $context Datos adicionales opcionales
     */
    public static function log($level, $message, $context = []) {
        if (!file_exists(self::$logPath)) {
            self::init();
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message";
        
        if (!empty($context)) {
            $logMessage .= " - " . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        $logMessage .= "\n";
        
        file_put_contents(self::$logPath, $logMessage, FILE_APPEND);
    }
    
    /**
     * Registra información sobre la petición actual
     */
    public static function logRequest() {
        $request = [
            'uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
            'referrer' => $_SERVER['HTTP_REFERER'] ?? 'N/A'
        ];
        
        self::log('INFO', 'Nueva solicitud recibida', $request);
    }
    
    /**
     * Registra variables específicas para depuración
     * 
     * @param mixed $data Los datos a registrar
     * @param string $label Etiqueta opcional para identificar los datos
     */
    public static function debug($data, $label = 'DEBUG') {
        if (is_array($data) || is_object($data)) {
            $output = print_r($data, true);
        } else {
            $output = (string) $data;
        }
        
        self::log('DEBUG', $label, ['data' => $output]);
    }
    
    /**
     * Registra un error
     * 
     * @param string $message Mensaje de error
     * @param Exception $exception Excepción opcional
     */
    public static function error($message, $exception = null) {
        $context = [];
        
        if ($exception !== null) {
            $context = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }
        
        self::log('ERROR', $message, $context);
    }
    
    /**
     * Limpia el archivo de log si excede un cierto tamaño
     * 
     * @param int $maxSize Tamaño máximo en bytes (por defecto 5MB)
     */
    public static function clean($maxSize = 5242880) {
        if (file_exists(self::$logPath) && filesize(self::$logPath) > $maxSize) {
            // Crear archivo de backup
            $backupFile = self::$logPath . '.' . date('Y-m-d-H-i-s') . '.bak';
            copy(self::$logPath, $backupFile);
            
            // Reiniciar el archivo de log
            file_put_contents(self::$logPath, "Log limpiado: " . date('Y-m-d H:i:s') . "\n");
            
            self::log('INFO', 'Archivo de log limpiado. Backup creado: ' . basename($backupFile));
        }
    }
}

// Inicializar el logger
Logger::init();
