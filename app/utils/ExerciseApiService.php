<?php
/**
 * Servicio para gestionar la comunicación con APIs de ejercicios
 * Actualmente soporta API Ninjas (https://api-ninjas.com/api/exercises)
 */
class ExerciseApiService {
    private $apiKey;
    private $baseUrl;
    private $lastError = null;
    private $lastHttpCode = 200;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Obtener la API key desde el archivo de configuración o variables de entorno
        $this->apiKey = getenv('API_NINJAS_KEY') ?: 'sX/y+NBDrmBqMS3Crt8x/Q==ffda6PsfKGzOkZS3'; 
        $this->baseUrl = 'https://api.api-ninjas.com/v1/exercises';
    }
    
    /**
     * Busca ejercicios por grupo muscular
     * 
     * @param string $muscle Grupo muscular (abs, biceps, chest, glutes, etc.)
     * @return array Array de ejercicios o array vacío en caso de error
     */
    public function searchByMuscle($muscle) {
        $url = $this->baseUrl . '?muscle=' . urlencode($muscle);
        return $this->makeRequest($url);
    }
    
    /**
     * Busca ejercicios por tipo
     * 
     * @param string $type Tipo de ejercicio (cardio, olympic_weightlifting, plyometrics, etc.)
     * @return array Array de ejercicios o array vacío en caso de error
     */
    public function searchByType($type) {
        $url = $this->baseUrl . '?type=' . urlencode($type);
        return $this->makeRequest($url);
    }
    
    /**
     * Busca ejercicios por nombre
     * 
     * @param string $name Nombre o parte del nombre del ejercicio
     * @return array Array de ejercicios o array vacío en caso de error
     */
    public function searchByName($name) {
        $url = $this->baseUrl . '?name=' . urlencode($name);
        return $this->makeRequest($url);
    }
    
    /**
     * Busca ejercicios con múltiples criterios
     * 
     * @param array $params Parámetros de búsqueda (muscle, type, name, difficulty)
     * @return array Array de ejercicios o array vacío en caso de error
     */
    public function advancedSearch($params) {
        $queryParams = [];
        
        // Añadir cada parámetro si está presente
        foreach (['muscle', 'type', 'name', 'difficulty'] as $param) {
            if (isset($params[$param]) && !empty($params[$param])) {
                $queryParams[] = $param . '=' . urlencode($params[$param]);
            }
        }
        
        $url = $this->baseUrl . '?' . implode('&', $queryParams);
        return $this->makeRequest($url);
    }
    
    /**
     * Realiza la petición a la API
     * 
     * @param string $url URL completa para la petición
     * @return array Respuesta decodificada o array vacío en caso de error
     */
    private function makeRequest($url) {
        // Reiniciar mensajes de error previos
        $this->lastError = null;
        $this->lastHttpCode = 200;
        
        // Inicializar cURL
        $ch = curl_init();
        
        if (!$ch) {
            $this->lastError = "No se pudo inicializar cURL";
            $this->logError();
            return [];
        }
        
        // Configurar opciones de cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Api-Key: ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        // Añadir timeout para evitar esperas infinitas
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para entornos sin certificados SSL válidos
        
        // Ejecutar la petición
        $response = curl_exec($ch);
        $this->lastError = curl_error($ch);
        $this->lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        // Registrar la respuesta para depuración
        if (class_exists('Logger')) {
            Logger::log('DEBUG', 'API URL: ' . $url);
            Logger::log('DEBUG', 'API Response Code: ' . $this->lastHttpCode);
            if ($response !== false) {
                Logger::log('DEBUG', 'API Response (truncated): ' . substr($response, 0, 200));
            } else {
                Logger::log('DEBUG', 'API Response: Failed to get response');
            }
        }
        
        // Si no hay error y el código HTTP es 200, procesar la respuesta
        if (empty($this->lastError) && $this->lastHttpCode == 200 && !empty($response)) {
            try {
                $decoded = json_decode($response, true);
                
                // Verificar si json_decode tuvo éxito
                if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                    $this->lastError = 'Error al decodificar JSON: ' . json_last_error_msg();
                    $this->logError();
                    return [];
                }
                
                return $decoded;
            } catch (Exception $e) {
                $this->lastError = 'Excepción al procesar respuesta: ' . $e->getMessage();
                $this->logError();
                return [];
            }
        } else {
            // Generar mensaje de error detallado
            if (empty($this->lastError)) {
                $this->lastError = 'Error HTTP: ' . $this->lastHttpCode;
                
                // Si tenemos una respuesta, intentar extraer más detalles del error
                if (!empty($response)) {
                    try {
                        $errorData = json_decode($response, true);
                        if (json_last_error() === JSON_ERROR_NONE && isset($errorData['error'])) {
                            $this->lastError .= ' - ' . $errorData['error'];
                        } else {
                            // Si la respuesta no es JSON válido, incluir parte de la respuesta
                            $this->lastError .= ' - Respuesta: ' . substr($response, 0, 100) . 
                                (strlen($response) > 100 ? '...' : '');
                        }
                    } catch (Exception $e) {
                        $this->lastError .= ' - No se pudo decodificar respuesta';
                    }
                }
            }
            
            $this->logError();
            return [];
        }
    }
    
    /**
     * Registra un error en el log
     */
    private function logError() {
        if (class_exists('Logger')) {
            Logger::log('ERROR', 'Error en ExerciseApiService: ' . $this->lastError);
        }
    }
    
    /**
     * Devuelve el último mensaje de error
     * 
     * @return string|null El último mensaje de error o null si no hay errores
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Devuelve el último código HTTP recibido
     * 
     * @return int El último código HTTP recibido
     */
    public function getLastHttpCode() {
        return $this->lastHttpCode;
    }
    
    /**
     * Obtener grupos musculares disponibles en la API
     * 
     * @return array Array con los grupos musculares disponibles
     */
    public function getAvailableMuscleGroups() {
        return [
            'abdominals' => 'Abdominales',
            'abductors' => 'Abductores',
            'adductors' => 'Aductores',
            'biceps' => 'Bíceps',
            'calves' => 'Pantorrillas',
            'chest' => 'Pecho',
            'forearms' => 'Antebrazos',
            'glutes' => 'Glúteos',
            'hamstrings' => 'Isquiotibiales',
            'lats' => 'Dorsales',
            'lower_back' => 'Lumbar',
            'middle_back' => 'Espalda media',
            'neck' => 'Cuello',
            'quadriceps' => 'Cuádriceps',
            'traps' => 'Trapecio',
            'triceps' => 'Tríceps'
        ];
    }
    
    /**
     * Obtener tipos de ejercicios disponibles en la API
     * 
     * @return array Array con los tipos de ejercicios disponibles
     */
    public function getAvailableTypes() {
        return [
            'cardio' => 'Cardio',
            'olympic_weightlifting' => 'Levantamiento olímpico',
            'plyometrics' => 'Pliometría',
            'powerlifting' => 'Powerlifting',
            'strength' => 'Fuerza',
            'stretching' => 'Estiramiento',
            'strongman' => 'Strongman'
        ];
    }
    
    /**
     * Obtener niveles de dificultad disponibles en la API
     * 
     * @return array Array con los niveles de dificultad disponibles
     */
    public function getAvailableDifficulties() {
        return [
            'beginner' => 'Principiante',
            'intermediate' => 'Intermedio',
            'expert' => 'Avanzado'
        ];
    }
}