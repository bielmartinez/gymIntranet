<?php
/**
 * Modelo para gestión de clases
 */

// Cargar configuración y base de datos
require_once dirname(dirname(__FILE__)) . '/config/config.php';
require_once dirname(dirname(__FILE__)) . '/libraries/Database.php';

class Class_ {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Obtiene una clase por su ID
     * @param int $classId ID de la clase
     * @return object|bool Objeto con datos de la clase o false si no existe
     */
    public function getClassById($classId) {
        $this->db->query('SELECT c.*, tc.nom as tipus_nom, tc.descripcio as tipus_descripcio, 
                          CONCAT(u.nom, " ", u.cognoms) as monitor_nom
                          FROM classes c
                          JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
                          JOIN usuaris u ON c.monitor_id = u.usuari_id
                          WHERE c.classe_id = :id');
        $this->db->bind(':id', $classId);
        
        $row = $this->db->single();
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }
    
    /**
     * Incrementa la capacidad actual de una clase
     * @param int $classId ID de la clase
     * @return bool Éxito o fracaso de la operación
     */
    public function incrementCapacity($classId) {
        $this->db->query('UPDATE classes SET capacitat_actual = capacitat_actual + 1 
                         WHERE classe_id = :id');
        $this->db->bind(':id', $classId);
        return $this->db->execute();
    }
    
    /**
     * Actualiza la capacidad actual de una clase
     * @param int $classId ID de la clase
     * @param int $increment Incremento (1) o decremento (-1) de capacidad
     * @return bool Éxito o fracaso de la operación
     */
    public function updateCapacity($classId, $increment = -1) {
        $this->db->query('UPDATE classes SET capacitat_actual = capacitat_actual + :increment 
                         WHERE classe_id = :id AND (capacitat_actual + :safe_increment) >= 0');
        $this->db->bind(':id', $classId);
        $this->db->bind(':increment', $increment);
        $this->db->bind(':safe_increment', $increment);
        return $this->db->execute();
    }
    
    /**
     * Crea una nueva clase
     * @param array $data Datos de la clase
     * @return int|bool ID de la clase creada o false en caso de error
     */
    public function create($data) {
        $this->db->query('INSERT INTO classes (tipus_classe_id, monitor_id, data, hora, duracio, 
                          capacitat_maxima, capacitat_actual, sala) 
                          VALUES (:tipus, :monitor, :data, :hora, :duracio, :capacitat, 0, :sala)');
                          
        $this->db->bind(':tipus', $data['tipus_classe_id']);
        $this->db->bind(':monitor', $data['monitor_id']);
        $this->db->bind(':data', $data['data']);
        $this->db->bind(':hora', $data['hora']);
        $this->db->bind(':duracio', $data['duracio']);
        $this->db->bind(':capacitat', $data['capacitat_maxima']);
        $this->db->bind(':sala', $data['sala']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Actualiza los datos de una clase
     * @param array $data Datos actualizados de la clase
     * @return bool Éxito o fracaso de la operación
     */
    public function update($data) {
        $this->db->query('UPDATE classes SET 
                          tipus_classe_id = :tipus, 
                          monitor_id = :monitor, 
                          data = :data, 
                          hora = :hora, 
                          duracio = :duracio, 
                          capacitat_maxima = :capacitat, 
                          sala = :sala 
                          WHERE classe_id = :id');
                          
        $this->db->bind(':id', $data['classe_id']);
        $this->db->bind(':tipus', $data['tipus_classe_id']);
        $this->db->bind(':monitor', $data['monitor_id']);
        $this->db->bind(':data', $data['data']);
        $this->db->bind(':hora', $data['hora']);
        $this->db->bind(':duracio', $data['duracio']);
        $this->db->bind(':capacitat', $data['capacitat_maxima']);
        $this->db->bind(':sala', $data['sala']);
        
        return $this->db->execute();
    }
    
    /**
     * Elimina una clase
     * @param int $classId ID de la clase a eliminar
     * @return bool Éxito o fracaso de la operación
     */
    public function delete($classId) {
        $this->db->query('DELETE FROM classes WHERE classe_id = :id');
        $this->db->bind(':id', $classId);
        return $this->db->execute();
    }
    
    /**
     * Elimina una clase (alias para delete)
     * @param int $classId ID de la clase a eliminar
     * @return bool Éxito o fracaso de la operación
     */
    public function deleteClass($classId) {
        return $this->delete($classId);
    }
    
    /**
     * Obtiene todas las clases programadas
     * @param string $filterDate Fecha opcional para filtrar
     * @param int $instructorId ID del monitor para filtrar
     * @return array Lista de clases
     */
    public function getAllClasses($filterDate = null, $instructorId = null) {
        $sql = 'SELECT c.*, tc.nom as tipus_nom, tc.descripcio as tipus_descripcio, 
               CONCAT(u.nom, " ", u.cognoms) as monitor_nom 
               FROM classes c
               JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
               JOIN usuaris u ON c.monitor_id = u.usuari_id
               WHERE 1=1';
               
        if ($filterDate) {
            $sql .= ' AND c.data = :data';
        }
        
        if ($instructorId) {
            $sql .= ' AND c.monitor_id = :instructor';
        }
        
        $sql .= ' ORDER BY c.data ASC, c.hora ASC';
        
        $this->db->query($sql);
        
        if ($filterDate) {
            $this->db->bind(':data', $filterDate);
        }
        
        if ($instructorId) {
            $this->db->bind(':instructor', $instructorId);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtiene las clases asignadas a un monitor específico
     * @param int $monitorId ID del monitor
     * @return array Lista de clases
     */
    public function getClassesByInstructor($monitorId) {
        return $this->getAllClasses(null, $monitorId);
    }
    
    /**
     * Verifica si hay conflictos de horario para un monitor en una fecha y hora específicas
     * @param array $data Datos de la clase (fecha, hora, monitor_id, duracio)
     * @param int $excludeClassId ID de clase a excluir de la verificación (útil para actualizaciones)
     * @return bool True si hay conflicto, False en caso contrario
     */
    public function hasScheduleConflict($data, $excludeClassId = null) {
        // Calcular el tiempo de finalización de la clase propuesta
        $startTime = strtotime($data['data'] . ' ' . $data['hora']);
        $endTime = $startTime + ($data['duracio'] * 60); // Convertir minutos a segundos
        
        // Obtener todas las clases del monitor en la misma fecha
        $sql = 'SELECT classe_id, data, hora, duracio 
                FROM classes 
                WHERE monitor_id = :monitor_id 
                AND data = :date';
        
        if ($excludeClassId) {
            $sql .= ' AND classe_id != :exclude_id';
        }
        
        $this->db->query($sql);
        $this->db->bind(':monitor_id', $data['monitor_id']);
        $this->db->bind(':date', $data['data']);
        
        if ($excludeClassId) {
            $this->db->bind(':exclude_id', $excludeClassId);
        }
        
        $classes = $this->db->resultSet();
        
        // Verificar si hay solapamiento de horarios con alguna clase
        foreach ($classes as $class) {
            $classStartTime = strtotime($class->data . ' ' . $class->hora);
            $classEndTime = $classStartTime + ($class->duracio * 60); // Convertir minutos a segundos
            
            // Verificar solapamiento de horarios
            if (($startTime < $classEndTime) && ($endTime > $classStartTime)) {
                return true; // Hay conflicto
            }
        }
        
        return false; // No hay conflicto
    }
    
    /**
     * Obtiene las clases activas que son iguales o posteriores a la fecha actual
     * @param string $filterDate Fecha opcional para filtrar (formato Y-m-d)
     * @param int $instructorId ID del monitor para filtrar
     * @return array Lista de clases activas
     */
    public function getActiveClasses($filterDate = null, $instructorId = null) {
        $currentDate = date('Y-m-d');
        
        $sql = 'SELECT c.*, tc.nom as tipus_nom, tc.descripcio as tipus_descripcio, 
               CONCAT(u.nom, " ", u.cognoms) as monitor_nom 
               FROM classes c
               JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
               JOIN usuaris u ON c.monitor_id = u.usuari_id
               WHERE c.data >= :current_date';
        
        if ($filterDate) {
            $sql .= ' AND c.data = :filter_date';
        }
        
        if ($instructorId) {
            $sql .= ' AND c.monitor_id = :instructor';
        }
        
        $sql .= ' ORDER BY c.data ASC, c.hora ASC';
        
        $this->db->query($sql);
        $this->db->bind(':current_date', $currentDate);
        
        if ($filterDate) {
            $this->db->bind(':filter_date', $filterDate);
        }
        
        if ($instructorId) {
            $this->db->bind(':instructor', $instructorId);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Añade una nueva clase (alias para create)
     * @param array $data Datos de la clase
     * @return int|bool ID de la clase creada o false en caso de error
     */
    public function addClass($data) {
        return $this->create($data);
    }
    
    /**
     * Actualiza los datos de una clase
     * @param array $data Datos actualizados de la clase
     * @return bool Éxito o fracaso de la operación
     */
    public function updateClass($data) {
        return $this->update($data);
    }
    
    /**
     * Filtra las clases según los criterios seleccionados
     * @param array $filters Criterios de filtrado (date, type_id, monitor_id)
     * @return array Lista de clases filtradas
     */
    public function filterClasses($filters) {
        $sql = 'SELECT c.*, tc.nom as tipus_nom, tc.descripcio as tipus_descripcio, 
               CONCAT(u.nom, " ", u.cognoms) as monitor_nom 
               FROM classes c
               JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
               JOIN usuaris u ON c.monitor_id = u.usuari_id
               WHERE 1=1';
        
        $params = [];
        
        // Filtrar por fecha
        if (!empty($filters['date'])) {
            $sql .= ' AND c.data = :date';
            $params[':date'] = $filters['date'];
        }
        
        // Filtrar por tipo de clase
        if (!empty($filters['type_id'])) {
            $sql .= ' AND c.tipus_classe_id = :type_id';
            $params[':type_id'] = $filters['type_id'];
        }
        
        // Filtrar por monitor
        if (!empty($filters['monitor_id'])) {
            $sql .= ' AND c.monitor_id = :monitor_id';
            $params[':monitor_id'] = $filters['monitor_id'];
        }
        
        // Ordenar los resultados
        $sql .= ' ORDER BY c.data ASC, c.hora ASC';
        
        $this->db->query($sql);
        
        // Asignar los parámetros
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtiene las clases programadas para los próximos N días
     * @param int $days Número de días a consultar desde hoy
     * @return array Lista de clases de los próximos días
     */
    public function getUpcomingClasses($days = 3) {
        $currentDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+$days days"));
        
        $sql = 'SELECT c.*, tc.nom as tipus_nom, tc.descripcio as tipus_descripcio, 
               CONCAT(u.nom, " ", u.cognoms) as monitor_nom, tc.nom as clase_tipo 
               FROM classes c
               JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
               JOIN usuaris u ON c.monitor_id = u.usuari_id
               WHERE c.data >= :current_date AND c.data <= :end_date
               ORDER BY c.data ASC, c.hora ASC';
        
        $this->db->query($sql);
        $this->db->bind(':current_date', $currentDate);
        $this->db->bind(':end_date', $endDate);
        
        return $this->db->resultSet();
    }
}
?>
