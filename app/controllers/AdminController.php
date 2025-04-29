<?php
/**
 * Controlador para la gestión de funciones administrativas
 */
class AdminController {
    private $userController;
    
    public function __construct() {
        // DESHABILITADO TEMPORALMENTE: Verificar que el usuario sea administrador
        /*
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT);
            exit;
        }
        */
        
        $this->userController = new UserController();
    }
    
    /**
     * Página de inicio para administradores
     */
    public function index() {
        $data = [
            'title' => 'Panel de Administración',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Administrador'
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
        if($this->userController->register($userData)) {
            // Éxito - Redirigir a lista de usuarios
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        } else {
            // Error - Volver al formulario
            header('Location: ' . URLROOT . '/admin/registerForm');
            exit;
        }
    }
    
    /**
     * Lista todos los usuarios del sistema
     */
    public function users() {
        // Pendiente de implementar
        // Obtener lista de usuarios y cargar vista
        $data = [
            'title' => 'Gestión de Usuarios',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Administrador'
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/admin/users.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
}
