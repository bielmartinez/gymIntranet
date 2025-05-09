<?php
/**
 * Modelo para gestión de notificaciones del sistema
 */

require_once dirname(__FILE__) . '/BaseModel.php';
require_once dirname(dirname(__FILE__)) . '/utils/Logger.php';

class Notification extends BaseModel {
    protected $table = 'notificacions';
    protected $primaryKey = 'notificacio_id';
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Valida los datos de entrada para una notificación
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        $errors = [];
        
        // Validar título
        if (empty($data['titol']) && empty($data['title'])) {
            $errors['titol'] = 'El título es requerido';
        } elseif (!empty($data['titol']) && strlen($data['titol']) > 100) {
            $errors['titol'] = 'El título no puede exceder los 100 caracteres';
        } elseif (!empty($data['title']) && strlen($data['title']) > 100) {
            $errors['title'] = 'El título no puede exceder los 100 caracteres';
        }
        
        // Validar mensaje
        if (empty($data['missatge']) && empty($data['message'])) {
            $errors['missatge'] = 'El mensaje es requerido';
        }
        
        return $errors;
    }

    /**
     * Crea una nueva notificación
     * @param array $notificationData Datos básicos de la notificación
     * @return int|bool ID de la notificación creada o false si hay error
     */
    public function createNotification($notificationData) {
        Logger::log('DEBUG', 'Iniciando creación de notificación: ' . 
                   ($notificationData['title'] ?? $notificationData['titol']));
        
        try {
            // Validar datos
            $errors = $this->validate($notificationData);
            if (!empty($errors)) {
                Logger::log('ERROR', 'Validación fallida para la notificación: ' . json_encode($errors));
                return false;
            }
            
            // Preparar datos para la inserción
            $insertData = [
                'titol' => $notificationData['title'] ?? $notificationData['titol'],
                'missatge' => $notificationData['message'] ?? $notificationData['missatge'],
                'creat_el' => date('Y-m-d H:i:s')
            ];
            
            // Si hay datos adicionales, los añadimos
            if (isset($notificationData['clase_id']) || isset($notificationData['classe_id'])) {
                $insertData['classe_id'] = $notificationData['clase_id'] ?? $notificationData['classe_id'];
            }
            
            if (isset($notificationData['emisor_id']) || isset($notificationData['sender_id'])) {
                $insertData['emisor_id'] = $notificationData['emisor_id'] ?? $notificationData['sender_id'];
            }
            
            // Usar el método create de BaseModel
            $notificationId = parent::create($insertData);
            
            if ($notificationId) {
                Logger::log('INFO', 'Notificación creada correctamente con ID: ' . $notificationId);
                return $notificationId;
            } else {
                Logger::log('ERROR', 'Error al ejecutar SQL para crear notificación');
                return false;
            }
        } catch (Exception $e) {
            Logger::log('ERROR', 'Excepción al crear notificación: ' . $e->getMessage());
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
        // Reutilizamos getAllNotifications ya que por ahora hacen lo mismo
        return $this->getAllNotifications();
    }
    
    /**
     * Obtiene el número de notificaciones disponibles
     * @return int Número de notificaciones
     */
    public function getNotificationsCount() {
        try {
            return $this->count();
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
    public function deleteNotification($notificationId) {
        return $this->delete($notificationId);
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
    
    /**
     * Marca una notificación como leída para un usuario específico
     * @param int $notificationId ID de la notificación
     * @param int $userId ID del usuario
     * @return bool Éxito de la operación
     */
    public function markAsRead($notificationId, $userId) {
        try {
            // Primero verificamos si ya existe un registro para este usuario y notificación
            $this->db->query('SELECT * FROM destinataris_notificacions
                            WHERE notificacio_id = :notification_id 
                            AND usuari_id = :user_id');
            $this->db->bind(':notification_id', $notificationId);
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();
            
            if ($this->db->rowCount() > 0) {
                // Si ya existe, actualizamos la marca de tiempo de lectura
                $this->db->query('UPDATE destinataris_notificacions 
                                SET llegit_el = NOW() 
                                WHERE notificacio_id = :notification_id 
                                AND usuari_id = :user_id 
                                AND llegit_el IS NULL');
            } else {
                // Si no existe, creamos un nuevo registro con marca de tiempo
                $this->db->query('INSERT INTO destinataris_notificacions 
                                (notificacio_id, usuari_id, llegit_el) 
                                VALUES (:notification_id, :user_id, NOW())');
            }
            
            $this->db->bind(':notification_id', $notificationId);
            $this->db->bind(':user_id', $userId);
            
            return $this->db->execute();
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al marcar notificación como leída: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Descarta una notificación para un usuario (la marca como leída sin verla)
     * @param int $notificationId ID de la notificación
     * @param int $userId ID del usuario
     * @return bool Éxito de la operación
     */
    public function dismissNotification($notificationId, $userId) {
        // Reutilizamos el método markAsRead ya que el comportamiento es el mismo
        return $this->markAsRead($notificationId, $userId);
    }
    
    /**
     * Obtiene las notificaciones no leídas para un usuario específico
     * @param int $userId ID del usuario
     * @return array Lista de notificaciones no leídas
     */
    public function getUnreadNotifications($userId) {
        try {
            $this->db->query('SELECT n.notificacio_id as id, n.titol as title, n.missatge as message, 
                            n.creat_el as created_at, n.classe_id, n.emisor_id
                            FROM notificacions n
                            LEFT JOIN destinataris_notificacions dn 
                                ON n.notificacio_id = dn.notificacio_id AND dn.usuari_id = :user_id
                            WHERE dn.llegit_el IS NULL OR dn.llegit_el IS NULL
                            ORDER BY n.creat_el DESC');
            $this->db->bind(':user_id', $userId);
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al obtener notificaciones no leídas: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cuenta el número de notificaciones no leídas para un usuario
     * @param int $userId ID del usuario
     * @return int Número de notificaciones no leídas
     */
    public function countUnreadNotifications($userId) {
        try {
            $this->db->query('SELECT COUNT(*) as count
                            FROM notificacions n
                            LEFT JOIN destinataris_notificacions dn 
                                ON n.notificacio_id = dn.notificacio_id AND dn.usuari_id = :user_id
                            WHERE dn.llegit_el IS NULL OR dn.llegit_el IS NULL');
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();
            
            return $result->count;
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al contar notificaciones no leídas: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Sobreescribe el método delete para eliminar primero las relaciones en destinataris_notificacions
     * @param int $id ID de la notificación a eliminar
     * @return bool Éxito o fracaso de la operación
     */
    public function delete($id) {
        try {
            Logger::log('DEBUG', 'Iniciando eliminación de notificación con ID: ' . $id);
            
            // Primero eliminar los registros relacionados en destinataris_notificacions
            $this->db->query("DELETE FROM destinataris_notificacions WHERE notificacio_id = :id");
            $this->db->bind(':id', $id);
            $deleteDestRecords = $this->db->execute();
            
            if (!$deleteDestRecords) {
                Logger::log('ERROR', 'Error al eliminar registros de destinatarios de la notificación ID: ' . $id);
                return false;
            }
            
            Logger::log('INFO', 'Registros de destinatarios eliminados correctamente para la notificación ID: ' . $id);
            
            // Ahora eliminar la notificación principal
            return parent::delete($id);
        } catch (Exception $e) {
            Logger::log('ERROR', 'Excepción al eliminar notificación ID: ' . $id . ' - ' . $e->getMessage());
            return false;
        }
    }
}
?>