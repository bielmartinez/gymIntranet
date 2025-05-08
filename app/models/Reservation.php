<?php
/**
 * Modelo para gestión de reservas
 */
class Reservation {
    private $id;
    private $userId;
    private $classId;
    private $reservationDate;
    private $attendance;
    private $db;
    
    public function __construct() {
        $this->db = new Database();
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
    
    // Métodos CRUD
    /**
     * Crear una nueva reserva
     * @return boolean
     */
    public function create() {
        $sql = "INSERT INTO reserves (usuari_id, classe_id, data_reserva, assistencia) 
                VALUES (:user_id, :class_id, NOW(), :attendance)";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $this->userId);
        $this->db->bind(':class_id', $this->classId);
        $this->db->bind(':attendance', $this->attendance ?? 0);
        
        if($this->db->execute()) {
            // Si la reserva se crea con éxito, actualizar la capacidad actual de la clase
            $sqlUpdate = "UPDATE classes SET capacitat_actual = capacitat_actual + 1 
                         WHERE classe_id = :class_id";
            $this->db->query($sqlUpdate);
            $this->db->bind(':class_id', $this->classId);
            return $this->db->execute();
        }
        
        return false;
    }
    
    /**
     * Actualizar una reserva existente
     * @return boolean
     */
    public function update() {
        $sql = "UPDATE reserves 
                SET usuari_id = :user_id, classe_id = :class_id, assistencia = :attendance 
                WHERE reserva_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $this->id);
        $this->db->bind(':user_id', $this->userId);
        $this->db->bind(':class_id', $this->classId);
        $this->db->bind(':attendance', $this->attendance);
        
        return $this->db->execute();
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
                         WHERE classe_id = :class_id";
            $this->db->query($sqlUpdate);
            $this->db->bind(':class_id', $result->classe_id);
            return $this->db->execute();
        }
        
        return false;
    }
    
    /**
     * Eliminar una reserva
     * @return boolean
     */
    public function delete() {
        return $this->cancel(); // Reutilizamos la función cancel que hace lo mismo
    }
    
    /**
     * Obtener una reserva por ID
     * @param int $id ID de la reserva
     * @return object
     */
    public function findById($id) {
        $sql = "SELECT * FROM reserves WHERE reserva_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        if ($row) {
            $this->id = $row->reserva_id;
            $this->userId = $row->usuari_id;
            $this->classId = $row->classe_id;
            $this->reservationDate = $row->data_reserva;
            $this->attendance = $row->assistencia;
        }
        
        return $row;
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
        $sql = "SELECT COUNT(*) as count FROM reserves 
                WHERE usuari_id = :user_id AND classe_id = :class_id";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':class_id', $classId);
        $result = $this->db->single();
        
        return $result->count > 0;
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
        $this->db->query('SELECT r.*, u.id as usuari_id, u.nom, u.cognoms, u.correu,
                          DATE_FORMAT(r.data_reserva, "%d/%m/%Y %H:%i") as data_reserva
                          FROM reserves r
                          JOIN usuaris u ON r.usuari_id = u.id
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
    }

    /**
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
}
?>
