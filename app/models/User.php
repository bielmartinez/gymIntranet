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
        $this->db->query('SELECT usuari_id as id, correu as email, contrasenya as password, 
                        CONCAT(nom, " ", cognoms) as fullName,
                        role as role, actiu as isActive, phone, data_naixement as birthDate
                        FROM usuaris WHERE correu = :username');
        $this->db->bind(':username', $username);
        
        $row = $this->db->singleArray();
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Encuentra un usuario por su correo electrónico
     */
    public function findByEmail($email) {
        $this->db->query('SELECT usuari_id as id, correu as email, contrasenya as password, 
                        CONCAT(nom, " ", cognoms) as fullName,
                        role as role, actiu as isActive, phone, data_naixement as birthDate
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
                        CONCAT(nom, " ", cognoms) as fullName, nom, cognoms,
                        role as role, actiu as isActive, phone, data_naixement as birthDate
                        FROM usuaris WHERE usuari_id = :id');
        $this->db->bind(':id', $id);
        
        $row = $this->db->single(); // Cambiado de singleArray() a single() para devolver un objeto
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Guarda un token de restablecimiento de contraseña
     */
    public function savePasswordResetToken($userId, $token) {
        $this->db->query('UPDATE usuaris SET token_recuperacio = :token, 
                        token_expiracio = DATE_ADD(NOW(), INTERVAL 1 HOUR),
                        token_creat = NOW() 
                        WHERE usuari_id = :id');
        $this->db->bind(':token', $token);
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    /**
     * Encuentra un usuario por su token de restablecimiento
     */
    public function findByResetToken($token) {
        $this->db->query('SELECT usuari_id as id, correu as email, token_recuperacio as token, 
                        UNIX_TIMESTAMP(token_expiracio) as token_expiration 
                        FROM usuaris WHERE token_recuperacio = :token');
        $this->db->bind(':token', $token);
        
        $row = $this->db->singleArray();
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Verifica si un token de restablecimiento es válido
     */
    public function isTokenValid($token) {
        $user = $this->findByResetToken($token);
        
        if (!$user) {
            return false;
        }
        
        // Verificar si el token ha expirado
        $tokenExpiration = $user['token_expiration'];
        $currentTime = time();
        
        return $tokenExpiration > $currentTime;
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
        $this->db->query('UPDATE usuaris SET token_recuperacio = NULL, token_expiracio = NULL, token_creat = NULL WHERE usuari_id = :id');
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
            
            // Crear el usuario con todos los campos disponibles
            $this->db->query('INSERT INTO usuaris (correu, contrasenya, nom, cognoms, 
                            role, actiu, phone, data_naixement, creat_el) 
                            VALUES (:email, :password, :nom, :cognoms, 
                            :role, :actiu, :phone, :birthDate, NOW())');
            
            $this->db->bind(':email', $userData['email']);
            $this->db->bind(':password', $userData['password']);
            $this->db->bind(':nom', $nombre);
            $this->db->bind(':cognoms', $apellidos);
            $this->db->bind(':role', $userData['role']);
            $this->db->bind(':actiu', true);
            $this->db->bind(':phone', $userData['phone'] ?? null);
            $this->db->bind(':birthDate', $birthDate);
            
            Logger::log('DEBUG', 'Ejecutando SQL para crear usuario');
            
            if($this->db->execute()) {
                $lastId = $this->db->lastInsertId();
                Logger::log('INFO', 'Usuario creado correctamente con ID: ' . $lastId);
                return $lastId;
            } else {
                Logger::log('ERROR', 'Error al ejecutar SQL para crear usuario');
                return false;
            }
        } catch (Exception $e) {
            Logger::log('ERROR', 'Excepción general al crear usuario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios
     * @param string $roleFilter Opcional: filtrar por rol
     * @return array Lista de usuarios
     */
    public function getAllUsers($roleFilter = null) {
        $sql = 'SELECT usuari_id as id, correu as email, 
                CONCAT(nom, " ", cognoms) as fullName,
                role, actiu as isActive, phone, data_naixement as birthDate,
                creat_el as createdAt, ultim_acces as lastAccess
                FROM usuaris';
        
        if ($roleFilter) {
            $sql .= ' WHERE role = :role';
        }
        
        $sql .= ' ORDER BY createdAt DESC';
        
        $this->db->query($sql);
        
        if ($roleFilter) {
            $this->db->bind(':role', $roleFilter);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Actualiza la información de un usuario
     */
    public function update($userId, $userData) {
        try {
            // Separar nombre y apellidos del nombre completo si existe
            if (isset($userData['fullName'])) {
                $fullNameParts = explode(' ', $userData['fullName'], 2);
                $userData['nom'] = $fullNameParts[0];
                $userData['cognoms'] = isset($fullNameParts[1]) ? $fullNameParts[1] : '';
            }
            
            // Construir la consulta dinámica
            $sql = 'UPDATE usuaris SET ';
            $params = [];
            
            // Añadir cada campo al SQL solo si está presente en userData
            if (isset($userData['nom'])) {
                $params[] = 'nom = :nom';
            }
            
            if (isset($userData['cognoms'])) {
                $params[] = 'cognoms = :cognoms';
            }
            
            if (isset($userData['email'])) {
                $params[] = 'correu = :email';
            }
            
            if (isset($userData['password'])) {
                $params[] = 'contrasenya = :password';
            }
            
            if (isset($userData['role'])) {
                $params[] = 'role = :role';
            }
            
            if (isset($userData['isActive'])) {
                $params[] = 'actiu = :actiu';
            }
            
            if (isset($userData['phone'])) {
                $params[] = 'phone = :phone';
            }
            
            if (isset($userData['birthDate'])) {
                $params[] = 'data_naixement = :birthDate';
            }
            
            // Combinar parámetros con comas
            $sql .= implode(', ', $params);
            $sql .= ' WHERE usuari_id = :id';
            
            $this->db->query($sql);
            
            // Vincular cada parámetro solo si está presente
            if (isset($userData['nom'])) {
                $this->db->bind(':nom', $userData['nom']);
            }
            
            if (isset($userData['cognoms'])) {
                $this->db->bind(':cognoms', $userData['cognoms']);
            }
            
            if (isset($userData['email'])) {
                $this->db->bind(':email', $userData['email']);
            }
            
            if (isset($userData['password'])) {
                $this->db->bind(':password', $userData['password']);
            }
            
            if (isset($userData['role'])) {
                $this->db->bind(':role', $userData['role']);
            }
            
            if (isset($userData['isActive'])) {
                $this->db->bind(':actiu', $userData['isActive']);
            }
            
            if (isset($userData['phone'])) {
                $this->db->bind(':phone', $userData['phone']);
            }
            
            if (isset($userData['birthDate'])) {
                $this->db->bind(':birthDate', $userData['birthDate']);
            }
            
            $this->db->bind(':id', $userId);
            
            return $this->db->execute();
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al actualizar usuario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la fecha de último acceso
     */
    public function updateLastAccess($userId) {
        $this->db->query('UPDATE usuaris SET ultim_acces = NOW() WHERE usuari_id = :id');
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    /**
     * Desactiva un usuario (soft delete)
     */
    public function deactivate($userId) {
        $this->db->query('UPDATE usuaris SET actiu = 0 WHERE usuari_id = :id');
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    /**
     * Activa un usuario
     */
    public function activate($userId) {
        $this->db->query('UPDATE usuaris SET actiu = 1 WHERE usuari_id = :id');
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    /**
     * Obtiene todos los usuarios que son monitores (staff)
     * @return array Lista de monitores
     */
    public function getAllMonitors() {
        $sql = 'SELECT usuari_id as personal_id, usuari_id, nom, cognoms, correu
                FROM usuaris 
                WHERE role = "staff" AND actiu = 1
                ORDER BY nom, cognoms';
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }
}
