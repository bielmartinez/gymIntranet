<?php
/**
 * Servicio para gestionar la comunicación con APIs de ejercicios
 * Actualmente soporta API Ninjas (https://api-ninjas.com/api/exercises)
 */
class ExerciseApiService {
    private $apiKey;
    private $rapidApiKey;
    private $baseUrl;
    private $lastError = null;
    private $lastHttpCode = 200;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Obtener la API key desde el archivo de configuración o variables de entorno
        $this->apiKey = getenv('API_NINJAS_KEY') ?: 'sX/y+NBDrmBqMS3Crt8x/Q==ffda6PsfKGzOkZS3'; 
        // Clave API específica para RapidAPI ExerciseDB
        $this->rapidApiKey = getenv('RAPID_API_KEY') ?: '7bfb89b35fmshb26219938180c66p18b403jsnf0f4866bd5e7';
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
    
    /**
     * Busca ejercicios en la API externa
     * 
     * @param string $query Término de búsqueda (opcional)
     * @param array $filters Filtros adicionales como 'muscle', 'type', 'difficulty', etc.
     * @return array Array de ejercicios encontrados
     */
    public function searchExercises($query = '', $filters = []) {
        // Construir la URL base
        $url = $this->baseUrl;
        
        // Añadir parámetros de consulta
        $queryParams = [];
        
        // Añadir el término de búsqueda si existe
        if (!empty($query)) {
            $queryParams[] = 'name=' . urlencode($query);
        }
        
        // Traducir el grupo muscular si está presente
        if (!empty($filters['muscle'])) {
            $translatedMuscle = $this->mapSpanishToEnglishMuscle($filters['muscle']);
            if (!empty($translatedMuscle)) {
                $filters['muscle'] = $translatedMuscle;
            } else {
                // Si no pudimos traducir el músculo, lo registramos
                if (class_exists('Logger')) {
                    Logger::log('WARNING', "No se pudo traducir el grupo muscular: {$filters['muscle']}");
                }
            }
        }
        
        // Añadir filtros si existen
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $queryParams[] = $key . '=' . urlencode($value);
            }
        }
        
        // Añadir los parámetros a la URL
        if (!empty($queryParams)) {
            $url .= '?' . implode('&', $queryParams);
        }
        
        // Registrar la búsqueda para depuración
        if (class_exists('Logger')) {
            Logger::log('INFO', "Realizando búsqueda de ejercicios con API Ninjas - URL: $url");
        }
        
        // Realizar la petición a la API
        return $this->makeRequest($url);
    }
    
    /**
     * Mapea nombres de músculos en español a sus equivalentes en inglés para la API
     * 
     * @param string $spanishMuscle Nombre del músculo en español
     * @return string Nombre del músculo en inglés
     */    private function mapSpanishToEnglishMuscle($spanishMuscle) {
        Logger::log('DEBUG', "Mapeando grupo muscular: $spanishMuscle");
        
        // Normalizar el término (minúsculas y sin acentos)
        $normalizedMuscle = $this->normalizeString($spanishMuscle);
        
        $muscleMap = [
            // Mapeo completo de grupos musculares español -> inglés según valores exactos de API Ninjas
            // Abdominals
            'abdominales' => 'abdominals',
            'abdomen' => 'abdominals',
            'abdominal' => 'abdominals',
            'abs' => 'abdominals',
            
            // Abductors
            'abductores' => 'abductors',
            'abductor' => 'abductors',
            
            // Adductors
            'adductors' => 'adductors',
            'aductores' => 'adductors',
            'aductor' => 'adductors',
            
            // Biceps
            'biceps' => 'biceps',
            'bicep' => 'biceps',
            
            // Triceps
            'triceps' => 'triceps',
            'tricep' => 'triceps',
            
            // Chest
            'pecho' => 'chest',
            'pectorales' => 'chest',
            'pectoral' => 'chest',
            'chest' => 'chest',
            
            // Middle Back
            'espalda' => 'middle_back',
            'back' => 'middle_back',
            'espalda media' => 'middle_back',
            'dorsal medio' => 'middle_back',
            'middle_back' => 'middle_back',
            
            // Lats
            'dorsal' => 'lats',
            'dorsales' => 'lats',
            'lats' => 'lats',
            'espalda alta' => 'lats',
            
            // Shoulders (Traps)
            'hombros' => 'traps',
            'hombro' => 'traps',
            'deltoides' => 'traps',
            'deltoide' => 'traps',
            'shoulders' => 'traps',
            
            // Quadriceps
            'piernas' => 'quadriceps',
            'pierna' => 'quadriceps',
            'cuadriceps' => 'quadriceps',
            'quad' => 'quadriceps',
            'quads' => 'quadriceps',
            'quadriceps' => 'quadriceps',
            
            // Glutes
            'gluteos' => 'glutes',
            'gluteo' => 'glutes',
            'glutes' => 'glutes',
            
            // Calves
            'pantorrillas' => 'calves',
            'pantorrilla' => 'calves',
            'gemelos' => 'calves',
            'calves' => 'calves',
            
            // Forearms
            'antebrazos' => 'forearms',
            'antebrazo' => 'forearms',
            'forearms' => 'forearms',
            
            // Traps
            'trapecio' => 'traps',
            'trapecios' => 'traps',
            'traps' => 'traps',
            
            // Hamstrings
            'isquiotibiales' => 'hamstrings',
            'isquios' => 'hamstrings',
            'femoral' => 'hamstrings',
            'femorales' => 'hamstrings',
            'hamstrings' => 'hamstrings',
            
            // Neck
            'cuello' => 'neck',
            'neck' => 'neck',
            
            // Lower Back
            'lower_back' => 'lower_back',
            'lower back' => 'lower_back',
            'lumbar' => 'lower_back',
            'lumbares' => 'lower_back',
            'columna' => 'spine',
            'tronco' => 'spine',
            
            'cuerpo completo' => 'full body',
            'full body' => 'full body',
            'todo el cuerpo' => 'full body',
            
            'brazos' => 'upper arms',
            'brazo' => 'upper arms',
            'upper arms' => 'upper arms',
            
            'lower legs' => 'lower legs',
            'piernas inferiores' => 'lower legs',
        ];
        
        if (isset($muscleMap[$normalizedMuscle])) {
            $englishMuscle = $muscleMap[$normalizedMuscle];
            Logger::log('DEBUG', "Músculo mapeado: $normalizedMuscle -> $englishMuscle");
            return $englishMuscle;
        }
        
        // Si no hay coincidencia exacta, buscar coincidencias parciales
        foreach ($muscleMap as $spanish => $english) {
            if (strpos($normalizedMuscle, $spanish) !== false) {
                Logger::log('DEBUG', "Coincidencia parcial encontrada: $normalizedMuscle contiene $spanish -> $english");
                return $english;
            }
        }
        
        Logger::log('WARNING', "No se pudo mapear el grupo muscular: $spanishMuscle");
        return '';
    }
    
    /**
     * Normaliza una cadena (minúsculas, sin acentos)
     * 
     * @param string $string Cadena a normalizar
     * @return string Cadena normalizada
     */
    private function normalizeString($string) {
        $string = mb_strtolower($string, 'UTF-8');
        $string = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'], 
            ['a', 'e', 'i', 'o', 'u', 'u', 'n'], 
            $string
        );
        return trim($string);
    }
    
    /**
     * Devuelve un array con todos los grupos musculares soportados por la API
     * 
     * @return array Array con los grupos musculares soportados
     */
    public function getMuscleGroups() {
        return [
            'abdominals',
            'abductors',
            'adductors',
            'biceps',
            'calves',
            'chest',
            'forearms',
            'glutes',
            'hamstrings',
            'lats',
            'lower_back',
            'middle_back',
            'neck',
            'quadriceps',
            'traps',
            'triceps'
        ];
    }
}