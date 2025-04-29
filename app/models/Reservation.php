<?php
/**
 * Modelo para gestión de reservas
 */
class Reservation {
    private $id;
    private $userId;
    private $resourceType; // 'class' o 'court'
    private $resourceId;
    private $date;
    private $startTime;
    private $endTime;
    private $status; // 'pending', 'confirmed', 'cancelled'
    
    // Aquí irán los métodos para CRUD de reservas
    // getters, setters, y métodos para interactuar con la base de datos
}
?>
