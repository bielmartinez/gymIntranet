<?php
/**
 * Model for managing class types
 * 
 * @property int $id ID del tipo de clase (tipus_classe_id)
 * @property string $name Nombre del tipo de clase (nom)
 * @property string $description Descripción del tipo de clase (descripcio)
 * @property string $color Color para mostrar el tipo de clase (color)
 */

require_once dirname(__FILE__) . '/BaseModel.php';

class TypeClass extends BaseModel {
    protected $table = 'tipus_classes';
    protected $primaryKey = 'tipus_classe_id';
    
    // Mapeo de campos para compatibilidad entre español e inglés
    protected $fieldMapping = [
        'id' => 'tipus_classe_id',
        'name' => 'nom',
        'description' => 'descripcio',
        'color' => 'color'
    ];
    
    // Propiedades para compatibilidad con código existente
    private $id;
    private $name;
    private $description;
    private $color;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Valida los datos de entrada para un tipo de clase
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        $errors = [];
        
        // Mapear nombres de campos en inglés a español si existen
        $name = $data['nom'] ?? $data['name'] ?? null;
        
        // Validar nombre
        if (empty($name)) {
            $errors['nom'] = 'El nombre del tipo de clase es requerido';
        } elseif (strlen($name) > 50) {
            $errors['nom'] = 'El nombre no puede exceder los 50 caracteres';
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
    
    public function getColor() {
        return $this->color;
    }
    
    public function setColor($color) {
        $this->color = $color;
    }    /**
     * Get all class types
     * 
     * @return array Array of class types
     */
    public function getAll() {
        return parent::getAll('nom');
    }

    /**
     * Get all class types (alias for getAll method)
     * For compatibility with AdminController
     * 
     * @return array Array of class types
     */
    public function getAllTypes() {
        return $this->getAll();
    }

    /**
     * Get class type by ID
     * 
     * @param int $id Class type ID
     * @return object Class type object
     */
    public function findById($id) {
        $typeClass = parent::getById($id);
        
        if ($typeClass) {
            $this->id = $typeClass->tipus_classe_id;
            $this->name = $typeClass->nom;
            $this->description = $typeClass->descripcio;
            $this->color = $typeClass->color;
        }
        
        return $typeClass;
    }
    
    /**
     * Alias para mantener compatibilidad
     * @param int $id Class type ID
     * @return object Class type object
     */
    public function getById($id) {
        return $this->findById($id);
    }
    
    /**
     * Create a new class type
     * 
     * @param array $data Class type data
     * @return bool|int ID of the created record or false on error
     */
    public function create($data = null) {
        // Si no se proporcionan datos, usar los valores del objeto
        if ($data === null) {
            $data = [
                'nom' => $this->name,
                'descripcio' => $this->description,
                'color' => $this->color ?? 'primary'
            ];
        }
        
        // Aplicar mapeo de campo si es necesario
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
        $this->db->query('INSERT INTO tipus_classes (nom, descripcio, color) VALUES (:nom, :descripcio, :color)');
        
        // Vincular valores
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio'] ?? '');
        $this->db->bind(':color', $data['color'] ?? 'primary');
        
        // Ejecutar
        if ($this->db->execute()) {
            $typeId = $this->db->lastInsertId();
            
            // Actualizar propiedades del objeto
            $this->id = $typeId;
            $this->name = $data['nom'];
            $this->description = $data['descripcio'] ?? '';
            $this->color = $data['color'] ?? 'primary';
            
            return $typeId;
        }
        
        return false;
    }
    
    /**
     * Alias para create, mantiene compatibilidad
     */
    public function add($data) {
        return $this->create($data);
    }
    
    /**
     * Update a class type
     * 
     * @param array $data Class type data
     * @return bool True if successful, false otherwise
     */
    public function update($data = null) {
        // Si no se proporcionan datos, usar los valores del objeto
        if ($data === null) {
            $data = [
                'id' => $this->id,
                'nom' => $this->name,
                'descripcio' => $this->description,
                'color' => $this->color ?? 'primary'
            ];
        }
        
        // Asegurarse de que tenemos un ID
        if (!isset($data['id']) && !isset($data['tipus_classe_id']) && $this->id) {
            $data['id'] = $this->id;
        } else if (isset($data['tipus_classe_id']) && !isset($data['id'])) {
            $data['id'] = $data['tipus_classe_id'];
        }
        
        if (!isset($data['id'])) {
            return false;
        }
        
        // Aplicar mapeo de campo si es necesario
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
        $this->db->query('UPDATE tipus_classes SET nom = :nom, descripcio = :descripcio, color = :color 
                         WHERE tipus_classe_id = :id');
        
        // Vincular valores
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio'] ?? '');
        $this->db->bind(':color', $data['color'] ?? 'primary');
        
        // Ejecutar
        $success = $this->db->execute();
        
        if ($success) {
            // Actualizar propiedades del objeto
            $this->id = $data['id'];
            $this->name = $data['nom'];
            $this->description = $data['descripcio'] ?? '';
            $this->color = $data['color'] ?? 'primary';
        }
        
        return $success;
    }
    
    /**
     * Delete a class type
     * 
     * @param int $id Class type ID
     * @return bool True if successful, false otherwise
     */
    public function delete($id = null) {
        if ($id !== null) {
            $this->id = $id;
        }
        
        if (!$this->id) {
            return false;
        }
        
        return parent::delete($this->id);
    }
    
    /**
     * Verificar si el tipo de clase está siendo utilizado en alguna clase
     * 
     * @param int $id Type class ID
     * @return bool True si está en uso, false de lo contrario
     */
    public function isInUse($id = null) {
        if ($id === null) {
            $id = $this->id;
        }
        
        if (!$id) {
            return false;
        }
        
        $this->db->query('SELECT COUNT(*) as count FROM classes WHERE tipus_classe_id = :id');
        $this->db->bind(':id', $id);
        
        $result = $this->db->single();
        return $result->count > 0;
    }
    
    /**
     * Obtener tipos de clase con la cantidad de clases asociadas
     * 
     * @return array Array de tipos de clase con estadísticas
     */
    public function getTypesWithStats() {
        $this->db->query('SELECT t.*, 
                         (SELECT COUNT(*) FROM classes c WHERE c.tipus_classe_id = t.tipus_classe_id) as class_count
                         FROM tipus_classes t
                         ORDER BY t.nom');
        
        return $this->db->resultSet();
    }
}