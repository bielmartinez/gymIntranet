<?php
/**
 * Modelo para gestión de pistas deportivas
 */
class Court {
    private $id;
    private $name;
    private $type; // tenis, pádel, etc.
    private $location;
    private $isAvailable;
    private $pricePerHour;
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database;
    }
    
    /**
     * Obtener todas las pistas
     */
    public function getAllCourts() {
        $this->db->query('SELECT * FROM courts');
        return $this->db->resultSet();
    }
    
    /**
     * Obtener pista por ID
     */
    public function getCourtById($id) {
        $this->db->query('SELECT * FROM courts WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Obtener pistas por tipo
     */
    public function getCourtsByType($type) {
        $this->db->query('SELECT * FROM courts WHERE type = :type');
        $this->db->bind(':type', $type);
        return $this->db->resultSet();
    }
    
    /**
     * Verificar disponibilidad de pista
     */
    public function isCourtAvailable($courtId, $date, $startTime, $endTime) {
        // Verificar si hay reservas que se superpongan con el horario solicitado
        $this->db->query('SELECT * FROM reservations 
                         WHERE court_id = :court_id 
                         AND reservation_date = :date 
                         AND ((start_time <= :start_time AND end_time > :start_time) 
                         OR (start_time < :end_time AND end_time >= :end_time)
                         OR (start_time >= :start_time AND end_time <= :end_time))');
        
        $this->db->bind(':court_id', $courtId);
        $this->db->bind(':date', $date);
        $this->db->bind(':start_time', $startTime);
        $this->db->bind(':end_time', $endTime);
        
        $results = $this->db->resultSet();
        
        // Si no hay resultados, la pista está disponible
        return empty($results);
    }
    
    // Getters y Setters
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
    
    public function getType() {
        return $this->type;
    }
    
    public function setType($type) {
        $this->type = $type;
    }
    
    public function getLocation() {
        return $this->location;
    }
    
    public function setLocation($location) {
        $this->location = $location;
    }
    
    public function getIsAvailable() {
        return $this->isAvailable;
    }
    
    public function setIsAvailable($isAvailable) {
        $this->isAvailable = $isAvailable;
    }
    
    public function getPricePerHour() {
        return $this->pricePerHour;
    }
    
    public function setPricePerHour($pricePerHour) {
        $this->pricePerHour = $pricePerHour;
    }
}
?>
