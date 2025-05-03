<?php
/**
 * Modelo para gestión de notificaciones del sistema (versión simplificada)
 */

// Cargar configuración y base de datos
require_once dirname(dirname(__FILE__)) . '/config/config.php';
require_once dirname(dirname(__FILE__)) . '/libraries/Database.php';
require_once dirname(dirname(__FILE__)) . '/utils/Logger.php';

class Notification {
    private $db;
    
    // Constructor
    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Crea una nueva notificación (versión simplificada)
     * @param array $notificationData Datos básicos de la notificación
     * @return int|bool ID de la notificación creada o false si hay error
     */
    public function create($notificationData) {
        Logger::log('DEBUG', 'Iniciando creación de notificación simplificada: ' . $notificationData['title']);
        
        try {
            // Insertar la notificación en la tabla notificacions (versión simplificada)
            $this->db->query('INSERT INTO notificacions (titol, missatge, creat_el) 
                            VALUES (:title, :message, NOW())');
            
            $this->db->bind(':title', $notificationData['title']);
            $this->db->bind(':message', $notificationData['message']);
            
            if($this->db->execute()) {
                $notificationId = $this->db->lastInsertId();
                Logger::log('INFO', 'Notificación creada correctamente con ID: ' . $notificationId);
                return $notificationId;
            } else {
                Logger::log('ERROR', 'Error al ejecutar SQL para crear notificación');
                return false;
            }
        } catch (Exception $e) {
            Logger::log('ERROR', 'Excepción al crear notificación simplificada: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todas las notificaciones
     * @return array Lista de notificaciones
     */
    public function getAllNotifications() {
        try {
            $this->db->query('SELECT notificacio_id as id, titol as title, missatge as message, 
                            creat_el as created_at
                            FROM notificacions 
                            ORDER BY creat_el DESC');
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al obtener todas las notificaciones: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene las notificaciones para mostrar al usuario
     * @return array Lista de notificaciones
     */
    public function getUserNotifications() {
        try {
            $this->db->query('SELECT notificacio_id as id, titol as title, missatge as message, 
                            creat_el as created_at
                            FROM notificacions 
                            ORDER BY creat_el DESC');
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al obtener notificaciones: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene el número de notificaciones disponibles
     * @return int Número de notificaciones
     */
    public function getNotificationsCount() {
        try {
            $this->db->query('SELECT COUNT(*) as count FROM notificacions');
            $result = $this->db->single();
            return $result->count;
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al obtener conteo de notificaciones: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Elimina una notificación
     * @param int $notificationId ID de la notificación
     * @return bool
     */
    public function delete($notificationId) {
        try {
            $this->db->query('DELETE FROM notificacions WHERE notificacio_id = :id');
            $this->db->bind(':id', $notificationId);
            return $this->db->execute();
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al eliminar notificación: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el detalle de una notificación específica
     * @param int $notificationId ID de la notificación
     * @return object|bool Datos de la notificación o false si no existe
     */
    public function getNotificationById($notificationId) {
        try {
            $this->db->query('SELECT notificacio_id as id, titol as title, missatge as message, 
                            creat_el as created_at
                            FROM notificacions 
                            WHERE notificacio_id = :id');
            $this->db->bind(':id', $notificationId);
            
            $result = $this->db->single();
            
            if($this->db->rowCount() > 0){
                return $result;
            } else {
                return false;
            }
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al obtener detalle de notificación: ' . $e->getMessage());
            return false;
        }
    }
}