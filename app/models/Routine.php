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
                         ORDER BY r.rutina_id DESC');
        $this->db->bind(':usuari_id', $userId);
        return $this->db->resultSet();
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
        $this->db->query('INSERT INTO rutines (usuari_id, nom, descripcio) VALUES (:usuari_id, :nom, :descripcio)');
        
        // Vincular valores
        $this->db->bind(':usuari_id', $data['usuari_id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio']);

        // Ejecutar
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Actualizar una rutina
    public function updateRoutine($data) {
        $this->db->query('UPDATE rutines SET usuari_id = :usuari_id, nom = :nom, descripcio = :descripcio WHERE rutina_id = :rutina_id');
        
        // Vincular valores
        $this->db->bind(':rutina_id', $data['rutina_id']);
        $this->db->bind(':usuari_id', $data['usuari_id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio']);

        // Ejecutar
        return $this->db->execute();
    }

    // Actualizar la ruta del PDF de una rutina
    public function updateRoutinePdf($id, $pdfPath) {
        $this->db->query('UPDATE rutines SET ruta_pdf = :ruta_pdf WHERE rutina_id = :rutina_id');
        
        // Vincular valores
        $this->db->bind(':rutina_id', $id);
        $this->db->bind(':ruta_pdf', $pdfPath);

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

    // Añadir un ejercicio a una rutina
    public function addExercise($data) {
        // Prepara la consulta base
        $query = 'INSERT INTO exercicis (rutina_id, nom, descripcio, series, repeticions, descans, imatge_url, ordre';
        
        // Si hay información adicional, añadir el campo a la consulta
        if (isset($data['info_adicional'])) {
            $query .= ', info_adicional';
        }
        
        $query .= ') VALUES (:rutina_id, :nom, :descripcio, :series, :repeticions, :descans, :imatge_url, :ordre';
        
        // Si hay información adicional, añadir el parámetro
        if (isset($data['info_adicional'])) {
            $query .= ', :info_adicional';
        }
        
        $query .= ')';
        
        $this->db->query($query);
        
        // Vincular valores básicos
        $this->db->bind(':rutina_id', $data['rutina_id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio']);
        $this->db->bind(':series', $data['series']);
        $this->db->bind(':repeticions', $data['repeticions']);
        $this->db->bind(':descans', $data['descans']);
        $this->db->bind(':imatge_url', $data['imatge_url']);
        
        // Si no se especifica un orden, obtener el siguiente
        if (!isset($data['ordre']) || empty($data['ordre'])) {
            $data['ordre'] = $this->getNextExerciseOrder($data['rutina_id']);
        }
        $this->db->bind(':ordre', $data['ordre']);
        
        // Si hay información adicional, vincularla
        if (isset($data['info_adicional'])) {
            $this->db->bind(':info_adicional', $data['info_adicional']);
        }

        // Ejecutar
        return $this->db->execute();
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
        // Preparar la consulta base
        $query = 'UPDATE exercicis SET 
                  nom = :nom, 
                  descripcio = :descripcio, 
                  series = :series, 
                  repeticions = :repeticions, 
                  descans = :descans';
        
        // Añadir campos opcionales si están presentes
        if (isset($data['imatge_url'])) {
            $query .= ', imatge_url = :imatge_url';
        }
        
        if (isset($data['ordre'])) {
            $query .= ', ordre = :ordre';
        }
        
        if (isset($data['info_adicional'])) {
            $query .= ', info_adicional = :info_adicional';
        }
        
        $query .= ' WHERE exercici_id = :exercici_id';
        
        $this->db->query($query);
        
        // Vincular valores obligatorios
        $this->db->bind(':exercici_id', $data['exercici_id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio']);
        $this->db->bind(':series', $data['series']);
        $this->db->bind(':repeticions', $data['repeticions']);
        $this->db->bind(':descans', $data['descans']);
        
        // Vincular valores opcionales
        if (isset($data['imatge_url'])) {
            $this->db->bind(':imatge_url', $data['imatge_url']);
        }
        
        if (isset($data['ordre'])) {
            $this->db->bind(':ordre', $data['ordre']);
        }
        
        if (isset($data['info_adicional'])) {
            $this->db->bind(':info_adicional', $data['info_adicional']);
        }
        
        // Ejecutar la consulta
        return $this->db->execute();
    }

    // Obtener un ejercicio por su ID
    public function getExerciseById($id) {
        $this->db->query('SELECT * FROM exercicis WHERE exercici_id = :exercici_id');
        $this->db->bind(':exercici_id', $id);
        return $this->db->single();
    }
}