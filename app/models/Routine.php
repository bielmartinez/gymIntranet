<?php
/**
 * Modelo para la gestión de rutinas de ejercicios
 */

// Cargar configuración y base de datos
require_once dirname(dirname(__FILE__)) . '/config/config.php';
require_once dirname(dirname(__FILE__)) . '/libraries/Database.php';
require_once dirname(dirname(__FILE__)) . '/utils/Logger.php';

class Routine {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Obtener todas las rutinas
    public function getAllRoutines() {
        $this->db->query('SELECT r.*, 
                         (SELECT COUNT(*) FROM exercicis e WHERE e.rutina_id = r.rutina_id) AS exercise_count 
                         FROM rutines r 
                         ORDER BY r.rutina_id DESC');
        return $this->db->resultSet();
    }

    // Obtener rutinas por usuario
    public function getRoutinesByUser($userId) {
        $this->db->query('SELECT r.*, 
                         (SELECT COUNT(*) FROM exercicis e WHERE e.rutina_id = r.rutina_id) AS exercise_count 
                         FROM rutines r 
                         WHERE r.usuari_id = :usuari_id 
                         ORDER BY r.creat_el DESC');
        $this->db->bind(':usuari_id', $userId);
        
        $result = $this->db->resultSet();
        
        // Registrar para depuración
        if (class_exists('Logger') && empty($result)) {
            Logger::log('DEBUG', 'No se encontraron rutinas para el usuario ID: ' . $userId);
        }
        
        return $result;
    }

    // Obtener una rutina por ID
    public function getRoutineById($id) {
        $this->db->query('SELECT r.*, 
                         (SELECT COUNT(*) FROM exercicis e WHERE e.rutina_id = r.rutina_id) AS exercise_count 
                         FROM rutines r 
                         WHERE r.rutina_id = :rutina_id');
        $this->db->bind(':rutina_id', $id);
        return $this->db->single();
    }

    // Crear una nueva rutina
    public function addRoutine($data) {
        $this->db->query('INSERT INTO rutines (usuari_id, nom, descripcio, creat_el) 
                          VALUES (:usuari_id, :nom, :descripcio, :creat_el)');
        
        // Vincular valores obligatorios
        $this->db->bind(':usuari_id', $data['usuari_id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio']);
        $this->db->bind(':creat_el', $data['creat_el'] ?? date('Y-m-d H:i:s'));

        // Ejecutar
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            // Registrar el error
            if (class_exists('Logger')) {
                Logger::log('ERROR', 'Error en addRoutine: ' . json_encode($this->db->getError()));
            }
            return false;
        }
    }

    // Actualizar una rutina
    public function updateRoutine($data) {
        $this->db->query('UPDATE rutines SET usuari_id = :usuari_id, nom = :nom, descripcio = :descripcio WHERE rutina_id = :rutina_id');
        
        // Vincular valores
        $this->db->bind(':rutina_id', $data['id']);
        $this->db->bind(':usuari_id', $data['usuari_id']);
        $this->db->bind(':nom', $data['name']);
        $this->db->bind(':descripcio', $data['description']);

        // Ejecutar
        return $this->db->execute();
    }

    // Eliminar una rutina
    public function deleteRoutine($id) {
        // Primero eliminamos los ejercicios asociados
        $this->db->query('DELETE FROM exercicis WHERE rutina_id = :rutina_id');
        $this->db->bind(':rutina_id', $id);
        $this->db->execute();
        
        // Luego eliminamos la rutina
        $this->db->query('DELETE FROM rutines WHERE rutina_id = :rutina_id');
        $this->db->bind(':rutina_id', $id);
        return $this->db->execute();
    }

    // Verificar si una rutina pertenece a un usuario
    public function isRoutineOwnedByUser($routineId, $userId) {
        $this->db->query('SELECT COUNT(*) as count FROM rutines WHERE rutina_id = :rutina_id AND usuari_id = :usuari_id');
        $this->db->bind(':rutina_id', $routineId);
        $this->db->bind(':usuari_id', $userId);
        
        $row = $this->db->single();
        return $row->count > 0;
    }

    // Obtener ejercicios de una rutina
    public function getExercisesByRoutine($routineId) {
        $this->db->query('SELECT * FROM exercicis WHERE rutina_id = :rutina_id ORDER BY ordre ASC');
        $this->db->bind(':rutina_id', $routineId);
        return $this->db->resultSet();
    }
    
    // Alias para getExercisesByRoutine para mantener compatibilidad con el controlador
    public function getExercisesByRoutineId($routineId) {
        return $this->getExercisesByRoutine($routineId);
    }

    /**
     * Añade un ejercicio a una rutina
     * @param array $data Datos del ejercicio
     * @return bool True si se añadió correctamente, False si no
     */
    public function addExercise($data)
    {
        // Mapear claves para compatibilidad entre controlador y modelo
        $rutina_id = $data['routine_id'] ?? null;
        $nom = $data['name'] ?? null;
        $descripcio = $data['description'] ?? '';
        $series = $data['sets'] ?? 3;
        $repeticions = $data['reps'] ?? 10;
        $descans = $data['rest'] ?? 60;
        $ordre = $data['order'] ?? $this->getNextExerciseOrder($rutina_id);
        $info_adicional = $data['additional_info'] ?? null;
        
        // Registrar los datos para depuración
        if (class_exists('Logger')) {
            Logger::log('DEBUG', 'Datos recibidos en addExercise: ' . json_encode($data));
        }
        
        // Verificar datos críticos
        if (empty($rutina_id) || empty($nom)) {
            if (class_exists('Logger')) {
                Logger::log('ERROR', 'Error en addExercise: ID de rutina o nombre del ejercicio faltantes');
            }
            return false;
        }

        // Verificar si tenemos información adicional
        if (!empty($info_adicional)) {
            $query = 'INSERT INTO exercicis (rutina_id, nom, descripcio, series, repeticions, descans, ordre, info_adicional) 
                      VALUES (:rutina_id, :nom, :descripcio, :series, :repeticions, :descans, :ordre, :info_adicional)';
        } else {
            $query = 'INSERT INTO exercicis (rutina_id, nom, descripcio, series, repeticions, descans, ordre) 
                      VALUES (:rutina_id, :nom, :descripcio, :series, :repeticions, :descans, :ordre)';
        }

        $this->db->query($query);
        
        // Vincular parámetros
        $this->db->bind(':rutina_id', $rutina_id);
        $this->db->bind(':nom', $nom);
        $this->db->bind(':descripcio', $descripcio);
        $this->db->bind(':series', $series);
        $this->db->bind(':repeticions', $repeticions);
        $this->db->bind(':descans', $descans);
        $this->db->bind(':ordre', $ordre);
        
        // Si hay información adicional, vincularla
        if (!empty($info_adicional)) {
            $this->db->bind(':info_adicional', $info_adicional);
        }
        
        // Ejecutar
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            if (class_exists('Logger')) {
                Logger::log('ERROR', 'Error en addExercise: ' . json_encode($this->db->getError()));
            }
            return false;
        }
    }

    // Eliminar un ejercicio
    public function deleteExercise($id) {
        $this->db->query('DELETE FROM exercicis WHERE exercici_id = :exercici_id');
        $this->db->bind(':exercici_id', $id);
        return $this->db->execute();
    }
    
    // Obtener el siguiente orden para un ejercicio en una rutina
    public function getNextExerciseOrder($routineId) {
        $this->db->query('SELECT MAX(ordre) as max_order FROM exercicis WHERE rutina_id = :rutina_id');
        $this->db->bind(':rutina_id', $routineId);
        $result = $this->db->single();
        
        // Si no hay ejercicios, comenzamos en 1, si no, incrementamos el máximo
        return (empty($result) || $result->max_order === null) ? 1 : $result->max_order + 1;
    }

    // Actualizar un ejercicio existente
    public function updateExercise($data) {
        // Mapear claves para compatibilidad entre controlador y modelo
        $exercici_id = $data['id'] ?? null;
        $nom = $data['name'] ?? null;
        $descripcio = $data['description'] ?? '';
        $series = $data['sets'] ?? null;
        $repeticions = $data['reps'] ?? null;
        $descans = $data['rest'] ?? null;
        $ordre = $data['order'] ?? null;
        $info_adicional = $data['additional_info'] ?? null;

        // Verificar datos críticos
        if (empty($exercici_id) || empty($nom)) {
            if (class_exists('Logger')) {
                Logger::log('ERROR', 'Error en updateExercise: ID de ejercicio o nombre faltantes');
            }
            return false;
        }
        
        // Preparar la consulta base
        $query = 'UPDATE exercicis SET 
                  nom = :nom, 
                  descripcio = :descripcio, 
                  series = :series, 
                  repeticions = :repeticions, 
                  descans = :descans';
        
        // Añadir campos opcionales si están presentes
        if (!empty($ordre)) {
            $query .= ', ordre = :ordre';
        }
        
        if (!empty($info_adicional)) {
            $query .= ', info_adicional = :info_adicional';
        }
        
        $query .= ' WHERE exercici_id = :exercici_id';
        
        // Registrar la consulta para depuración
        if (class_exists('Logger')) {
            Logger::log('DEBUG', 'Query updateExercise: ' . $query);
            Logger::log('DEBUG', 'Datos recibidos en updateExercise: ' . json_encode($data));
        }
        
        $this->db->query($query);
        
        // Vincular valores obligatorios
        $this->db->bind(':exercici_id', $exercici_id);
        $this->db->bind(':nom', $nom);
        $this->db->bind(':descripcio', $descripcio);
        $this->db->bind(':series', $series);
        $this->db->bind(':repeticions', $repeticions);
        $this->db->bind(':descans', $descans);
        
        // Vincular valores opcionales
        if (!empty($ordre)) {
            $this->db->bind(':ordre', $ordre);
        }
        
        if (!empty($info_adicional)) {
            $this->db->bind(':info_adicional', $info_adicional);
        }
        
        // Ejecutar la consulta
        if ($this->db->execute()) {
            return true;
        } else {
            if (class_exists('Logger')) {
                Logger::log('ERROR', 'Error en updateExercise: ' . json_encode($this->db->getError()));
            }
            return false;
        }
    }

    // Obtener un ejercicio por su ID
    public function getExerciseById($id) {
        $this->db->query('SELECT * FROM exercicis WHERE exercici_id = :exercici_id');
        $this->db->bind(':exercici_id', $id);
        return $this->db->single();
    }
    
    /**
     * Verifica si ya existe un ejercicio con ese orden y si es así, reordena los ejercicios
     * @param int $routineId ID de la rutina
     * @param int $order Orden seleccionado
     * @param int|null $excludeExerciseId ID del ejercicio a excluir (para actualizaciones)
     * @return int Orden final a utilizar
     */
    public function verifyAndFixExerciseOrder($routineId, $order, $excludeExerciseId = null) {
        // Verificar si ya existe un ejercicio con ese orden en la misma rutina
        if ($excludeExerciseId) {
            $this->db->query('SELECT COUNT(*) as count FROM exercicis 
                             WHERE rutina_id = :rutina_id 
                             AND ordre = :ordre 
                             AND exercici_id != :exercici_id');
            $this->db->bind(':exercici_id', $excludeExerciseId);
        } else {
            $this->db->query('SELECT COUNT(*) as count FROM exercicis 
                             WHERE rutina_id = :rutina_id 
                             AND ordre = :ordre');
        }
        
        $this->db->bind(':rutina_id', $routineId);
        $this->db->bind(':ordre', $order);
        
        $result = $this->db->single();
        
        // Si no hay conflictos, retornar el orden original
        if ($result->count == 0) {
            return $order;
        }
        
        // Si hay conflicto, incrementar el orden de los ejercicios a partir del orden seleccionado
        $this->db->query('UPDATE exercicis 
                         SET ordre = ordre + 1 
                         WHERE rutina_id = :rutina_id 
                         AND ordre >= :ordre');
        $this->db->bind(':rutina_id', $routineId);
        $this->db->bind(':ordre', $order);
        
        $this->db->execute();
        
        // Registrar acción
        if (class_exists('Logger')) {
            Logger::log('DEBUG', "Reordenados ejercicios de rutina ID: $routineId a partir del orden: $order");
        }
        
        return $order;
    }
    
    /**
     * Obtener el último error de la base de datos
     * @return mixed Información del error
     */
    public function getLastError() {
        return $this->db->getError();
    }
}