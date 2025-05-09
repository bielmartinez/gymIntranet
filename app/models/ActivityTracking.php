<?php
/**
 * Modelo para seguimiento de actividad física
 */
require_once dirname(__FILE__) . '/BaseModel.php';

class ActivityTracking extends BaseModel {
    protected $table = 'seguiment_activitat';
    protected $primaryKey = 'activitat_id';
    
    // Propiedades
    private $id;
    private $userId;
    private $activityType; // 'workout', 'class', 'cardio', etc.
    private $duration;
    private $calories;
    private $date;
    private $notes;
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Obtiene el seguimiento de actividad de un usuario específico
     * @param int $userId ID del usuario
     * @return array Registros de actividad
     */
    public function getUserActivities($userId) {
        $this->db->query('
            SELECT * 
            FROM seguiment_activitat 
            WHERE usuari_id = :userId 
            ORDER BY data_activitat DESC
        ');
        
        $this->db->bind(':userId', $userId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtiene la actividad más reciente de un usuario
     * @param int $userId ID del usuario
     * @return object|bool Datos de la actividad o false si no existe
     */
    public function getLastActivity($userId) {
        $this->db->query('
            SELECT * 
            FROM seguiment_activitat 
            WHERE usuari_id = :userId 
            ORDER BY data_activitat DESC 
            LIMIT 1
        ');
        
        $this->db->bind(':userId', $userId);
        
        return $this->db->single();
    }
    
    /**
     * Registra una nueva actividad
     * @param array $data Datos de la actividad
     * @return int|bool ID de la actividad registrada o false en caso de error
     */
    public function addActivity($data) {
        $this->db->query('
            INSERT INTO seguiment_activitat 
            (usuari_id, tipus_activitat, duracio, calories, data_activitat, notes) 
            VALUES 
            (:userId, :activityType, :duration, :calories, :activityDate, :notes)
        ');
        
        $this->db->bind(':userId', $data['usuari_id']);
        $this->db->bind(':activityType', $data['tipus_activitat']);
        $this->db->bind(':duration', $data['duracio']);
        $this->db->bind(':calories', $data['calories']);
        $this->db->bind(':activityDate', $data['data_activitat']);
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Actualiza una actividad existente
     * @param array $data Datos actualizados de la actividad
     * @return bool Éxito o fracaso de la operación
     */
    public function updateActivity($data) {
        $this->db->query('
            UPDATE seguiment_activitat 
            SET tipus_activitat = :activityType, 
                duracio = :duration,
                calories = :calories,
                data_activitat = :activityDate,
                notes = :notes
            WHERE activitat_id = :id
        ');
        
        $this->db->bind(':id', $data['activitat_id']);
        $this->db->bind(':activityType', $data['tipus_activitat']);
        $this->db->bind(':duration', $data['duracio']);
        $this->db->bind(':calories', $data['calories']);
        $this->db->bind(':activityDate', $data['data_activitat']);
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        return $this->db->execute();
    }
    
    /**
     * Elimina una actividad
     * @param int $activityId ID de la actividad a eliminar
     * @return bool Éxito o fracaso de la operación
     */
    public function deleteActivity($activityId) {
        return $this->delete($activityId);
    }
    
    /**
     * Obtiene estadísticas de actividad de un usuario en un período determinado
     * @param int $userId ID del usuario
     * @param string $startDate Fecha inicial (formato Y-m-d)
     * @param string $endDate Fecha final (formato Y-m-d)
     * @return object Estadísticas de actividad
     */
    public function getActivityStats($userId, $startDate = null, $endDate = null) {
        $sql = '
            SELECT 
                COUNT(*) as total_activities,
                SUM(duracio) as total_duration,
                SUM(calories) as total_calories,
                AVG(duracio) as avg_duration,
                AVG(calories) as avg_calories
            FROM seguiment_activitat 
            WHERE usuari_id = :userId
        ';
        
        if ($startDate) {
            $sql .= ' AND data_activitat >= :startDate';
        }
        
        if ($endDate) {
            $sql .= ' AND data_activitat <= :endDate';
        }
        
        $this->db->query($sql);
        $this->db->bind(':userId', $userId);
        
        if ($startDate) {
            $this->db->bind(':startDate', $startDate);
        }
        
        if ($endDate) {
            $this->db->bind(':endDate', $endDate);
        }
        
        return $this->db->single();
    }
    
    /**
     * Valida datos de la actividad
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        $errors = [];
        
        // Validar campos requeridos
        if (empty($data['usuari_id'])) {
            $errors['usuari_id'] = 'El usuario es requerido';
        }
        
        if (empty($data['tipus_activitat'])) {
            $errors['tipus_activitat'] = 'El tipo de actividad es requerido';
        }
        
        if (empty($data['duracio']) || !is_numeric($data['duracio']) || $data['duracio'] <= 0) {
            $errors['duracio'] = 'La duración debe ser un número positivo';
        }
        
        if (empty($data['calories']) || !is_numeric($data['calories']) || $data['calories'] < 0) {
            $errors['calories'] = 'Las calorías deben ser un número no negativo';
        }
        
        if (empty($data['data_activitat'])) {
            $errors['data_activitat'] = 'La fecha de actividad es requerida';
        } else {
            $format = 'Y-m-d';
            $d = DateTime::createFromFormat($format, $data['data_activitat']);
            
            if (!($d && $d->format($format) === $data['data_activitat'])) {
                $errors['data_activitat'] = 'El formato de fecha no es válido (YYYY-MM-DD)';
            }
        }
        
        return $errors;
    }
}
?>
