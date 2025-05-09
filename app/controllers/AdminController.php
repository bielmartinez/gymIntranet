<?php
/**
 * Controlador para la gestión de funciones administrativas
 */
// Incluir los modelos necesarios
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Class.php';
require_once dirname(__DIR__) . '/models/TypeClass.php';
require_once dirname(__DIR__) . '/models/Notification.php';

class AdminController extends BaseController {
    protected $userController;
    protected $userModel;
    protected $classModel;
    
    public function __construct() {
        parent::__construct();
        
        // Verificar que el usuario sea administrador o staff para permitir acceso
        $this->requireRole(['admin', 'staff']);
        
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
        $todayClasses = $this->classModel->getTodayClassesCount();
        
        $data = [
            'title' => 'Panel de Administración',
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'staffUsers' => $staffUsers,
            'todayClasses' => $todayClasses
        ];
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('admin/dashboard', $data);
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
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('admin/register', $data);
    }
      /**
     * Procesa el registro de un nuevo usuario
     */
    public function register() {
        // Verificar si se envió el formulario usando el método del BaseController
        if(!$this->isPost()) {
            $this->redirect('admin/registerForm');
            return;
        }
        
        // Procesar y sanitizar datos del formulario
        $userData = [
            'fullName' => $this->sanitizeInput($_POST['fullName'] ?? ''),
            'username' => $this->sanitizeInput($_POST['username'] ?? ''),
            'email' => $this->sanitizeInput($_POST['email'] ?? ''),
            'password' => $this->sanitizeInput($_POST['password'] ?? ''),
            'confirmPassword' => $this->sanitizeInput($_POST['confirmPassword'] ?? ''),
            'role' => $this->sanitizeInput($_POST['role'] ?? ''),
            'membershipType' => $this->sanitizeInput($_POST['membershipType'] ?? ''),
            'phone' => $this->sanitizeInput($_POST['phone'] ?? ''),
            'birthDate' => $this->sanitizeInput($_POST['birthDate'] ?? ''),
            'sendWelcomeEmail' => isset($_POST['sendWelcomeEmail'])
        ];
        
        // Registrar usuario usando UserController
        $userId = $this->userController->register($userData);
        
        if(!$userId) {
            $this->handleError('Error al crear el usuario. Por favor revise los datos ingresados.', 'admin/registerForm');
            return;
        }
        
        // Si el usuario es staff, crear entrada en la tabla personal
        if($userData['role'] === 'staff') {
            if($this->userModel->createStaffRecord($userId)) {
                $this->handleSuccess('Monitor registrado correctamente y vinculado como personal', 'admin/users');
            } else {
                $this->handleSuccess('Usuario creado correctamente, pero hubo un error al vincularlo como personal', 'admin/users');
            }
            return;
        }
        
        $this->handleSuccess('Usuario creado correctamente', 'admin/users');
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
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('admin/users', $data);
    }
    
    /**
     * Elimina un usuario
     */
    public function deleteUser($userId = null) {
        if(!$userId) {
            $this->redirect('admin/users');
            return;
        }
        
        // Eliminar usuario
        if($this->userModel->deleteUser($userId)) {
            $this->handleSuccess('Usuario eliminado correctamente', 'admin/users');
        } else {
            $this->handleError('Error al eliminar usuario', 'admin/users');
        }
    }
    
    /**
     * Reactivar un usuario que ha sido previamente desactivado
     * @param int|null $userId ID del usuario a reactivar
     */
    public function reactivateUser($userId = null) {
        if(!$userId) {
            $this->redirect('admin/users');
            return;
        }
        
        // Obtener información del usuario
        $user = $this->userModel->findById($userId);
        
        if(!$user) {
            $this->handleError('Usuario no encontrado', 'admin/users');
            return;
        }
        
        // Reactivar el usuario
        if($this->userModel->activate($userId)) {
            $this->handleSuccess('Usuario reactivado correctamente', 'admin/users');
        } else {
            $this->handleError('Error al reactivar el usuario', 'admin/users');
        }
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
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('admin/classes', $data);
    }
    
    /**
     * Filtrar clases según los criterios seleccionados
     */
    public function filterClasses() {
        // Verificar si se envió el formulario usando el método del BaseController
        if (!$this->isPost()) {
            $this->redirect('admin/classes');
            return;
        }
        
        // Recoger datos del formulario
        $filters = [
            'date' => !empty($_POST['date']) ? $this->sanitizeInput($_POST['date']) : null,
            'type_id' => !empty($_POST['type_id']) ? $this->sanitizeInput($_POST['type_id']) : null,
            'monitor_id' => !empty($_POST['monitor_id']) ? $this->sanitizeInput($_POST['monitor_id']) : null
        ];
        
        // Filtrar las clases
        $classes = $this->classModel->filterClasses($filters);
        
        // Cargar tipos de clases
        $typeClassModel = new TypeClass();
        $classTypes = $typeClassModel->getAllTypes();
        
        // Cargar monitores disponibles
        $monitors = $this->userModel->getAllMonitors();
        
        $data = [
            'title' => 'Gestión de Clases - Resultados filtrados',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Administrador',
            'classTypes' => $classTypes,
            'monitors' => $monitors,
            'classes' => $classes,
            'filters' => $filters
        ];
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('admin/classes', $data);
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
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('admin/notifications', $data);
    }
    
    /**
     * Crea una nueva notificación
     */
    public function createNotification() {
        // Verificar si se envió el formulario usando el método del BaseController
        if(!$this->isPost()) {
            $this->redirect('admin/notifications');
            return;
        }
        
        // Cargar modelo de notificaciones
        $notificationModel = new Notification();
          try {            // Obtener datos del formulario
            $notificationData = [
                'title' => $this->sanitizeInput($_POST['title']),
                'message' => $this->sanitizeInput($_POST['message']),
                'type' => isset($_POST['type']) ? $this->sanitizeInput($_POST['type']) : 'info', // Valor por defecto 'info'
                'is_global' => isset($_POST['is_global']) ? 1 : 0,
                'class_id' => !empty($_POST['class_id']) ? $this->sanitizeInput($_POST['class_id']) : null,
                'emisor_id' => $_SESSION['user_id'] // Usar el ID del usuario actual como emisor
            ];
            
            // Registro de depuración
            Logger::log('DEBUG', 'Iniciando creación de notificación simplificada: ' . $notificationData['title']);
            
            // Si no es global, obtener los destinatarios seleccionados
            if(!$notificationData['is_global'] && isset($_POST['recipients'])) {
                $notificationData['recipients'] = $_POST['recipients'];
            }
              // Convertir los nombres de campo a los nombres que coincidan con la estructura de la base de datos
            $dbNotificationData = [
                'titol' => $notificationData['title'],
                'missatge' => $notificationData['message'],
                'creat_el' => date('Y-m-d H:i:s'),
                'emisor_id' => $notificationData['emisor_id']
            ];
            
            // Si hay una clase específica seleccionada, agregarla 
            if (!empty($notificationData['class_id'])) {
                $dbNotificationData['classe_id'] = $notificationData['class_id'];
            }
            
            // Crear la notificación
            if($notificationModel->createNotification($dbNotificationData)) {
                $this->handleSuccess('Notificación enviada correctamente', 'admin/notifications');
            } else {
                $this->handleError('Error al enviar la notificación', 'admin/notifications');
            }
        } catch (Exception $e) {
            Logger::log('ERROR', 'Error al procesar la notificación: ' . $e->getMessage());
            $this->handleError('Error al enviar la notificación: ' . $e->getMessage(), 'admin/notifications');
        }
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
            $this->handleError('ID de notificación no válido', 'admin/notifications');
            return;
        }
        
        // Cargar modelo de notificaciones
        $notificationModel = new Notification();
        
        // Eliminar la notificación
        if($notificationModel->delete($notificationId)) {
            $this->handleSuccess('Notificación eliminada correctamente', 'admin/notifications');
        } else {
            $this->handleError('Error al eliminar la notificación', 'admin/notifications');
        }
    }
    
    /**
     * Añadir una nueva clase
     */
    public function addClass() {
        // Verificar si se envió el formulario usando el método del BaseController
        if (!$this->isPost()) {
            $this->redirect('admin/classes');
            return;
        }
        
        // Recoger datos del formulario
        $data = [
            'tipus_classe_id' => $this->sanitizeInput($_POST['tipus_classe_id']),
            'monitor_id' => $this->sanitizeInput($_POST['monitor_id']),
            'data' => $this->sanitizeInput($_POST['data']),
            'hora' => $this->sanitizeInput($_POST['hora']),
            'duracio' => $this->sanitizeInput($_POST['duracio']),
            'capacitat_maxima' => $this->sanitizeInput($_POST['capacitat_maxima']),
            'sala' => $this->sanitizeInput($_POST['sala'])
        ];
        
        // Validar que los valores estén en los rangos correctos
        if ($data['duracio'] < 15 || $data['duracio'] > 60) {
            $this->handleError('La duración debe estar entre 15 y 60 minutos', 'admin/classes');
            return;
        }
        
        if ($data['sala'] < 1 || $data['sala'] > 4) {
            $this->handleError('El número de sala debe estar entre 1 y 4', 'admin/classes');
            return;
        }
        
        if ($data['capacitat_maxima'] < 5 || $data['capacitat_maxima'] > 20) {
            $this->handleError('La capacidad máxima debe estar entre 5 y 20 personas', 'admin/classes');
            return;
        }
        
        // Verificar si hay conflictos de horario para el monitor
        if ($this->classModel->hasScheduleConflict($data)) {
            $this->handleError('El monitor ya tiene una clase programada en ese horario', 'admin/classes');
            return;
        }
        
        // Crear la clase
        if ($this->classModel->addClass($data)) {
            $this->handleSuccess('Clase añadida correctamente', 'admin/classes');
        } else {
            $this->handleError('Error al añadir la clase', 'admin/classes');
        }
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
        // Verificar si se envió el formulario usando el método del BaseController
        if (!$this->isPost()) {
            $this->redirect('admin/classes');
            return;
        }
        
        // Recoger datos del formulario
        $data = [
            'classe_id' => $this->sanitizeInput($_POST['classe_id']),
            'tipus_classe_id' => $this->sanitizeInput($_POST['tipus_classe_id']),
            'monitor_id' => $this->sanitizeInput($_POST['monitor_id']),
            'data' => $this->sanitizeInput($_POST['data']),
            'hora' => $this->sanitizeInput($_POST['hora']),
            'duracio' => $this->sanitizeInput($_POST['duracio']),
            'capacitat_maxima' => $this->sanitizeInput($_POST['capacitat_maxima']),
            'sala' => $this->sanitizeInput($_POST['sala'])
        ];
        
        // Validar que los valores estén en los rangos correctos
        if ($data['duracio'] < 15 || $data['duracio'] > 60) {
            $this->handleError('La duración debe estar entre 15 y 60 minutos', 'admin/classes');
            return;
        }
        
        if ($data['sala'] < 1 || $data['sala'] > 4) {
            $this->handleError('El número de sala debe estar entre 1 y 4', 'admin/classes');
            return;
        }
        
        if ($data['capacitat_maxima'] < 5 || $data['capacitat_maxima'] > 20) {
            $this->handleError('La capacidad máxima debe estar entre 5 y 20 personas', 'admin/classes');
            return;
        }
        
        // Verificar que la capacidad máxima no sea menor que la actual
        $currentClass = $this->classModel->getClassById($data['classe_id']);
        if ($currentClass && $data['capacitat_maxima'] < $currentClass->capacitat_actual) {
            $this->handleError('La capacidad máxima no puede ser menor que el número actual de reservas (' . $currentClass->capacitat_actual . ')', 'admin/classes');
            return;
        }
        
        // Verificar si hay conflictos de horario para el monitor
        if ($this->classModel->hasScheduleConflict($data, $data['classe_id'])) {
            $this->handleError('El monitor ya tiene una clase programada en ese horario', 'admin/classes');
            return;
        }
        
        // Actualizar la clase
        if ($this->classModel->updateClass($data)) {
            $this->handleSuccess('Clase actualizada correctamente', 'admin/classes');
        } else {
            $this->handleError('Error al actualizar la clase', 'admin/classes');
        }
    }
      /**
     * Eliminar una clase
     */
    public function deleteClass() {
        // Verificar si se envió el formulario usando el método del BaseController
        if (!$this->isPost()) {
            $this->redirect('admin/classes');
            return;
        }
        
        // Verificar que se haya proporcionado un ID
        if (!isset($_POST['classe_id']) || empty($_POST['classe_id'])) {
            $this->handleError('ID de clase no válido', 'admin/classes');
            return;
        }
        
        $classId = $this->sanitizeInput($_POST['classe_id']);
        
        // Primero eliminar todas las reservas asociadas a la clase
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        $reservationModel->deleteAllReservationsByClassId($classId);
        
        // Resetear la capacidad de la clase a 0 (por si acaso)
        $this->classModel->resetCapacity($classId);
        
        // Ahora eliminar la clase
        if ($this->classModel->deleteClass($classId)) {
            $this->handleSuccess('Clase eliminada correctamente', 'admin/classes');
        } else {
            $this->handleError('Error al eliminar la clase', 'admin/classes');
        }
    }
    
    /**
     * Cancelar una reserva específica
     */
    public function cancelReservation() {
        // Verificar si se envió el formulario usando el método del BaseController
        if (!$this->isPost()) {
            $this->redirect('admin/classes');
            return;
        }
        
        // Verificar que se hayan proporcionado los IDs necesarios
        if (!isset($_POST['reserva_id']) || empty($_POST['reserva_id']) || 
            !isset($_POST['classe_id']) || empty($_POST['classe_id'])) {
            $this->handleError('Datos incompletos para cancelar la reserva', 'admin/classes');
            return;
        }
        
        $reservationId = $this->sanitizeInput($_POST['reserva_id']);
        $classId = $this->sanitizeInput($_POST['classe_id']);
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        // Cancelar la reserva
        if ($reservationModel->cancelReservation($reservationId)) {
            $this->handleSuccess('Reserva cancelada correctamente', 'admin/viewClassReservations/' . $classId);
        } else {
            $this->handleError('Error al cancelar la reserva', 'admin/viewClassReservations/' . $classId);
        }
    }
    
    /**
     * Cancelar todas las reservas de una clase
     */
    public function cancelAllReservations() {
        // Verificar si se envió el formulario usando el método del BaseController
        if (!$this->isPost()) {
            $this->redirect('admin/classes');
            return;
        }
        
        // Verificar que se haya proporcionado un ID de clase
        if (!isset($_POST['classe_id']) || empty($_POST['classe_id'])) {
            $this->handleError('ID de clase no válido', 'admin/classes');
            return;
        }
        
        $classId = $this->sanitizeInput($_POST['classe_id']);
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        // Eliminar todas las reservas de esta clase
        if ($reservationModel->deleteAllReservationsByClassId($classId)) {
            // Actualizar capacidad de la clase a 0
            $this->classModel->resetCapacity($classId);
            $this->handleSuccess('Todas las reservas han sido eliminadas', 'admin/viewClassReservations/' . $classId);
        } else {
            $this->handleError('Error al eliminar las reservas', 'admin/viewClassReservations/' . $classId);
        }
    }
    
    /**
     * Ver las reservas de una clase específica
     * @param int $classId ID de la clase
     */
    public function viewClassReservations($classId = null) {
        // Verificar que se haya proporcionado un ID
        if (!$classId) {
            $this->redirect('admin/classes');
            return;
        }
        
        // Obtener detalles de la clase
        $class = $this->classModel->getClassById($classId);
        
        if (!$class) {
            $this->handleError('Clase no encontrada', 'admin/classes');
            return;
        }
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        // Obtener reservas de la clase
        $reservations = $reservationModel->getReservationsByClassId($classId);
        
        // Preparar datos para la vista
        $data = [
            'class' => $class,
            'reservations' => $reservations
        ];
        
        // Cargar vista
        $this->loadView('admin/class_reservations', $data);
    }
}