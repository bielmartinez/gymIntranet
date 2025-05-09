<?php
/**
 * Modelo para gestionar el seguimiento físico de los usuarios
 */
require_once dirname(__FILE__) . '/BaseModel.php';

class PhysicalTracking extends BaseModel {
    protected $table = 'seguiment_fisic';
    protected $primaryKey = 'seguiment_id';
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Validar los datos de entrada para el seguimiento físico
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        $errors = [];
        
        // Validar campos requeridos
        if (empty($data['usuari_id'])) {
            $errors['usuari_id'] = 'El ID del usuario es requerido';
        }
        
        // Validar peso
        if (empty($data['pes'])) {
            $errors['pes'] = 'El peso es requerido';
        } else if (!is_numeric($data['pes']) || $data['pes'] <= 0) {
            $errors['pes'] = 'El peso debe ser un número positivo';
        }
        
        // Validar altura
        if (empty($data['alcada'])) {
            $errors['alcada'] = 'La altura es requerida';
        } else if (!is_numeric($data['alcada']) || $data['alcada'] <= 0) {
            $errors['alcada'] = 'La altura debe ser un número positivo (en centímetros)';
        }
        
        // Opcional: validar fecha si está presente
        if (!empty($data['data_mesura'])) {
            $format = 'Y-m-d';
            $d = DateTime::createFromFormat($format, $data['data_mesura']);
            
            if (!($d && $d->format($format) === $data['data_mesura'])) {
                $errors['data_mesura'] = 'El formato de fecha no es válido (YYYY-MM-DD)';
            }
        }
        
        return $errors;
    }

    /**
     * Obtener todas las mediciones de un usuario
     * @param int $userId ID del usuario
     * @return array Mediciones del usuario
     */
    public function getUserMeasurements($userId) {
        $this->db->query('
            SELECT * 
            FROM seguiment_fisic 
            WHERE usuari_id = :userId 
            ORDER BY data_mesura DESC
        ');
        
        $this->db->bind(':userId', $userId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtener la última medición de un usuario
     * @param int $userId ID del usuario
     * @return object|bool Última medición o false si no existe
     */
    public function getLastMeasurement($userId) {
        $this->db->query('
            SELECT * 
            FROM seguiment_fisic 
            WHERE usuari_id = :userId 
            ORDER BY data_mesura DESC 
            LIMIT 1
        ');
        
        $this->db->bind(':userId', $userId);
        
        return $this->db->single();
    }
    
    /**
     * Añadir una nueva medición
     * @param array $data Datos de la medición
     * @return bool True si se ha añadido correctamente
     */
    public function addMeasurement($data) {
        // Validar datos de entrada
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return false;
        }
        
        // Calcular IMC si tenemos peso y altura
        if (!empty($data['pes']) && !empty($data['alcada'])) {
            // El IMC se calcula como peso (kg) / (altura (m))^2
            // La altura en la BD está en cm, hay que convertirla a metros
            $altura_metros = $data['alcada'] / 100;
            $data['imc'] = round($data['pes'] / ($altura_metros * $altura_metros), 2);
        }
        
        $this->db->query('
            INSERT INTO seguiment_fisic (usuari_id, pes, alcada, imc, data_mesura) 
            VALUES (:userId, :weight, :height, :imc, NOW())
        ');
        
        // Vincular valores
        $this->db->bind(':userId', $data['usuari_id']);
        $this->db->bind(':weight', $data['pes']);
        $this->db->bind(':height', $data['alcada']);
        $this->db->bind(':imc', $data['imc']);
        
        // Ejecutar
        return $this->db->execute();
    }
    
    /**
     * Actualizar una medición existente
     * @param array $data Datos de la medición
     * @return bool True si se ha actualizado correctamente
     */
    public function updateMeasurement($data) {
        // Validar datos de entrada
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return false;
        }
        
        // Calcular IMC si tenemos peso y altura
        if (!empty($data['pes']) && !empty($data['alcada'])) {
            // El IMC se calcula como peso (kg) / (altura (m))^2
            // La altura en la BD está en cm, hay que convertirla a metros
            $altura_metros = $data['alcada'] / 100;
            $data['imc'] = round($data['pes'] / ($altura_metros * $altura_metros), 2);
        }
        
        $this->db->query('
            UPDATE seguiment_fisic 
            SET pes = :weight, 
                alcada = :height, 
                imc = :imc
            WHERE seguiment_id = :id
        ');
        
        // Vincular valores
        $this->db->bind(':id', $data['seguiment_id']);
        $this->db->bind(':weight', $data['pes']);
        $this->db->bind(':height', $data['alcada']);
        $this->db->bind(':imc', $data['imc']);
        
        // Ejecutar
        return $this->db->execute();
    }
    
    /**
     * Eliminar una medición
     * @param int $measurementId ID de la medición
     * @param int $userId ID del usuario (para verificar que sea propietario)
     * @return bool True si se ha eliminado correctamente
     */
    public function deleteMeasurement($measurementId, $userId) {
        $this->db->query('
            DELETE FROM seguiment_fisic 
            WHERE seguiment_id = :id AND usuari_id = :userId
        ');
        
        $this->db->bind(':id', $measurementId);
        $this->db->bind(':userId', $userId);
        
        return $this->db->execute();
    }
    
    /**
     * Obtener mediciones para el gráfico de evolución
     * @param int $userId ID del usuario
     * @param int $months Número de meses hacia atrás (3, 6, 12)
     * @return array Mediciones para el gráfico
     */
    public function getChartData($userId, $months = 6) {
        $this->db->query('
            SELECT 
                DATE_FORMAT(data_mesura, "%d/%m/%Y") as fecha_formateada,
                DATE_FORMAT(data_mesura, "%Y-%m-%d") as fecha,
                pes, 
                imc
            FROM seguiment_fisic 
            WHERE usuari_id = :userId 
              AND data_mesura >= DATE_SUB(NOW(), INTERVAL :months MONTH)
            ORDER BY data_mesura ASC
        ');
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':months', $months);
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtener una medida específica
     * @param int $measurementId ID de la medición
     * @return object|bool Medición o false si no existe
     */
    public function getMeasurementById($measurementId) {
        $this->db->query('
            SELECT * 
            FROM seguiment_fisic 
            WHERE seguiment_id = :id
        ');
        
        $this->db->bind(':id', $measurementId);
        
        return $this->db->single();
    }
}
?>