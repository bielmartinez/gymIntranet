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
    
    /**
     * Busca ejercicios por nombre o grupo muscular para la interfaz de búsqueda
     * 
     * @param string $searchTerm Término de búsqueda para el nombre del ejercicio
     * @param string $muscle Grupo muscular a filtrar (un solo grupo)
     * @return array Array de objetos de ejercicios formateados para la interfaz
     */
    public function searchExercises($searchTerm = '', $muscle = '') {
        $results = [];
        
        // Si tenemos un término de búsqueda, buscar por nombre
        if (!empty($searchTerm)) {
            $nameResults = $this->searchByName($searchTerm);
            $results = array_merge($results, $nameResults);
        }
        
        // Si tenemos un grupo muscular, buscar por ese músculo
        if (!empty($muscle)) {
            // Mapear el nombre en español al valor en inglés que espera la API
            $mappedMuscle = $this->mapSpanishToEnglishMuscle($muscle);
            if (!empty($mappedMuscle)) {
                $muscleResults = $this->searchByMuscle($mappedMuscle);
                $results = array_merge($results, $muscleResults);
            }
        }
        
        // Eliminar duplicados basados en el nombre del ejercicio
        $uniqueResults = [];
        $seenNames = [];
        
        foreach ($results as $exercise) {
            if (!in_array($exercise['name'], $seenNames)) {
                $seenNames[] = $exercise['name'];
                
                // Convertir a objeto para la interfaz
                $exerciseObj = new stdClass();
                $exerciseObj->name = $exercise['name'];
                $exerciseObj->description = $exercise['instructions'] ?? '';
                $exerciseObj->muscles = $exercise['muscle'] ?? '';
                
                $uniqueResults[] = $exerciseObj;
            }
        }
        
        return $uniqueResults;
    }
    
    /**
     * Búsqueda avanzada que combina múltiples criterios incluyendo tipo de ejercicio
     * 
     * @param string $searchTerm Término de búsqueda para el nombre del ejercicio
     * @param array $muscles Lista de grupos musculares a filtrar
     * @param array $types Lista de tipos de ejercicio a filtrar
     * @return array Array de objetos de ejercicios formateados para la interfaz
     */
    public function advancedSearchExercises($searchTerm = '', $muscles = [], $types = []) {
        $results = [];
        
        // Si tenemos un término de búsqueda, buscar por nombre
        if (!empty($searchTerm)) {
            $nameResults = $this->searchByName($searchTerm);
            $results = array_merge($results, $nameResults);
        }
        
        // Si tenemos grupos musculares, buscar por cada músculo
        if (!empty($muscles)) {
            foreach ($muscles as $muscle) {
                // Mapear los nombres en español a los valores en inglés que espera la API
                $mappedMuscle = $this->mapSpanishToEnglishMuscle($muscle);
                if (!empty($mappedMuscle)) {
                    $muscleResults = $this->searchByMuscle($mappedMuscle);
                    $results = array_merge($results, $muscleResults);
                }
            }
        }
        
        // Si tenemos tipos de ejercicio, buscar por cada tipo
        if (!empty($types)) {
            foreach ($types as $type) {
                $typeResults = $this->searchByType($type);
                $results = array_merge($results, $typeResults);
            }
        }
        
        // Eliminar duplicados y formatear resultados
        return $this->formatResults($results);
    }
    
    /**
     * Formatea los resultados eliminando duplicados y convirtiendo a objetos
     * 
     * @param array $results Array de resultados de la API
     * @return array Array de objetos formateados
     */
    private function formatResults($results) {
        // Eliminar duplicados basados en el nombre del ejercicio
        $uniqueResults = [];
        $seenNames = [];
        
        foreach ($results as $exercise) {
            if (!in_array($exercise['name'], $seenNames)) {
                $seenNames[] = $exercise['name'];
                
                // Convertir a objeto para la interfaz
                $exerciseObj = new stdClass();
                $exerciseObj->name = $exercise['name'];
                $exerciseObj->description = $exercise['instructions'] ?? '';
                $exerciseObj->muscles = $exercise['muscle'] ?? '';
                $exerciseObj->type = $exercise['type'] ?? '';
                $exerciseObj->equipment = $exercise['equipment'] ?? '';
                $exerciseObj->difficulty = $exercise['difficulty'] ?? '';
                
                // En un caso real, aquí podrías asignar una URL de imagen basada en el ejercicio
                // Por ahora usaremos imágenes de placeholder específicas según tipo de ejercicio
                $exerciseObj->image_url = $this->getExerciseImageUrl($exercise);
                
                $uniqueResults[] = $exerciseObj;
            }
        }
        
        return $uniqueResults;
    }
    
    /**
     * Genera URLs de imágenes de ejemplo basadas en el tipo y grupo muscular del ejercicio
     * 
     * @param array $exercise Datos del ejercicio
     * @return string URL de la imagen
     */
    private function getExerciseImageUrl($exercise) {
        // Este método simula asignar imágenes a los ejercicios basadas en su grupo muscular o tipo
        // En una implementación real, esto podría extraerse de una base de datos o API externa
        
        $placeholderImages = [
            'abdominals' => 'https://images.pexels.com/photos/3775566/pexels-photo-3775566.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
            'biceps' => 'https://images.pexels.com/photos/3837781/pexels-photo-3837781.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
            'chest' => 'https://images.unsplash.com/photo-1534438097545-a2c22c57f2ad?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
            'quadriceps' => 'https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1349&q=80',
            'cardio' => 'https://images.unsplash.com/photo-1538805060514-97d9cc17730c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1267&q=80',
            'strength' => 'https://images.unsplash.com/photo-1574680096145-d05b474e2155?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
            'stretching' => 'https://images.unsplash.com/photo-1575052814086-f385e2e2ad1b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1355&q=80'
        ];
        
        $defaultImage = 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80';
        
        // Intentar asignar una imagen según el grupo muscular
        if (isset($exercise['muscle']) && isset($placeholderImages[$exercise['muscle']])) {
            return $placeholderImages[$exercise['muscle']];
        } 
        // Si no, intentar según el tipo de ejercicio
        elseif (isset($exercise['type']) && isset($placeholderImages[$exercise['type']])) {
            return $placeholderImages[$exercise['type']];
        }
        // Si todo falla, usar imagen por defecto
        else {
            return $defaultImage;
        }
    }
    
    /**
     * Mapea nombres de músculos en español a los valores en inglés que espera la API
     * 
     * @param string $spanishMuscle Nombre del músculo en español
     * @return string Nombre del músculo en inglés para la API
     */
    private function mapSpanishToEnglishMuscle($spanishMuscle) {
        $mapping = [
            'abdomen' => 'abdominals',
            'pecho' => 'chest',
            'espalda' => 'middle_back',
            'hombros' => 'traps',
            'brazos' => 'biceps', // Simplificado, podría ser biceps o triceps
            'piernas' => 'quadriceps', // Simplificado, podría ser varios grupos
            'gluteos' => 'glutes'
        ];
        
        return $mapping[strtolower($spanishMuscle)] ?? '';
    }
}