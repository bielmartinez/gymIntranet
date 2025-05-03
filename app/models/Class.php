<?php
/**
 * Modelo para la gestión de clases
 */
class Class_ {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Obtener todas las clases
     * @return array
     */
    public function getAllClasses() {
        $sql = "SELECT * FROM classes ORDER BY data DESC, hora ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    /**
     * Obtener clases activas (desde hoy en adelante)
     * @return array
     */
    public function getActiveClasses() {
        $sql = "SELECT * FROM classes WHERE data >= CURDATE() ORDER BY data ASC, hora ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    /**
     * Obtener una clase por su ID
     * @param int $id ID de la clase
     * @return object
     */
    public function getClassById($id) {
        $sql = "SELECT * FROM classes WHERE classe_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Crear una nueva clase
     * @param array $data Datos de la clase
     * @return bool
     */
    public function addClass($data) {
        $sql = "INSERT INTO classes (tipus_classe_id, monitor_id, data, hora, duracio, capacitat_maxima, capacitat_actual, sala) 
                VALUES (:tipus_classe_id, :monitor_id, :data, :hora, :duracio, :capacitat_maxima, 0, :sala)";
                
        $this->db->query($sql);
        $this->db->bind(':tipus_classe_id', $data['tipus_classe_id']);
        $this->db->bind(':monitor_id', $data['monitor_id']);
        $this->db->bind(':data', $data['data']);
        $this->db->bind(':hora', $data['hora']);
        $this->db->bind(':duracio', $data['duracio']);
        $this->db->bind(':capacitat_maxima', $data['capacitat_maxima']);
        $this->db->bind(':sala', $data['sala']);
        
        return $this->db->execute();
    }
    
    /**
     * Actualizar una clase existente
     * @param array $data Datos de la clase
     * @return bool
     */
    public function updateClass($data) {
        $sql = "UPDATE classes 
                SET tipus_classe_id = :tipus_classe_id, 
                    monitor_id = :monitor_id, 
                    data = :data, 
                    hora = :hora, 
                    duracio = :duracio, 
                    capacitat_maxima = :capacitat_maxima, 
                    sala = :sala 
                WHERE classe_id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':id', $data['classe_id']);
        $this->db->bind(':tipus_classe_id', $data['tipus_classe_id']);
        $this->db->bind(':monitor_id', $data['monitor_id']);
        $this->db->bind(':data', $data['data']);
        $this->db->bind(':hora', $data['hora']);
        $this->db->bind(':duracio', $data['duracio']);
        $this->db->bind(':capacitat_maxima', $data['capacitat_maxima']);
        $this->db->bind(':sala', $data['sala']);
        
        return $this->db->execute();
    }
    
    /**
     * Eliminar una clase
     * @param int $id ID de la clase
     * @return bool
     */
    public function deleteClass($id) {
        // Primero, eliminar todas las reservas asociadas a esta clase
        $sqlReserves = "DELETE FROM reserves WHERE classe_id = :id";
        $this->db->query($sqlReserves);
        $this->db->bind(':id', $id);
        $this->db->execute();
        
        // Luego eliminar la clase
        $sql = "DELETE FROM classes WHERE classe_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * Filtrar clases por diferentes parámetros
     * @param array $filters Filtros aplicados
     * @return array
     */
    public function filterClasses($filters) {
        $sql = "SELECT c.*, t.nom as tipus_nom, CONCAT(u.nom, ' ', u.cognoms) as monitor_nom 
                FROM classes c 
                JOIN tipus_classes t ON c.tipus_classe_id = t.tipus_classe_id
                JOIN personal p ON c.monitor_id = p.personal_id
                JOIN usuaris u ON p.usuari_id = u.usuari_id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['date'])) {
            $sql .= " AND c.data = :date";
            $params[':date'] = $filters['date'];
        }
        
        if (!empty($filters['type_id'])) {
            $sql .= " AND c.tipus_classe_id = :type_id";
            $params[':type_id'] = $filters['type_id'];
        }
        
        if (!empty($filters['monitor_id'])) {
            $sql .= " AND c.monitor_id = :monitor_id";
            $params[':monitor_id'] = $filters['monitor_id'];
        }
        
        $sql .= " ORDER BY c.data ASC, c.hora ASC";
        
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Verificar si hay conflictos de horario para un monitor
     * @param array $data Datos de la clase
     * @param int $excludeClassId ID de la clase a excluir (para edición)
     * @return bool
     */
    public function hasScheduleConflict($data, $excludeClassId = null) {
        // Calcular la hora de finalización
        $endTime = date('H:i:s', strtotime($data['hora']) + $data['duracio'] * 60);
        
        $sql = "SELECT COUNT(*) as conflict_count 
                FROM classes 
                WHERE monitor_id = :monitor_id 
                AND data = :data 
                AND (
                    (hora <= :start_time AND ADDTIME(hora, SEC_TO_TIME(duracio * 60)) > :start_time) OR 
                    (hora < :end_time AND ADDTIME(hora, SEC_TO_TIME(duracio * 60)) >= :end_time) OR
                    (hora >= :start_time AND ADDTIME(hora, SEC_TO_TIME(duracio * 60)) <= :end_time)
                )";
        
        if ($excludeClassId) {
            $sql .= " AND classe_id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':monitor_id', $data['monitor_id']);
        $this->db->bind(':data', $data['data']);
        $this->db->bind(':start_time', $data['hora']);
        $this->db->bind(':end_time', $endTime);
        
        if ($excludeClassId) {
            $this->db->bind(':exclude_id', $excludeClassId);
        }
        
        $result = $this->db->single();
        
        return $result->conflict_count > 0;
    }
    
    /**
     * Obtener el conteo de reservas para una clase
     * @param int $classId ID de la clase
     * @return int
     */
    public function getReservationCount($classId) {
        $sql = "SELECT COUNT(*) as count FROM reserves WHERE classe_id = :class_id";
        $this->db->query($sql);
        $this->db->bind(':class_id', $classId);
        $result = $this->db->single();
        
        return $result->count;
    }
    
    /**
     * Actualizar la capacidad actual de una clase
     * @param int $classId ID de la clase
     * @return bool
     */
    public function updateCapacity($classId) {
        $reservationCount = $this->getReservationCount($classId);
        
        $sql = "UPDATE classes SET capacitat_actual = :count WHERE classe_id = :id";
        $this->db->query($sql);
        $this->db->bind(':count', $reservationCount);
        $this->db->bind(':id', $classId);
        
        return $this->db->execute();
    }
}
?>
