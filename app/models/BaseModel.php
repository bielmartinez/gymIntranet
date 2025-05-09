<?php
/**
 * Modelo base que proporciona funcionalidad común para todos los modelos
 */

// Cargar configuración y base de datos
require_once dirname(dirname(__FILE__)) . '/config/config.php';
require_once dirname(dirname(__FILE__)) . '/libraries/Database.php';

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey;
    
    /**
     * Constructor del modelo base
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Obtener un registro por su ID
     * @param int $id ID del registro
     * @return object|bool Registro o false si no existe
     */
    public function getById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id);
        
        $row = $this->db->single();
        
        return $this->db->rowCount() > 0 ? $row : false;
    }
    
    /**
     * Obtener todos los registros
     * @return array Registros
     */
    public function getAll() {
        $this->db->query("SELECT * FROM {$this->table}");
        return $this->db->resultSet();
    }
    
    /**
     * Crear un nuevo registro
     * @param array $data Datos del registro
     * @return int|bool ID del registro creado o false en caso de error
     */
    public function create($data) {
        // Preparar la consulta
        $fields = array_keys($data);
        $fieldsString = implode(', ', $fields);
        $placeholders = ':' . implode(', :', $fields);
        
        $this->db->query("INSERT INTO {$this->table} ({$fieldsString}) VALUES ({$placeholders})");
        
        // Vincular los valores
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        // Ejecutar
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Actualizar un registro
     * @param array $data Datos actualizados (debe incluir el ID)
     * @return bool Éxito o fracaso de la operación
     */
    public function update($data) {
        // Preparar la consulta
        $setParts = [];
        foreach (array_keys($data) as $key) {
            if ($key !== $this->primaryKey) {
                $setParts[] = "{$key} = :{$key}";
            }
        }
        
        $setString = implode(', ', $setParts);
        
        $this->db->query("UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = :{$this->primaryKey}");
        
        // Vincular los valores
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        // Ejecutar
        return $this->db->execute();
    }
    
    /**
     * Eliminar un registro
     * @param int $id ID del registro a eliminar
     * @return bool Éxito o fracaso de la operación
     */
    public function delete($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Validar datos de entrada
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        // Por defecto, no hay validación en el modelo base
        // Este método debe ser sobrescrito por clases hijas cuando sea necesario
        return [];
    }
    
    /**
     * Busca registros que coincidan con los criterios proporcionados
     * @param array $criteria Criterios de búsqueda (campo => valor)
     * @param string $operator Operador para la condición (AND, OR)
     * @return array Registros que coinciden
     */
    public function findBy($criteria, $operator = 'AND') {
        $whereClauses = [];
        foreach (array_keys($criteria) as $field) {
            $whereClauses[] = "{$field} = :{$field}";
        }
        
        $whereString = implode(" {$operator} ", $whereClauses);
        
        $this->db->query("SELECT * FROM {$this->table} WHERE {$whereString}");
        
        foreach ($criteria as $field => $value) {
            $this->db->bind(':' . $field, $value);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Busca un único registro que coincida con los criterios proporcionados
     * @param array $criteria Criterios de búsqueda (campo => valor)
     * @return object|bool Registro o false si no existe
     */
    public function findOneBy($criteria) {
        $results = $this->findBy($criteria);
        return !empty($results) ? $results[0] : false;
    }
    
    /**
     * Cuenta el número de registros que coinciden con los criterios
     * @param array $criteria Criterios de búsqueda (campo => valor)
     * @return int Número de registros
     */
    public function count($criteria = []) {
        if (empty($criteria)) {
            $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        } else {
            $whereClauses = [];
            foreach (array_keys($criteria) as $field) {
                $whereClauses[] = "{$field} = :{$field}";
            }
            
            $whereString = implode(" AND ", $whereClauses);
            
            $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE {$whereString}");
            
            foreach ($criteria as $field => $value) {
                $this->db->bind(':' . $field, $value);
            }
        }
        
        $result = $this->db->single();
        return $result ? $result->count : 0;
    }
}
