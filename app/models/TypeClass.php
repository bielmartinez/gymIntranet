<?php
/**
 * Model for managing class types
 */
class TypeClass {
    private $db;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Get all class types
     * 
     * @return array Array of class types
     */
    public function getAll() {
        $this->db->query('SELECT * FROM tipus_classes ORDER BY nom');
        return $this->db->resultSet();
    }

    /**
     * Get class type by ID
     * 
     * @param int $id Class type ID
     * @return object Class type object
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM tipus_classes WHERE tipus_classe_id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Add a new class type
     * 
     * @param array $data Class type data
     * @return bool True if successful, false otherwise
     */
    public function add($data) {
        $this->db->query('INSERT INTO tipus_classes (nom, descripcio, color) VALUES (:nom, :descripcio, :color)');
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio']);
        $this->db->bind(':color', $data['color'] ?? 'primary');

        return $this->db->execute();
    }

    /**
     * Update a class type
     * 
     * @param array $data Class type data
     * @return bool True if successful, false otherwise
     */
    public function update($data) {
        $this->db->query('UPDATE tipus_classes SET nom = :nom, descripcio = :descripcio, color = :color WHERE tipus_classe_id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':descripcio', $data['descripcio']);
        $this->db->bind(':color', $data['color'] ?? 'primary');

        return $this->db->execute();
    }

    /**
     * Delete a class type
     * 
     * @param int $id Class type ID
     * @return bool True if successful, false otherwise
     */
    public function delete($id) {
        $this->db->query('DELETE FROM tipus_classes WHERE tipus_classe_id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
}