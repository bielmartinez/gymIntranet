<?php
/**
 * Modelo para gestión de clases
 */

require_once dirname(__FILE__) . '/BaseModel.php';

class Class_ extends BaseModel {
    protected $table = 'classes';
    protected $primaryKey = 'classe_id';
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Valida los datos de entrada para una clase
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        $errors = [];
        
        // Validar tipo de clase
        if (empty($data['tipus_classe_id'])) {
            $errors['tipus_classe_id'] = 'El tipo de clase es requerido';
        }
        
        // Validar monitor
        if (empty($data['monitor_id'])) {
            $errors['monitor_id'] = 'El monitor es requerido';
        }
        
        // Validar fecha
        if (empty($data['data'])) {
            $errors['data'] = 'La fecha es requerida';
        } else {
            $format = 'Y-m-d';
            $d = \DateTime::createFromFormat($format, $data['data']);
            
            if (!($d && $d->format($format) === $data['data'])) {
                $errors['data'] = 'El formato de fecha no es válido (YYYY-MM-DD)';
            }
        }
          // Validar hora
        if (empty($data['hora'])) {
            $errors['hora'] = 'La hora es requerida';
        } else {
            // Intentar varios formatos de hora permitidos (HH:MM:SS o HH:MM)
            $format1 = 'H:i:s';
            $format2 = 'H:i';
            
            $t1 = \DateTime::createFromFormat($format1, $data['hora']);
            $t2 = \DateTime::createFromFormat($format2, $data['hora']);
            
            if (!($t1 && $t1->format($format1) === $data['hora']) && 
                !($t2 && $t2->format($format2) === $data['hora'])) {
                $errors['hora'] = 'El formato de hora no es válido (debe ser HH:MM o HH:MM:SS)';
            }
        }
        
        // Validar duración
        if (empty($data['duracio'])) {
            $errors['duracio'] = 'La duración es requerida';
        } else if (!is_numeric($data['duracio']) || $data['duracio'] <= 0) {
            $errors['duracio'] = 'La duración debe ser un número positivo';
        }
        
        // Validar capacidad máxima
        if (empty($data['capacitat_maxima'])) {
            $errors['capacitat_maxima'] = 'La capacidad máxima es requerida';
        } else if (!is_numeric($data['capacitat_maxima']) || $data['capacitat_maxima'] <= 0) {
            $errors['capacitat_maxima'] = 'La capacidad máxima debe ser un número positivo';
        }
          // Validar sala
        if (empty($data['sala'])) {
            $errors['sala'] = 'La sala es requerida';
        } else if (!is_numeric($data['sala']) || $data['sala'] < 1 || $data['sala'] > 4) {
            $errors['sala'] = 'El número de sala debe estar entre 1 y 4';
        }
        
        return $errors;
    }
    
    /**
     * Obtiene una clase por su ID con información adicional
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
        
        return $this->db->single();
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
     * Reinicia la capacidad actual de la clase a 0
     * @param int $classId ID de la clase
     * @return bool Éxito o fracaso de la operación
     */
    public function resetCapacity($classId) {
        $this->db->query('UPDATE classes SET capacitat_actual = 0 WHERE classe_id = :id');
        $this->db->bind(':id', $classId);
        return $this->db->execute();
    }
    
    /**
     * Crea una nueva clase
     * @param array $data Datos de la clase
     * @return int|bool ID de la clase creada o false en caso de error
     */
    public function addClass($data) {
        // Validar datos
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return false;
        }
        
        // Verificar conflictos de horario
        if ($this->hasScheduleConflict($data)) {
            return false;
        }
          // Formatear la hora correctamente si viene sin segundos
        $hora = $data['hora'];
        if (strpos($hora, ':') !== false && substr_count($hora, ':') === 1) {
            $hora .= ':00'; // Añadir segundos
        }
        
        // Preparar datos para la creación
        $insertData = [
            'tipus_classe_id' => $data['tipus_classe_id'],
            'monitor_id' => $data['monitor_id'],
            'data' => $data['data'],
            'hora' => $hora,
            'duracio' => $data['duracio'],
            'capacitat_maxima' => $data['capacitat_maxima'],
            'capacitat_actual' => 0,
            'sala' => $data['sala']
        ];
        
        // Usar el método create de BaseModel
        return $this->create($insertData);
    }
      /**
     * Actualiza los datos de una clase
     * @param array $data Datos actualizados de la clase
     * @return bool Éxito o fracaso de la operación
     */
    public function updateClass($data) {
        // Formatear la hora correctamente si viene sin segundos
        if (isset($data['hora']) && strpos($data['hora'], ':') !== false && substr_count($data['hora'], ':') === 1) {
            $data['hora'] .= ':00'; // Añadir segundos
        }
        
        // Validar datos
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return false;
        }
        
        // Verificar conflictos de horario (excluyendo la propia clase)
        if ($this->hasScheduleConflict($data, $data['classe_id'])) {
            return false;
        }
        
        // Usar el método update de BaseModel
        return $this->update($data);
    }
    
    /**
     * Elimina una clase (alias para delete de BaseModel)
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
    
    /**
     * Obtiene las clases programadas para los próximos N días de un monitor específico
     * @param int $monitorId ID del monitor
     * @param int $days Número de días a consultar desde hoy
     * @return array Lista de clases del monitor para los próximos días
     */
    public function getUpcomingClassesByMonitor($monitorId, $days = 3) {
        $currentDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+$days days"));
        
        $sql = 'SELECT c.*, tc.nom as tipus_nom, tc.descripcio as tipus_descripcio, 
               CONCAT(u.nom, " ", u.cognoms) as monitor_nom, tc.nom as clase_tipo 
               FROM classes c
               JOIN tipus_classes tc ON c.tipus_classe_id = tc.tipus_classe_id
               JOIN usuaris u ON c.monitor_id = u.usuari_id
               WHERE c.data >= :current_date AND c.data <= :end_date 
               AND c.monitor_id = :monitor_id
               ORDER BY c.data ASC, c.hora ASC';
        
        $this->db->query($sql);
        $this->db->bind(':current_date', $currentDate);
        $this->db->bind(':end_date', $endDate);
        $this->db->bind(':monitor_id', $monitorId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtiene el número de clases programadas para hoy
     * @return int Número de clases programadas para hoy
     */
    public function getTodayClassesCount() {
        $currentDate = date('Y-m-d');
        
        $this->db->query('SELECT COUNT(*) as total FROM classes WHERE data = :current_date');
        $this->db->bind(':current_date', $currentDate);
        
        $row = $this->db->single();
        return $row->total;
    }
}
?>
