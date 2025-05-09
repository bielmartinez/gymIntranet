<?php
/**
 * Modelo para la gestión de rutinas de ejercicios
 * 
 * @property int $id ID de la rutina (rutina_id)
 * @property int $userId ID del usuario (usuari_id)
 * @property string $name Nombre de la rutina (nom)
 * @property string $description Descripción de la rutina (descripcio)
 * @property string $createdAt Fecha de creación (creat_el)
 */

require_once dirname(__FILE__) . '/BaseModel.php';

class Routine extends BaseModel {
    protected $table = 'rutines';
    protected $primaryKey = 'rutina_id';
    
    // Mapeo de campos para compatibilidad entre español e inglés
    protected $fieldMapping = [
        'id' => 'rutina_id',
        'userId' => 'usuari_id',
        'name' => 'nom',
        'description' => 'descripcio',
        'createdAt' => 'creat_el'
    ];
    
    // Propiedades para la compatibilidad con el código existente
    private $id;
    private $userId;
    private $name;
    private $description;
    private $createdAt;
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Valida los datos de entrada para una rutina
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        $errors = [];
        
        // Mapear nombres de campos en inglés a español si existen
        $userId = $data['usuari_id'] ?? $data['userId'] ?? null;
        $name = $data['nom'] ?? $data['name'] ?? null;
        
        // Validar usuario
        if (empty($userId)) {
            $errors['usuari_id'] = 'El usuario es requerido';
        }
        
        // Validar nombre
        if (empty($name)) {
            $errors['nom'] = 'El nombre de la rutina es requerido';
        } elseif (strlen($name) > 100) {
            $errors['nom'] = 'El nombre no puede exceder los 100 caracteres';
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
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    /**
     * Obtiene el último mensaje de error de la base de datos
     * @return string|null El mensaje de error o null si no hay error
     */
    public function getLastError() {
        return $this->db->getError();
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }    /**
     * Obtener todas las rutinas
     * @return array Lista de rutinas
     */
    public function getAllRoutines() {
        $this->db->query('SELECT r.*, 
                         (SELECT COUNT(*) FROM exercicis e WHERE e.rutina_id = r.rutina_id) AS exercise_count 
                         FROM rutines r 
                         ORDER BY r.rutina_id DESC');
        return $this->db->resultSet();
    }
    
    /**
     * Obtener rutinas por usuario
     * @param int $userId ID del usuario
     * @return array Lista de rutinas del usuario
     */
    public function getRoutinesByUser($userId) {
        $this->db->query('SELECT r.*, 
                         (SELECT COUNT(*) FROM exercicis e WHERE e.rutina_id = r.rutina_id) AS exercise_count 
                         FROM rutines r 
                         WHERE r.usuari_id = :usuari_id 
                         ORDER BY r.creat_el DESC');
        $this->db->bind(':usuari_id', $userId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtener una rutina por ID
     * @param int $id ID de la rutina
     * @return object Datos de la rutina
     */
    public function getRoutineById($id) {
        $routine = $this->getById($id);
        
        if ($routine) {
            // Obtener la cantidad de ejercicios para esta rutina
            $this->db->query('SELECT COUNT(*) as exercise_count FROM exercicis WHERE rutina_id = :rutina_id');
            $this->db->bind(':rutina_id', $id);
            $exerciseCount = $this->db->single();
            
            // Añadir la propiedad exercise_count al objeto rutina
            $routine->exercise_count = $exerciseCount->exercise_count;
            
            // Actualizar propiedades del objeto
            $this->id = $routine->rutina_id;
            $this->userId = $routine->usuari_id;
            $this->name = $routine->nom;
            $this->description = $routine->descripcio;
            $this->createdAt = $routine->creat_el;
        }
        
        return $routine;
    }    /**
     * Crear una nueva rutina
     * @param array $data Datos de la rutina
     * @return bool|int ID de la rutina creada o false si hay error
     */
    public function create($data = null) {
        // Si no se proporcionan datos, usar los valores del objeto
        if ($data === null) {
            $data = [
                'usuari_id' => $this->userId,
                'nom' => $this->name,
                'descripcio' => $this->description,
                'creat_el' => $this->createdAt ?? date('Y-m-d H:i:s')
            ];
        }
        
        // Aplicar mapeo de campo si es necesario
        if (isset($data['userId']) && !isset($data['usuari_id'])) {
            $data['usuari_id'] = $data['userId'];
        }
        
        if (isset($data['name']) && !isset($data['nom'])) {
            $data['nom'] = $data['name'];
        }
        
        if (isset($data['description']) && !isset($data['descripcio'])) {
            $data['descripcio'] = $data['description'];
        }
        
        // Validar datos
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return false;
        }
        
        // Preparar la consulta
        $this->db->query('INSERT INTO rutines (usuari_id, nom, descripcio, creat_el) 
                          VALUES (:usuari_id, :nom, :descripcio, :creat_el)');
        
        // Vincular valores
        $this->db->bind(':usuari_id', $data['usuari_id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio'] ?? '');
        $this->db->bind(':creat_el', $data['creat_el'] ?? date('Y-m-d H:i:s'));
        
        // Ejecutar
        if ($this->db->execute()) {
            $routineId = $this->db->lastInsertId();
            
            // Actualizar propiedades del objeto
            $this->id = $routineId;
            $this->userId = $data['usuari_id'];
            $this->name = $data['nom'];
            $this->description = $data['descripcio'] ?? '';
            $this->createdAt = $data['creat_el'] ?? date('Y-m-d H:i:s');
            
            return $routineId;
        }
        
        return false;
    }
    
    /**
     * Alias para create, mantiene compatibilidad
     */
    public function addRoutine($data) {
        return $this->create($data);
    }
    
    /**
     * Actualizar una rutina existente
     * @param array $data Datos actualizados
     * @return bool Éxito o fracaso de la operación
     */
    public function update($data = null) {
        // Si no se proporcionan datos, usar los valores del objeto
        if ($data === null) {
            $data = [
                'rutina_id' => $this->id,
                'usuari_id' => $this->userId,
                'nom' => $this->name,
                'descripcio' => $this->description
            ];
        }
        
        // Asegurarse de que tenemos un ID
        if (!isset($data['rutina_id']) && $this->id) {
            $data['rutina_id'] = $this->id;
        }
        
        if (!isset($data['rutina_id'])) {
            return false;
        }
        
        // Aplicar mapeo de campo si es necesario
        if (isset($data['id']) && !isset($data['rutina_id'])) {
            $data['rutina_id'] = $data['id'];
        }
        
        if (isset($data['userId']) && !isset($data['usuari_id'])) {
            $data['usuari_id'] = $data['userId'];
        }
        
        if (isset($data['name']) && !isset($data['nom'])) {
            $data['nom'] = $data['name'];
        }
        
        if (isset($data['description']) && !isset($data['descripcio'])) {
            $data['descripcio'] = $data['description'];
        }
        
        // Preparar la consulta
        $this->db->query('UPDATE rutines SET usuari_id = :usuari_id, nom = :nom, descripcio = :descripcio 
                          WHERE rutina_id = :rutina_id');
        
        // Vincular valores
        $this->db->bind(':rutina_id', $data['rutina_id']);
        $this->db->bind(':usuari_id', $data['usuari_id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio'] ?? '');
        
        // Ejecutar
        $success = $this->db->execute();
        
        if ($success) {
            // Actualizar propiedades del objeto
            $this->id = $data['rutina_id'];
            $this->userId = $data['usuari_id'];
            $this->name = $data['nom'];
            $this->description = $data['descripcio'] ?? '';
        }
        
        return $success;
    }
    
    /**
     * Alias para update, mantiene compatibilidad
     */
    public function updateRoutine($data) {
        return $this->update($data);
    }
    
    /**
     * Eliminar una rutina
     * @param int $id ID de la rutina a eliminar (opcional)
     * @return bool Éxito o fracaso de la operación
     */
    public function delete($id = null) {
        if ($id !== null) {
            $this->id = $id;
        }
        
        if (!$this->id) {
            return false;
        }
        
        // Primero eliminamos los ejercicios asociados
        $this->db->query('DELETE FROM exercicis WHERE rutina_id = :rutina_id');
        $this->db->bind(':rutina_id', $this->id);
        $this->db->execute();
        
        // Luego eliminamos la rutina
        $this->db->query('DELETE FROM rutines WHERE rutina_id = :rutina_id');
        $this->db->bind(':rutina_id', $this->id);
        return $this->db->execute();
    }
    
    /**
     * Alias para delete, mantiene compatibilidad
     */
    public function deleteRoutine($id) {
        return $this->delete($id);
    }    /**
     * Verificar si una rutina pertenece a un usuario
     * @param int $routineId ID de la rutina
     * @param int $userId ID del usuario
     * @return bool True si la rutina pertenece al usuario
     */
    public function isRoutineOwnedByUser($routineId, $userId) {
        $this->db->query('SELECT COUNT(*) as count FROM rutines WHERE rutina_id = :rutina_id AND usuari_id = :usuari_id');
        $this->db->bind(':rutina_id', $routineId);
        $this->db->bind(':usuari_id', $userId);
        
        $row = $this->db->single();
        return $row->count > 0;
    }
    
    /**
     * Obtener ejercicios de una rutina
     * @param int $routineId ID de la rutina
     * @return array Lista de ejercicios
     */
    public function getExercisesByRoutine($routineId) {
        $this->db->query('SELECT * FROM exercicis WHERE rutina_id = :rutina_id ORDER BY ordre ASC');
        $this->db->bind(':rutina_id', $routineId);
        return $this->db->resultSet();
    }
    
    /**
     * Alias para getExercisesByRoutine para mantener compatibilidad con el controlador
     */
    public function getExercisesByRoutineId($routineId) {
        return $this->getExercisesByRoutine($routineId);
    }
    
    /**
     * Añade un ejercicio a una rutina
     * @param array $data Datos del ejercicio
     * @return bool|int ID del ejercicio creado o false si hay error
     */
    public function addExercise($data) {
        // Mapear claves para compatibilidad entre controlador y modelo
        $rutina_id = $data['routine_id'] ?? $data['rutina_id'] ?? null;
        $nom = $data['name'] ?? $data['nom'] ?? null;
        $descripcio = $data['description'] ?? $data['descripcio'] ?? '';
        $series = $data['sets'] ?? $data['series'] ?? 3;
        $repeticions = $data['reps'] ?? $data['repeticions'] ?? 10;
        $descans = $data['rest'] ?? $data['descans'] ?? 60;
        $ordre = $data['order'] ?? $data['ordre'] ?? $this->getNextExerciseOrder($rutina_id);
        $info_adicional = $data['additional_info'] ?? $data['info_adicional'] ?? null;
        
        // Verificar datos críticos
        if (empty($rutina_id) || empty($nom)) {
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
     * Obtiene estadísticas sobre las rutinas
     * @return object Datos estadísticos
     */
    public function getRoutineStats() {
        $this->db->query('SELECT 
            COUNT(*) as total_routines,
            (SELECT COUNT(*) FROM exercicis) as total_exercises,
            (SELECT AVG(tmp.count) FROM (SELECT COUNT(*) as count FROM exercicis GROUP BY rutina_id) as tmp) as avg_exercises_per_routine,
            (SELECT COUNT(DISTINCT usuari_id) FROM rutines) as users_with_routines
        FROM rutines');
        
        return $this->db->single();
    }
    
    /**
     * Obtener las rutinas más populares basado en ejercicios
     * @param int $limit Límite de resultados a devolver
     * @return array Lista de rutinas populares
     */
    public function getPopularRoutines($limit = 5) {
        $this->db->query('SELECT r.*, 
            (SELECT COUNT(*) FROM exercicis e WHERE e.rutina_id = r.rutina_id) AS exercise_count,
            u.nom as user_name, u.cognoms as user_surname
            FROM rutines r
            JOIN usuaris u ON r.usuari_id = u.usuari_id
            ORDER BY exercise_count DESC, r.creat_el DESC
            LIMIT :limit');
            
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}