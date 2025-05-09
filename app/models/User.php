<?php
/**
 * Modelo para gestión de usuarios del sistema
 */

require_once dirname(__FILE__) . '/BaseModel.php';
require_once dirname(dirname(__FILE__)) . '/utils/Logger.php';

class User extends BaseModel {
    protected $table = 'usuaris';
    protected $primaryKey = 'usuari_id';
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Valida los datos de entrada para un usuario
     * @param array $data Datos a validar
     * @return array Array de errores o array vacío si no hay errores
     */
    public function validate($data) {
        $errors = [];
        
        // Validar correo electrónico
        if (empty($data['correu']) && empty($data['email'])) {
            $errors['correu'] = 'El correo electrónico es requerido';
        } else {
            $email = !empty($data['email']) ? $data['email'] : $data['correu'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['correu'] = 'El formato del correo electrónico no es válido';
            }
        }
        
        // Validar nombre
        if (empty($data['nom']) && (empty($data['fullName']) && empty($data['name']))) {
            $errors['nom'] = 'El nombre es requerido';
        }
        
        // Validar rol si está presente
        if (!empty($data['role']) && !in_array($data['role'], ['admin', 'staff', 'user'])) {
            $errors['role'] = 'El rol no es válido';
        }
          // Validar teléfono si está presente (debe tener entre 6 y 9 dígitos)
        if (!empty($data['phone']) && !preg_match('/^\d{6,9}$/', $data['phone'])) {
            $errors['phone'] = 'El formato del teléfono no es válido (debe tener entre 6 y 9 dígitos)';
        }
        
        // Validar fecha de nacimiento si está presente
        if (!empty($data['birthDate']) || !empty($data['data_naixement'])) {
            $birthDate = !empty($data['birthDate']) ? $data['birthDate'] : $data['data_naixement'];
            $format = 'Y-m-d';
            $d = \DateTime::createFromFormat($format, $birthDate);
            
            if (!($d && $d->format($format) === $birthDate)) {
                $errors['birthDate'] = 'El formato de fecha de nacimiento no es válido (YYYY-MM-DD)';
            }
        }
        
        // Validar contraseña en creación de usuario
        if (isset($data['_action']) && $data['_action'] === 'create' && empty($data['contrasenya']) && empty($data['password'])) {
            $errors['password'] = 'La contraseña es requerida';
        }
        
        return $errors;
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
        return $this->findOneBy(['correu' => $email]);
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
        
        $row = $this->db->single();
        
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Obtiene un usuario por su ID (alias para findById con retorno compatible)
     * @param int $id ID del usuario
     * @return object|bool Objeto con datos del usuario o false si no existe
     */
    public function getUserById($id) {
        return $this->findById($id);
    }
    
    /**
     * Cambia la contraseña de un usuario
     * @param int $userId ID del usuario
     * @param string $newPassword Nueva contraseña (sin encriptar)
     * @return bool Éxito o fracaso de la operación
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->updatePassword($userId, $hashedPassword);
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
        Logger::log('DEBUG', 'Iniciando creación de usuario: ' . ($userData['email'] ?? $userData['correu']));
        
        try {
            // Validar datos
            $userData['_action'] = 'create';
            $errors = $this->validate($userData);
            if (!empty($errors)) {
                Logger::log('ERROR', 'Validación fallida para creación de usuario: ' . json_encode($errors));
                return false;
            }
            
            // Separar nombre y apellidos del nombre completo
            $nombre = $userData['nom'] ?? '';
            $apellidos = $userData['cognoms'] ?? '';
            
            if (!empty($userData['fullName'])) {
                $fullNameParts = explode(' ', $userData['fullName'], 2);
                $nombre = $fullNameParts[0];
                $apellidos = isset($fullNameParts[1]) ? $fullNameParts[1] : '';
            }
            
            // Formatear correctamente la fecha de nacimiento para MySQL o NULL si está vacía
            $birthDate = !empty($userData['birthDate']) ? $userData['birthDate'] : 
                         (!empty($userData['data_naixement']) ? $userData['data_naixement'] : null);
            
            // Preparar datos para la inserción
            $insertData = [
                'correu' => $userData['email'] ?? $userData['correu'],
                'contrasenya' => $userData['password'] ?? $userData['contrasenya'],
                'nom' => $nombre,
                'cognoms' => $apellidos,
                'role' => $userData['role'] ?? 'user',
                'actiu' => $userData['isActive'] ?? $userData['actiu'] ?? true,
                'phone' => $userData['phone'] ?? null,
                'data_naixement' => $birthDate,
                'creat_el' => date('Y-m-d H:i:s')
            ];
            
            Logger::log('DEBUG', 'Ejecutando SQL para crear usuario');
            
            // Usar el método create de BaseModel
            $userId = parent::create($insertData);
            if ($userId) {
                Logger::log('INFO', 'Usuario creado correctamente con ID: ' . $userId);
                return $userId;
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
        $sql = 'SELECT usuari_id, usuari_id as id, correu, correu as email, 
                nom, cognoms, CONCAT(nom, " ", cognoms) as fullName,
                role, actiu as isActive, actiu, phone, data_naixement as birthDate,
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
    public function update($data) {
        try {
            // Validar datos
            $errors = $this->validate($data);
            if (!empty($errors)) {
                Logger::log('ERROR', 'Validación fallida para actualización de usuario: ' . json_encode($errors));
                return false;
            }
            
            // Extraer el ID y eliminar del array de datos
            $userId = $data[$this->primaryKey] ?? $data['id'] ?? null;
            if (!$userId) {
                Logger::log('ERROR', 'ID de usuario no proporcionado para actualización');
                return false;
            }
            
            // Preparar datos para actualización
            $updateData = [];
            
            // Mapear campos con nombres en inglés a nombres en español de la BD
            $fieldMap = [
                'email' => 'correu',
                'password' => 'contrasenya',
                'name' => 'nom',
                'lastname' => 'cognoms',
                'isActive' => 'actiu',
                'birthDate' => 'data_naixement'
            ];
            
            // Procesar campos estándar
            foreach ($data as $key => $value) {
                // Si el campo está en el mapa, usar el nombre en español
                if (array_key_exists($key, $fieldMap)) {
                    $updateData[$fieldMap[$key]] = $value;
                } 
                // Si no es un campo especial, añadirlo directamente
                else if (!in_array($key, ['id', $this->primaryKey, 'fullName'])) {
                    $updateData[$key] = $value;
                }
            }
            
            // Procesar nombre completo si está disponible
            if (isset($data['fullName'])) {
                $fullNameParts = explode(' ', $data['fullName'], 2);
                $updateData['nom'] = $fullNameParts[0];
                $updateData['cognoms'] = isset($fullNameParts[1]) ? $fullNameParts[1] : '';
            }
            
            // Añadir el ID para la actualización
            $updateData[$this->primaryKey] = $userId;
            
            // Usar el método update de BaseModel
            return parent::update($updateData);
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
        return $this->findBy(['role' => 'staff', 'actiu' => 1]);
    }

    /**
     * Obtiene todos los usuarios normales (rol 'user')
     * @param bool $activeOnly Si es true, devuelve solo usuarios activos
     * @return array Lista de usuarios normales
     */
    public function getAllNormalUsers($activeOnly = true) {
        if ($activeOnly) {
            return $this->findBy(['role' => 'user', 'actiu' => 1]);
        } else {
            return $this->findBy(['role' => 'user']);
        }
    }

    /**
     * Obtiene usuarios por rol específico
     * @param string $role El rol a filtrar ('user', 'staff', 'admin', etc.)
     * @return array Lista de usuarios con el rol especificado
     */
    public function getUsersByRole($role) {
        return $this->findBy(['role' => $role]);
    }

    /**
     * Elimina un usuario del sistema
     * Implementa una eliminación "soft delete" cambiando el estado a inactivo
     * 
     * @param int $userId ID del usuario a eliminar
     * @return bool True si se eliminó correctamente, False en caso contrario
     */
    public function deleteUser($userId) {
        try {
            // Verificamos primero si el usuario existe
            $user = $this->getById($userId);
            if (!$user) {
                return false;
            }
            
            // En lugar de eliminar físicamente, marcamos como inactivo
            return $this->deactivate($userId);
            
            // Si realmente quisieras eliminar físicamente:
            // return $this->delete($userId);
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al eliminar usuario: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crea un registro de personal/monitor para un usuario
     * Usado cuando un usuario es ascendido a rol de staff
     * 
     * @param int $userId ID del usuario
     * @return bool True si se creó correctamente, False en caso contrario
     */
    public function createStaffRecord($userId) {
        try {
            // Verificar si el usuario existe y tiene el rol correcto
            $user = $this->getById($userId);
            
            if (!$user || $user->role !== 'staff') {
                return false;
            }
            
            // Verificar si ya existe un registro en la tabla de asignación de monitores
            $this->db->query('SELECT COUNT(*) as count FROM assignacio_monitors WHERE usuari_id = :userId OR monitor_id = :monitorId');
            $this->db->bind(':userId', $userId);
            $this->db->bind(':monitorId', $userId);
            
            $result = $this->db->single();
            
            if ($result->count > 0) {
                // Ya existe un registro, no es necesario crear otro
                return true;
            }
            
            // Crear registro en la tabla de asignación de monitores
            $this->db->query('INSERT INTO assignacio_monitors (usuari_id, monitor_id, data_assignacio) VALUES (:userId, :userId, NOW())');
            $this->db->bind(':userId', $userId);
            
            return $this->db->execute();
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al crear registro de staff: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asegura que un usuario con rol 'staff' tenga un registro en la tabla de personal
     * Si no existe, lo crea
     * 
     * @param int $userId ID del usuario
     * @return bool True si existe o se creó correctamente, False en caso contrario
     */
    public function ensureStaffRecord($userId) {
        // Verificar si el usuario tiene rol de staff
        $user = $this->getById($userId);
        
        if (!$user || $user->role !== 'staff') {
            return false;
        }
        
        // Verificar si ya existe un registro en la tabla de asignación
        $this->db->query('SELECT COUNT(*) as count FROM assignacio_monitors WHERE usuari_id = :userId OR monitor_id = :monitorId');
        $this->db->bind(':userId', $userId);
        $this->db->bind(':monitorId', $userId);
        
        $result = $this->db->single();
        
        if ($result->count > 0) {
            // Ya existe un registro
            return true;
        }
        
        // Si no existe, crear uno nuevo
        return $this->createStaffRecord($userId);
    }

    /**
     * Actualiza un usuario con datos provenientes del formulario con nombres en inglés
     * Este método es un wrapper que adapta la estructura de datos del formulario a los campos de la BDD
     * 
     * @param array $userData Datos del usuario con nombres en inglés
     * @return bool True si se actualizó correctamente, False en caso contrario
     */
    public function updateUser($userData) {
        return $this->update($userData);
    }

    /**
     * Actualiza el perfil del usuario (método específico para la actualización de perfil)
     * @param array $data Datos del perfil a actualizar
     * @return bool Éxito o fracaso de la operación
     */
    public function updateProfile($data) {
        return $this->update($data);
    }

    /**
     * Cuenta el número total de usuarios en el sistema.
     * @return int Número total de usuarios
     */
    public function countUsers() {
        $this->db->query('SELECT COUNT(*) as total FROM usuaris');
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Cuenta el número de usuarios por rol.
     * @param string $role Rol de usuario (admin, staff, user)
     * @return int Número de usuarios con el rol especificado
     */
    public function countUsersByRole($role) {
        $this->db->query('SELECT COUNT(*) as total FROM usuaris WHERE role = :role');
        $this->db->bind(':role', $role);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }
}
?>
