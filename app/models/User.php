<?php
/**
 * Modelo para gestión de usuarios del sistema
 */

// Cargar configuración y base de datos
require_once dirname(dirname(__FILE__)) . '/config/config.php';
require_once dirname(dirname(__FILE__)) . '/libraries/Database.php';

class User {
    private $db;
    
    // Constructor
    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Encuentra un usuario por su nombre de usuario (correo electrónico)
     * En ProvaGym, usamos el correo como nombre de usuario
     */
    public function findByUsername($username) {
        // Intentar con la columna 'role' que es el nombre más común en inglés
        $this->db->query('SELECT usuari_id as id, correu as email, contrasenya as password, 
                        CONCAT(nom, " ", cognoms) as fullName,
                        role as role, actiu as isActive
                        FROM usuaris WHERE correu = :username');
        $this->db->bind(':username', $username);
        
        $row = $this->db->singleArray();
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            // Si no encuentra, intentar con la columna 'user_role'
            $this->db->query('SELECT usuari_id as id, correu as email, contrasenya as password, 
                            CONCAT(nom, " ", cognoms) as fullName,
                            user_role as role, actiu as isActive
                            FROM usuaris WHERE correu = :username');
            $this->db->bind(':username', $username);
            
            $row = $this->db->singleArray();
            
            if($this->db->rowCount() > 0){
                return $row;
            } else {
                return false;
            }
        }
    }

    /**
     * Encuentra un usuario por su correo electrónico
     */
    public function findByEmail($email) {
        $this->db->query('SELECT usuari_id as id, correu as email, contrasenya as password, 
                        CONCAT(nom, " ", cognoms) as fullName,
                        role as role, actiu as isActive
                        FROM usuaris WHERE correu = :email');
        $this->db->bind(':email', $email);
        
        $row = $this->db->singleArray();
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Encuentra un usuario por su ID
     */
    public function findById($id) {
        $this->db->query('SELECT usuari_id as id, correu as email, contrasenya as password, 
                        CONCAT(nom, " ", cognoms) as fullName,
                        role as role, actiu as isActive
                        FROM usuaris WHERE usuari_id = :id');
        $this->db->bind(':id', $id);
        
        $row = $this->db->singleArray();
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Guarda un token de restablecimiento de contraseña
     * Nota: Necesitaríamos añadir una columna 'reset_token' a la tabla usuaris
     */
    public function savePasswordResetToken($userId, $token) {
        $this->db->query('UPDATE usuaris SET reset_token = :token, 
                        reset_token_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) 
                        WHERE usuari_id = :id');
        $this->db->bind(':token', $token);
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    /**
     * Encuentra un usuario por su token de restablecimiento
     */
    public function findByResetToken($token) {
        $this->db->query('SELECT usuari_id as id, correu as email, reset_token as token, 
                        UNIX_TIMESTAMP(reset_token_expiration) as token_expiration 
                        FROM usuaris WHERE reset_token = :token');
        $this->db->bind(':token', $token);
        
        $row = $this->db->singleArray();
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Verifica si un token es válido
     */
    public function isTokenValid($token) {
        $user = $this->findByResetToken($token);
        return $user && $user['token_expiration'] > time();
    }

    /**
     * Actualiza la contraseña de un usuario
     */
    public function updatePassword($userId, $hashedPassword) {
        $this->db->query('UPDATE usuaris SET contrasenya = :password WHERE usuari_id = :id');
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    /**
     * Limpia el token de restablecimiento de contraseña
     */
    public function clearResetToken($userId) {
        $this->db->query('UPDATE usuaris SET reset_token = NULL, reset_token_expiration = NULL WHERE usuari_id = :id');
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    /**
     * Crea un nuevo usuario
     */
    public function create($userData) {
        // Añadir logging para depuración
        Logger::log('DEBUG', 'Iniciando creación de usuario: ' . $userData['email']);
        
        try {
            // Separar nombre y apellidos del nombre completo
            $fullNameParts = explode(' ', $userData['fullName'], 2);
            $nombre = $fullNameParts[0];
            $apellidos = isset($fullNameParts[1]) ? $fullNameParts[1] : '';
            
            // Formatear correctamente la fecha de nacimiento para MySQL o NULL si está vacía
            $birthDate = !empty($userData['birthDate']) ? $userData['birthDate'] : null;
            
            // Verificar qué columnas existen realmente en la tabla usuaris
            Logger::log('DEBUG', 'Obteniendo estructura de la tabla usuaris');
            
            // Intentar con la consulta más simple posible
            try {
                $this->db->query('INSERT INTO usuaris (correu, contrasenya, nom, cognoms, actiu, role, creat_el) 
                                VALUES (:email, :password, :nom, :cognoms, :actiu, :role, NOW())');
                
                $this->db->bind(':email', $userData['email']);
                $this->db->bind(':password', $userData['password']);
                $this->db->bind(':nom', $nombre);
                $this->db->bind(':cognoms', $apellidos);
                $this->db->bind(':actiu', true);
                $this->db->bind(':role', $userData['role']);
                
                Logger::log('DEBUG', 'Ejecutando SQL para crear usuario con campos mínimos');
                
                if($this->db->execute()) {
                    $lastId = $this->db->lastInsertId();
                    Logger::log('INFO', 'Usuario creado correctamente con ID: ' . $lastId);
                    return $lastId;
                } else {
                    Logger::log('ERROR', 'Error al ejecutar SQL para crear usuario con campos mínimos');
                    return false;
                }
            } catch (PDOException $e) {
                Logger::log('ERROR', 'Excepción PDO al crear usuario con campos mínimos: ' . $e->getMessage());
                return false;
            }
        } catch (Exception $e) {
            Logger::log('ERROR', 'Excepción general al crear usuario: ' . $e->getMessage());
            return false;
        }
        
        Logger::log('ERROR', 'Error al ejecutar SQL para crear usuario');
        return false;
    }
}
