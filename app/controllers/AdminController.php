<?php
/**
 * Controlador para la gestión de funciones administrativas
 */
// Incluir los modelos necesarios
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Class.php';
require_once dirname(__DIR__) . '/models/TypeClass.php';
require_once dirname(__DIR__) . '/models/Notification.php';

class AdminController {
    private $userController;
    private $userModel;
    private $classModel;
    
    public function __construct() {
        // Verificar que el usuario sea administrador
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT);
            exit;
        }
        
        $this->userController = new UserController();
        $this->userModel = new User();
        $this->classModel = new Class_();
    }
    
    /**
     * Página de inicio para administradores
     */
    public function index() {
        // Obtener estadísticas para el dashboard
        $totalUsers = count($this->userModel->getAllUsers());
        $activeUsers = count($this->userModel->getAllUsers('user'));
        $staffUsers = count($this->userModel->getAllUsers('staff'));
        
        $data = [
            'title' => 'Panel de Administración',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Administrador',
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'staffUsers' => $staffUsers
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar vista de dashboard para administradores
        include_once APPROOT . '/views/admin/dashboard.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Alias para index() - Dashboard para administradores
     * Agregado para manejar redirecciones desde login
     */
    public function dashboard() {
        // Simplemente redirigir a index()
        $this->index();
    }
    
    /**
     * Muestra el formulario de registro de usuarios
     */
    public function registerForm() {
        $data = [
            'title' => 'Registro de Usuario',
            'fullName' => '',
            'username' => '',
            'email' => '',
            'role' => '',
            'membershipType' => '',
            'phone' => '',
            'birthDate' => '',
            'fullName_err' => '',
            'username_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => '',
            'role_err' => '',
            'membership_err' => '',
            'phone_err' => '',
            'birthDate_err' => ''
        ];
        
        // Si hay errores de registro previos, cargarlos
        if(isset($_SESSION['register_errors'])) {
            $data = array_merge($data, $_SESSION['register_errors']);
            unset($_SESSION['register_errors']);
        }
        
        // Si hay datos de registro previos, cargarlos
        if(isset($_SESSION['register_data'])) {
            $data = array_merge($data, $_SESSION['register_data']);
            unset($_SESSION['register_data']);
        }
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/admin/register.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Procesa el registro de un nuevo usuario
     */
    public function register() {
        // Verificar si se envió el formulario
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/admin/registerForm');
            exit;
        }
        
        // Procesar datos del formulario
        $userData = [
            'fullName' => trim($_POST['fullName']),
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
            'confirmPassword' => trim($_POST['confirmPassword']),
            'role' => trim($_POST['role']),
            'membershipType' => isset($_POST['membershipType']) ? trim($_POST['membershipType']) : '',
            'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
            'birthDate' => isset($_POST['birthDate']) ? trim($_POST['birthDate']) : '',
            'sendWelcomeEmail' => isset($_POST['sendWelcomeEmail'])
        ];
        
        // Registrar usuario usando UserController
        $userId = $this->userController->register($userData);
        
        if($userId) {
            // Si el usuario es staff, crear entrada en la tabla personal
            if($userData['role'] === 'staff') {
                if($this->userModel->createStaffRecord($userId)) {
                    $_SESSION['admin_message'] = 'Monitor registrado correctamente y vinculado como personal';
                    $_SESSION['toast_message'] = 'Monitor registrado correctamente y vinculado como personal';
                    $_SESSION['toast_type'] = 'success';
                } else {
                    $_SESSION['admin_message'] = 'Usuario creado correctamente, pero hubo un error al vincularlo como personal';
                    $_SESSION['toast_message'] = 'Usuario creado correctamente, pero hubo un error al vincularlo como personal';
                    $_SESSION['toast_type'] = 'warning';
                }
            } else {
                $_SESSION['admin_message'] = 'Usuario creado correctamente';
                $_SESSION['toast_message'] = 'Usuario creado correctamente';
                $_SESSION['toast_type'] = 'success';
            }
            
            $_SESSION['admin_message_type'] = 'success';
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        } else {
            // Error - Volver al formulario
            $_SESSION['toast_message'] = 'Error al crear el usuario. Por favor revise los datos ingresados.';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/admin/registerForm');
            exit;
        }
    }
    
    /**
     * Lista todos los usuarios del sistema
     */
    public function users() {
        // Obtener todos los usuarios
        $users = $this->userModel->getAllUsers();
        
        $data = [
            'title' => 'Gestión de Usuarios',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Administrador',
            'users' => $users
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/admin/users.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Muestra el formulario para editar un usuario
     */
    public function editUser($userId = null) {
        // Si no se proporciona ID, redirigir a la lista de usuarios
        if(!$userId) {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
        
        // Obtener información del usuario
        $user = $this->userModel->findById($userId);
        
        if(!$user) {
            $_SESSION['admin_message'] = 'Usuario no encontrado';
            $_SESSION['admin_message_type'] = 'danger';
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
        
        $data = [
            'title' => 'Editar Usuario',
            'user' => $user
        ];
        
        // Si hay errores de edición previos, cargarlos
        if(isset($_SESSION['edit_errors'])) {
            $data['errors'] = $_SESSION['edit_errors'];
            unset($_SESSION['edit_errors']);
        }
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/admin/edit_user.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Actualiza los datos de un usuario
     */
    public function updateUser() {
        // Verificar si se envió el formulario
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
        
        // Procesar datos del formulario
        $userData = [
            'id' => $_POST['userId'],
            'fullName' => trim($_POST['fullName']),
            'email' => trim($_POST['email']),
            'role' => trim($_POST['role']),
            'status' => trim($_POST['status']),
            'membershipType' => isset($_POST['membershipType']) ? trim($_POST['membershipType']) : '',
            'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
            'birthDate' => isset($_POST['birthDate']) ? trim($_POST['birthDate']) : ''
        ];
        
        // Actualizar usuario
        if($this->userModel->updateUser($userData)) {
            // Si el rol cambió a staff y no existe en la tabla staff, crear registro
            if($userData['role'] === 'staff') {
                $this->userModel->ensureStaffRecord($userData['id']);
            }
            
            $_SESSION['admin_message'] = 'Usuario actualizado correctamente';
            $_SESSION['admin_message_type'] = 'success';
            $_SESSION['toast_message'] = 'Usuario actualizado correctamente';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['admin_message'] = 'Error al actualizar usuario';
            $_SESSION['admin_message_type'] = 'error';
            $_SESSION['toast_message'] = 'Error al actualizar usuario';
            $_SESSION['toast_type'] = 'error';
        }
        
        header('Location: ' . URLROOT . '/admin/users');
        exit;
    }
    
    /**
     * Activa o desactiva un usuario
     */
    public function toggleUserStatus($userId = null) {
        // Si no se proporciona ID, redirigir a la lista de usuarios
        if(!$userId) {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
        
        // Obtener información del usuario
        $user = $this->userModel->findById($userId);
        
        if(!$user) {
            $_SESSION['admin_message'] = 'Usuario no encontrado';
            $_SESSION['admin_message_type'] = 'danger';
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
        
        // Verificar si es un objeto o un array
        $isActive = is_object($user) ? $user->isActive : $user['isActive'];
        
        // Cambiar estado del usuario
        if($isActive) {
            $result = $this->userModel->deactivate($userId);
            $message = 'Usuario desactivado correctamente';
        } else {
            $result = $this->userModel->activate($userId);
            $message = 'Usuario activado correctamente';
        }
        
        if($result) {
            $_SESSION['admin_message'] = $message;
            $_SESSION['admin_message_type'] = 'success';
        } else {
            $_SESSION['admin_message'] = 'Error al cambiar el estado del usuario';
            $_SESSION['admin_message_type'] = 'danger';
        }
        
        header('Location: ' . URLROOT . '/admin/users');
        exit;
    }
    
    /**
     * Elimina un usuario
     */
    public function deleteUser($userId = null) {
        if(!$userId) {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
        
        // Eliminar usuario
        if($this->userModel->deleteUser($userId)) {
            $_SESSION['admin_message'] = 'Usuario eliminado correctamente';
            $_SESSION['admin_message_type'] = 'success';
            $_SESSION['toast_message'] = 'Usuario eliminado correctamente';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['admin_message'] = 'Error al eliminar usuario';
            $_SESSION['admin_message_type'] = 'error';
            $_SESSION['toast_message'] = 'Error al eliminar usuario';
            $_SESSION['toast_type'] = 'error';
        }
        
        header('Location: ' . URLROOT . '/admin/users');
        exit;
    }
    
    /**
     * Gestión de clases
     */
    public function classes() {
        // Cargar tipos de clases
        $typeClassModel = new TypeClass();
        $classTypes = $typeClassModel->getAllTypes();
        
        // Cargar monitores disponibles
        $monitors = $this->userModel->getAllMonitors();
        
        // Cargar todas las clases
        $classes = $this->classModel->getAllClasses();
        
        $data = [
            'title' => 'Gestión de Clases',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Administrador',
            'classTypes' => $classTypes,
            'monitors' => $monitors,
            'classes' => $classes
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/admin/classes.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Muestra la vista de gestión de notificaciones
     */
    public function notifications() {
        // Cargar modelo de notificaciones
        $notificationModel = new Notification();
        
        // Obtener todas las notificaciones
        $notifications = $notificationModel->getAllNotifications();
        
        // Cargar clases activas
        $classes = [];
        $classModel = new Class_();
        $classes = $classModel->getActiveClasses();
        
        // Cargar modelo de usuarios para el selector de destinatarios
        $users = $this->userModel->getAllUsers('user');
        
        $data = [
            'title' => 'Gestión de Notificaciones',
            'notifications' => $notifications,
            'classes' => $classes,
            'users' => $users
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/admin/notifications.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Crea una nueva notificación
     */
    public function createNotification() {
        // Verificar si se envió el formulario
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/admin/notifications');
            exit;
        }
        
        // Cargar modelo de notificaciones
        $notificationModel = new Notification();
        
        try {
            // Obtener datos del formulario
            $notificationData = [
                // Usar NULL para personal_id para evitar el error de clave foránea
                'personal_id' => null,
                'title' => trim($_POST['title']),
                'message' => trim($_POST['message']),
                'type' => $_POST['type'],
                'is_global' => isset($_POST['is_global']) ? 1 : 0,
                'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null
            ];
            
            // Si no es global, obtener los destinatarios seleccionados
            if(!$notificationData['is_global'] && isset($_POST['recipients'])) {
                $notificationData['recipients'] = $_POST['recipients'];
            }
            
            // Crear la notificación
            if($notificationModel->create($notificationData)) {
                $_SESSION['admin_message'] = 'Notificación enviada correctamente';
                $_SESSION['admin_message_type'] = 'success';
            } else {
                $_SESSION['admin_message'] = 'Error al enviar la notificación';
                $_SESSION['admin_message_type'] = 'danger';
            }
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al procesar la notificación: ' . $e->getMessage());
            $_SESSION['admin_message'] = 'Error al enviar la notificación: ' . $e->getMessage();
            $_SESSION['admin_message_type'] = 'danger';
        }
        
        header('Location: ' . URLROOT . '/admin/notifications');
        exit;
    }
    
    /**
     * Obtiene los detalles de una notificación específica (para AJAX)
     */
    public function getNotificationDetails($notificationId = null) {
        // Verificar que se haya proporcionado un ID
        if(!$notificationId) {
            echo json_encode(['success' => false, 'error' => 'ID de notificación no válido']);
            return;
        }
        
        // Cargar modelo de notificaciones
        $notificationModel = new Notification();
        
        // Obtener detalles de la notificación
        $notification = $notificationModel->getNotificationById($notificationId);
        
        if(!$notification) {
            echo json_encode(['success' => false, 'error' => 'Notificación no encontrada']);
            return;
        }

        // Convertir objeto a array si es necesario
        if (is_object($notification)) {
            $notification = (array) $notification;
        }
        
        // Devolver los datos como JSON
        echo json_encode([
            'success' => true,
            'notification' => $notification,
            'recipients' => []  // Ya no usamos destinatarios en la versión simplificada
        ]);
    }
    
    /**
     * Elimina una notificación
     */
    public function deleteNotification($notificationId = null) {
        // Verificar que se haya proporcionado un ID
        if(!$notificationId) {
            $_SESSION['admin_message'] = 'ID de notificación no válido';
            $_SESSION['admin_message_type'] = 'danger';
            header('Location: ' . URLROOT . '/admin/notifications');
            exit;
        }
        
        // Cargar modelo de notificaciones
        $notificationModel = new Notification();
        
        // Eliminar la notificación
        if($notificationModel->delete($notificationId)) {
            $_SESSION['admin_message'] = 'Notificación eliminada correctamente';
            $_SESSION['admin_message_type'] = 'success';
        } else {
            $_SESSION['admin_message'] = 'Error al eliminar la notificación';
            $_SESSION['admin_message_type'] = 'danger';
        }
        
        header('Location: ' . URLROOT . '/admin/notifications');
        exit;
    }
    
    /**
     * Añadir una nueva clase
     */
    public function addClass() {
        // Verificar si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/admin/classes');
            exit;
        }
        
        // Recoger datos del formulario
        $data = [
            'tipus_classe_id' => trim($_POST['tipus_classe_id']),
            'monitor_id' => trim($_POST['monitor_id']),
            'data' => trim($_POST['data']),
            'hora' => trim($_POST['hora']),
            'duracio' => trim($_POST['duracio']),
            'capacitat_maxima' => trim($_POST['capacitat_maxima']),
            'sala' => trim($_POST['sala'])
        ];
        
        // Verificar si hay conflictos de horario para el monitor
        if ($this->classModel->hasScheduleConflict($data)) {
            $_SESSION['admin_message'] = 'El monitor ya tiene una clase programada en ese horario';
            $_SESSION['admin_message_type'] = 'danger';
            $_SESSION['toast_message'] = 'El monitor ya tiene una clase programada en ese horario';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/admin/classes');
            exit;
        }
        
        // Crear la clase
        if ($this->classModel->addClass($data)) {
            $_SESSION['admin_message'] = 'Clase añadida correctamente';
            $_SESSION['admin_message_type'] = 'success';
            $_SESSION['toast_message'] = 'Clase añadida correctamente';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['admin_message'] = 'Error al añadir la clase';
            $_SESSION['admin_message_type'] = 'danger';
            $_SESSION['toast_message'] = 'Error al añadir la clase';
            $_SESSION['toast_type'] = 'error';
        }
        
        header('Location: ' . URLROOT . '/admin/classes');
        exit;
    }
    
    /**
     * Obtener detalles de una clase (para editar)
     * @param int $classId ID de la clase
     */
    public function getClassDetails($classId = null) {
        // Verificar que se proporcione un ID
        if (!$classId) {
            echo json_encode(['success' => false, 'error' => 'ID de clase no válido']);
            return;
        }
        
        // Obtener la clase
        $class = $this->classModel->getClassById($classId);
        
        if (!$class) {
            echo json_encode(['success' => false, 'error' => 'Clase no encontrada']);
            return;
        }
        
        // Formatear la fecha y hora para el formulario
        $class->data = date('Y-m-d', strtotime($class->data));
        $class->hora = date('H:i', strtotime($class->hora));
        
        // Devolver los datos como JSON
        echo json_encode(['success' => true, 'class' => $class]);
    }
    
    /**
     * Actualiza una clase existente
     */
    public function updateClass() {
        // Verificar si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/admin/classes');
            exit;
        }
        
        // Recoger datos del formulario
        $data = [
            'classe_id' => $_POST['classe_id'],
            'tipus_classe_id' => trim($_POST['tipus_classe_id']),
            'monitor_id' => trim($_POST['monitor_id']),
            'data' => trim($_POST['data']),
            'hora' => trim($_POST['hora']),
            'duracio' => trim($_POST['duracio']),
            'capacitat_maxima' => trim($_POST['capacitat_maxima']),
            'sala' => trim($_POST['sala'])
        ];
        
        // Verificar si hay conflictos de horario para el monitor
        if ($this->classModel->hasScheduleConflict($data, $data['classe_id'])) {
            $_SESSION['admin_message'] = 'El monitor ya tiene una clase programada en ese horario';
            $_SESSION['admin_message_type'] = 'danger';
            $_SESSION['toast_message'] = 'El monitor ya tiene una clase programada en ese horario';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/admin/classes');
            exit;
        }
        
        // Actualizar la clase
        if ($this->classModel->updateClass($data)) {
            $_SESSION['admin_message'] = 'Clase actualizada correctamente';
            $_SESSION['admin_message_type'] = 'success';
            $_SESSION['toast_message'] = 'Clase actualizada correctamente';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['admin_message'] = 'Error al actualizar la clase';
            $_SESSION['admin_message_type'] = 'danger';
            $_SESSION['toast_message'] = 'Error al actualizar la clase';
            $_SESSION['toast_type'] = 'error';
        }
        
        header('Location: ' . URLROOT . '/admin/classes');
        exit;
    }
    
    /**
     * Eliminar una clase
     */
    public function deleteClass() {
        // Verificar si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URLROOT . '/admin/classes');
            exit;
        }
        
        // Verificar que se haya proporcionado un ID
        if (!isset($_POST['classe_id']) || empty($_POST['classe_id'])) {
            $_SESSION['admin_message'] = 'ID de clase no válido';
            $_SESSION['admin_message_type'] = 'danger';
            $_SESSION['toast_message'] = 'ID de clase no válido';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/admin/classes');
            exit;
        }
        
        // Eliminar la clase
        if ($this->classModel->deleteClass($_POST['classe_id'])) {
            $_SESSION['admin_message'] = 'Clase eliminada correctamente';
            $_SESSION['admin_message_type'] = 'success';
            $_SESSION['toast_message'] = 'Clase eliminada correctamente';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['admin_message'] = 'Error al eliminar la clase';
            $_SESSION['admin_message_type'] = 'danger';
            $_SESSION['toast_message'] = 'Error al eliminar la clase';
            $_SESSION['toast_type'] = 'error';
        }
        
        header('Location: ' . URLROOT . '/admin/classes');
        exit;
    }
}
