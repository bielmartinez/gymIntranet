<?php
/**
 * Modelo para gestión de reservas
 * 
 * @property int $id ID de la reserva (reserva_id)
 * @property int $userId ID del usuario (usuari_id)
 * @property int $classId ID de la clase (classe_id)
 * @property string $reservationDate Fecha de la reserva (data_reserva)
 * @property int $attendance Estado de asistencia (assistencia)
 */
require_once dirname(__FILE__) . '/BaseModel.php';

class Reservation extends BaseModel {
    protected $table = 'reserves';
    protected $primaryKey = 'reserva_id';
    
    // Mapeo de campos para compatibilidad entre español e inglés
    protected $fieldMapping = [
        'id' => 'reserva_id',
        'userId' => 'usuari_id',
        'classId' => 'classe_id',
        'reservationDate' => 'data_reserva',
        'attendance' => 'assistencia'
    ];
    
    // Propiedades para la compatibilidad con el código existente
    private $id;
    private $userId;
    private $classId;
    private $reservationDate;
    private $attendance;
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        parent::__construct();
    }    /**
     * Valida los datos de entrada para una reserva
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        $errors = [];
        
        // Mapear nombres de campos en inglés a español si existen
        $userId = $data['usuari_id'] ?? $data['user_id'] ?? null;
        $classId = $data['classe_id'] ?? $data['class_id'] ?? null;
        
        // Validar usuario
        if (empty($userId)) {
            $errors['usuari_id'] = 'El usuario es requerido';
        }
        
        // Validar clase
        if (empty($classId)) {
            $errors['classe_id'] = 'La clase es requerida';
        }
        
        // Solo validar disponibilidad y reserva previa si no hay errores básicos
        if (empty($errors)) {
            // Validar que la clase tenga plazas disponibles
            if (!$this->isClassAvailable($classId)) {
                $errors['classe_id'] = 'No hay plazas disponibles para esta clase';
            }
            
            // Validar que el usuario no tenga ya una reserva para esta clase
            if ($this->userHasReservation($userId, $classId)) {
                $errors['reserva'] = 'Ya tienes una reserva para esta clase';
            }
        }
        
        return $errors;
    }
    
    // Getters y setters
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getUserId() {
        return $this->userId;
    }
    
    public function setUserId($userId) {
        $this->userId = $userId;
    }
    
    public function getClassId() {
        return $this->classId;
    }
    
    public function setClassId($classId) {
        $this->classId = $classId;
    }
    
    public function getReservationDate() {
        return $this->reservationDate;
    }
    
    public function setReservationDate($reservationDate) {
        $this->reservationDate = $reservationDate;
    }
    
    public function getAttendance() {
        return $this->attendance;
    }
    
    public function setAttendance($attendance) {
        $this->attendance = $attendance;
    }
      /**
     * Crear una nueva reserva
     * @param array $data Datos de la reserva (opcional, si no se proporciona se usan los campos del objeto)
     * @return boolean|int ID del registro creado o false en caso de error
     */
    public function create($data = null) {
        // Si no se proporcionan datos, usar los valores del objeto
        if ($data === null) {
            $data = [
                'usuari_id' => $this->userId,
                'classe_id' => $this->classId,
                'assistencia' => $this->attendance ?? 0
            ];
        }
        
        // Aplicar mapeo de campo si es necesario
        if (isset($data['user_id']) && !isset($data['usuari_id'])) {
            $data['usuari_id'] = $data['user_id'];
        }
        
        if (isset($data['class_id']) && !isset($data['classe_id'])) {
            $data['classe_id'] = $data['class_id'];
        }
        
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return false;
        }
        
        $userId = $data['usuari_id'] ?? $this->userId;
        $classId = $data['classe_id'] ?? $this->classId;
        $attendance = $data['assistencia'] ?? $this->attendance ?? 0;
        
        $sql = "INSERT INTO reserves (usuari_id, classe_id, data_reserva, assistencia) 
                VALUES (:user_id, :class_id, NOW(), :attendance)";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':class_id', $classId);
        $this->db->bind(':attendance', $attendance);
        
        if($this->db->execute()) {
            $reservationId = $this->db->lastInsertId();
            
            // Si la reserva se crea con éxito, actualizar la capacidad actual de la clase
            $sqlUpdate = "UPDATE classes SET capacitat_actual = capacitat_actual + 1 
                         WHERE classe_id = :class_id";
            $this->db->query($sqlUpdate);
            $this->db->bind(':class_id', $classId);
            
            if ($this->db->execute()) {
                // Actualizar propiedades del objeto
                $this->id = $reservationId;
                $this->userId = $userId;
                $this->classId = $classId;
                $this->attendance = $attendance;
                
                return $reservationId;
            }
        }
        
        return false;
    }
      /**
     * Actualizar una reserva existente
     * @param array $data Datos actualizados (debe incluir el ID)
     * @return boolean Éxito o fracaso de la operación
     */
    public function update($data = null) {
        // Si no se proporcionan datos, usar los valores del objeto
        if ($data === null) {
            $data = [
                'usuari_id' => $this->userId,
                'classe_id' => $this->classId,
                'reserva_id' => $this->id,
                'assistencia' => $this->attendance
            ];
        }
        
        // Asegurarse de que tenemos un ID
        if (!isset($data['reserva_id']) && $this->id) {
            $data['reserva_id'] = $this->id;
        }
        
        if (!isset($data['reserva_id'])) {
            return false;
        }
        
        // Aplicar mapeo de campo si es necesario
        if (isset($data['user_id']) && !isset($data['usuari_id'])) {
            $data['usuari_id'] = $data['user_id'];
        }
        
        if (isset($data['class_id']) && !isset($data['classe_id'])) {
            $data['classe_id'] = $data['class_id'];
        }
        
        // No validamos disponibilidad porque ya tiene una reserva
        
        $sql = "UPDATE reserves 
                SET usuari_id = :user_id, classe_id = :class_id, assistencia = :attendance 
                WHERE reserva_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $data['reserva_id']);
        $this->db->bind(':user_id', $data['usuari_id'] ?? $this->userId);
        $this->db->bind(':class_id', $data['classe_id'] ?? $this->classId);
        $this->db->bind(':attendance', $data['assistencia'] ?? $this->attendance ?? 0);
        
        $success = $this->db->execute();
        
        if ($success) {
            // Actualizar propiedades del objeto
            $this->id = $data['reserva_id'];
            $this->userId = $data['usuari_id'] ?? $this->userId;
            $this->classId = $data['classe_id'] ?? $this->classId;
            $this->attendance = $data['assistencia'] ?? $this->attendance ?? 0;
        }
        
        return $success;
    }
      /**
     * Cancelar una reserva
     * @return boolean
     */
    public function cancel() {
        // Primero obtenemos el class_id de la reserva actual para actualizar la capacidad
        $sqlGet = "SELECT classe_id FROM reserves WHERE reserva_id = :id";
        $this->db->query($sqlGet);
        $this->db->bind(':id', $this->id);
        $result = $this->db->single();
        
        if(!$result) {
            return false;
        }
        
        // Eliminar la reserva
        $sql = "DELETE FROM reserves WHERE reserva_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $this->id);
        
        if($this->db->execute()) {
            // Actualizar la capacidad actual de la clase
            $sqlUpdate = "UPDATE classes SET capacitat_actual = capacitat_actual - 1 
                         WHERE classe_id = :class_id AND capacitat_actual > 0";
            $this->db->query($sqlUpdate);
            $this->db->bind(':class_id', $result->classe_id);
            return $this->db->execute();
        }
        
        return false;
    }
      /**
     * Eliminar una reserva
     * @param int $id ID de la reserva a eliminar (opcional)
     * @return boolean Éxito o fracaso de la operación
     */
    public function delete($id = null) {
        if ($id !== null) {
            $this->id = $id;
        }
        return $this->cancel(); // Reutilizamos la función cancel que hace lo mismo
    }
    
    /**
     * Obtener una reserva por ID
     * @param int $id ID de la reserva
     * @return object
     */
    public function findById($id) {
        $reservation = $this->getById($id);
        
        if ($reservation) {
            $this->id = $reservation->reserva_id;
            $this->userId = $reservation->usuari_id;
            $this->classId = $reservation->classe_id;
            $this->reservationDate = $reservation->data_reserva;
            $this->attendance = $reservation->assistencia;
        }
        
        return $reservation;
    }
    
    /**
     * Obtener todas las reservas de un usuario
     * @param int $userId ID del usuario
     * @return array
     */
    public function findByUserId($userId) {
        $sql = "SELECT r.*, c.*, tc.nom as tipus_nom, tc.descripcio as tipus_descripcio, 
                    CONCAT(u_monitor.nom, ' ', u_monitor.cognoms) as monitor_nom
                FROM reserves r
                JOIN classes c ON r.classe_id = c.classe_id
                JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
                JOIN usuaris u_monitor ON c.monitor_id = u_monitor.usuari_id
                WHERE r.usuari_id = :user_id
                ORDER BY c.data ASC, c.hora ASC";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    
    /**
     * Comprobar si una clase tiene plazas disponibles
     * @param int $classId ID de la clase
     * @return boolean
     */
    public function isClassAvailable($classId) {
        // Obtenemos la información de la clase
        $sqlClass = "SELECT capacitat_maxima, capacitat_actual FROM classes WHERE classe_id = :class_id";
        $this->db->query($sqlClass);
        $this->db->bind(':class_id', $classId);
        $class = $this->db->single();
        
        if (!$class) {
            return false; // La clase no existe
        }
        
        // Hay plazas disponibles si la capacidad actual es menor que la capacidad máxima
        return ($class->capacitat_actual < $class->capacitat_maxima);
    }
    
    /**
     * Comprobar si el usuario ya tiene una reserva para esta clase
     * @param int $userId ID del usuario
     * @param int $classId ID de la clase
     * @return boolean
     */
    public function userHasReservation($userId, $classId) {
        return $this->hasUserReservation($userId, $classId);
    }
    
    /**
     * Obtener todas las reservas para una clase específica
     * @param int $classId ID de la clase
     * @return array
     */
    public function getClassReservations($classId) {
        $sql = "SELECT r.*, u.nom, u.cognoms, u.correu 
                FROM reserves r
                JOIN usuaris u ON r.usuari_id = u.usuari_id
                WHERE r.classe_id = :class_id";
        
        $this->db->query($sql);
        $this->db->bind(':class_id', $classId);
        return $this->db->resultSet();
    }
    
    /**
     * Obtener clases disponibles para reservar
     * @param string $date Fecha opcional para filtrar
     * @return array
     */
    public function getAvailableClasses($date = null) {
        $sql = "SELECT c.*, tc.nom as tipus_nom, tc.descripcio as tipus_descripcio, 
                CONCAT(u.nom, ' ', u.cognoms) as monitor_nom
                FROM classes c 
                JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
                JOIN usuaris u ON c.monitor_id = u.usuari_id
                WHERE c.capacitat_actual < c.capacitat_maxima";
        
        if($date) {
            $sql .= " AND c.data = :date";
        } else {
            // Si no se especifica una fecha, mostrar solo clases de hoy en adelante
            $sql .= " AND c.data >= CURDATE()";
        }
        
        $sql .= " ORDER BY c.data ASC, c.hora ASC";
        
        $this->db->query($sql);
        
        if($date) {
            $this->db->bind(':date', $date);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtener todos los estudiantes inscritos en una clase
     * @param int $classId ID de la clase
     * @return array Estudiantes inscritos
     */
    public function getStudentsByClassId($classId) {
        $this->db->query('SELECT r.*, u.usuari_id, u.nom, u.cognoms, u.correu,
                          DATE_FORMAT(r.data_reserva, "%d/%m/%Y %H:%i") as data_reserva
                          FROM reserves r
                          JOIN usuaris u ON r.usuari_id = u.usuari_id
                          WHERE r.classe_id = :class_id
                          ORDER BY r.data_reserva ASC');
        $this->db->bind(':class_id', $classId);
        return $this->db->resultSet();
    }
    
    /**
     * Actualizar el estado de asistencia de una reserva
     * @param int $reservationId ID de la reserva
     * @param bool $attended Si asistió (1) o no (0)
     * @return bool Si se actualizó correctamente
     */
    public function updateAttendance($reservationId, $attended) {
        $this->db->query('UPDATE reserves SET assistencia = :assistencia 
                         WHERE reserva_id = :reserva_id');
        $this->db->bind(':reserva_id', $reservationId);
        $this->db->bind(':assistencia', $attended);
        return $this->db->execute();
    }
    
    /**
     * Cancelar una reserva mediante su ID (para uso directo sin instanciar el objeto)
     * @param int $reservationId ID de la reserva a cancelar
     * @return bool
     */
    public function cancelReservation($reservationId) {
        $this->id = $reservationId; // Establecer el ID para usar el método cancel
        return $this->cancel();
    }

    /**
     * Comprobar si el usuario ya tiene una reserva para esta clase
     * @param int $userId ID del usuario
     * @param int $classId ID de la clase
     * @return boolean
     */
    public function hasUserReservation($userId, $classId) {
        $sql = "SELECT COUNT(*) as count FROM reserves 
                WHERE usuari_id = :user_id AND classe_id = :class_id";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':class_id', $classId);
        
        $result = $this->db->single();
        
        return $result->count > 0;
    }

    /**
     * Añade una reserva sin usar el método create
     * @param int $userId ID del usuario
     * @param int $classId ID de la clase
     * @return boolean Éxito o fracaso de la operación
     */
    public function addReservation($userId, $classId) {
        $this->setUserId($userId);
        $this->setClassId($classId);
        $this->setAttendance(0);
        return $this->create();
    }    /**
     * Elimina una reserva para un usuario y clase específicos
     * @param int $userId ID del usuario
     * @param int $classId ID de la clase
     * @return boolean Éxito o fracaso de la operación
     */
    public function deleteReservation($userId, $classId) {
        $sql = "DELETE FROM reserves WHERE usuari_id = :user_id AND classe_id = :class_id";
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':class_id', $classId);
        return $this->db->execute();
    }
    
    /**
     * Elimina todas las reservas asociadas a una clase
     * @param int $classId ID de la clase
     * @return boolean Éxito o fracaso de la operación
     */
    public function deleteAllReservationsByClassId($classId) {
        $sql = "DELETE FROM reserves WHERE classe_id = :class_id";
        $this->db->query($sql);
        $this->db->bind(':class_id', $classId);
        return $this->db->execute();
    }
    
    /**
     * Obtener estadísticas de reservas por tipo de clase
     * @return array
     */
    public function getReservationStatsByClassType() {
        $sql = "SELECT tc.tipus_classe_id, tc.nom, COUNT(r.reserva_id) as total_reservas
                FROM reserves r
                JOIN classes c ON r.classe_id = c.classe_id
                JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
                GROUP BY tc.tipus_classe_id
                ORDER BY total_reservas DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    /**
     * Obtener estadísticas de asistencia
     * @return object
     */
    public function getAttendanceStats() {
        $sql = "SELECT 
                SUM(CASE WHEN assistencia = 1 THEN 1 ELSE 0 END) as attended,
                SUM(CASE WHEN assistencia = 0 THEN 1 ELSE 0 END) as not_attended,
                COUNT(*) as total
                FROM reserves";
                
        $this->db->query($sql);
        return $this->db->single();
    }

    /**
     * Obtener todas las reservas de una clase específica con información de usuario
     * @param int $classId ID de la clase
     * @return array
     */
    public function getReservationsByClassId($classId) {
        $sql = "SELECT r.*, r.reserva_id, r.usuari_id, r.classe_id, r.data_reserva, r.assistencia,
                CONCAT(u.nom, ' ', u.cognoms) as usuario_nombre
                FROM reserves r
                JOIN usuaris u ON r.usuari_id = u.usuari_id
                WHERE r.classe_id = :class_id
                ORDER BY r.data_reserva DESC";
        
        $this->db->query($sql);
        $this->db->bind(':class_id', $classId);
        return $this->db->resultSet();
    }
}
?>
