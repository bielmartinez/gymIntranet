<?php
/**
 * Controlador de autenticación
 * Maneja inicio de sesión, recuperación de contraseña y cierre de sesión
 */
class AuthController extends BaseController {
    protected $userModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }    /**
     * Método por defecto: muestra la página de login
     */
    public function index() {
        $this->login();
    }
    
    /**
     * Muestra la página de inicio de sesión
     */
    public function login() {
        // Redirigir si ya está logueado
        if (isset($_SESSION['user_id'])) {
            $this->redirect('user/dashboard');
            return;
        }
        
        // Verificar si hay datos de login redirigidos desde el archivo legacy
        if (isset($_SESSION['login_redirect_data'])) {
            $_POST = $_SESSION['login_redirect_data'];
            unset($_SESSION['login_redirect_data']);
            $_SERVER['REQUEST_METHOD'] = 'POST';
        }
        
        // Verificar si se ha enviado el formulario usando el método del BaseController
        if ($this->isPost()) {
            // Procesar el inicio de sesión
            return $this->processLogin();
        }
        
        // Preparar datos para la vista
        $data = [
            'title' => 'Iniciar Sesión',
            'email' => $_SESSION['login_data']['email'] ?? '',
            'email_err' => $_SESSION['login_errors']['email_err'] ?? '',
            'password_err' => $_SESSION['login_errors']['password_err'] ?? ''
        ];
        
        // Limpiar los datos de sesión temporales
        unset($_SESSION['login_data']);
        unset($_SESSION['login_errors']);
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('auth/login', $data);
    }    /**
     * Procesa el formulario de inicio de sesión
     */
    private function processLogin() {
        // Sanitizar datos del formulario
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? ''; // No sanitizar contraseña
        $remember = isset($_POST['remember']);
        
        // Validar datos
        if (!$this->validateRequired($email, 'email') || !$this->validateRequired($password, 'password')) {
            // Hay errores en el formulario
            $_SESSION['login_errors'] = $this->getErrors();
            $_SESSION['login_data']['email'] = $email;
            
            // Redirigir de vuelta al login
            $this->redirect('auth/login');
            return;
        }
          // Intentar iniciar sesión
        $user = $this->userModel->findByUsername($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            // Error de autenticación
            $this->errors['login_err'] = 'Correo electrónico o contraseña incorrectos';
            
            // Guardar el email para mostrar en el formulario
            $_SESSION['login_data']['email'] = $email;
            $_SESSION['login_errors'] = $this->getErrors();
            
            // Redirigir de vuelta al login
            $this->redirect('auth/login');
            return;
        }
        
        // Verificar si el usuario está activo
        if (isset($user['isActive']) && $user['isActive'] == 0) {
            // Usuario desactivado
            $this->errors['login_err'] = 'La cuenta ha sido desactivada. Contacte con administración.';
            
            // Guardar el email para mostrar en el formulario
            $_SESSION['login_data']['email'] = $email;
            $_SESSION['login_errors'] = $this->getErrors();
            
            // Redirigir de vuelta al login
            $this->redirect('auth/login');
            return;
        }
        
        // Inicio de sesión exitoso
        // Almacenar datos del usuario en sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['fullName'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        // Redirigir según el rol
        switch ($user['role']) {
            case 'admin':
                $this->redirect('admin/dashboard');
                break;
            case 'staff':
                $this->redirect('staff/dashboard');
                break;
            default:
                $this->redirect('user/dashboard');
                break;
        }
    }    /**
     * Muestra la página de recuperación de contraseña
     */
    public function forgotPassword() {
        // DESHABILITADO TEMPORALMENTE: Redirigir si ya está logueado
        /*
        if (isset($_SESSION['user_id'])) {
            $this->redirect('user/dashboard');
            return;
        }
        */
        
        // Verificar si se ha enviado el formulario usando el método del BaseController
        if ($this->isPost()) {
            // Procesar la solicitud de recuperación
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            
            // Validar email
            if (!$this->validateEmail($email)) {
                $this->handleError('Por favor ingrese un correo electrónico válido', 'auth/forgotPassword');
                return;
            }
            
            // Instanciar el controlador de usuario para usar su método de recuperación
            $userController = new UserController();
            $userController->sendPasswordReset($email);
            
            // Mostrar mensaje de éxito (siempre, para no revelar emails existentes)
            $this->handleSuccess('Si tu correo está registrado, recibirás un enlace para restablecer tu contraseña', 'auth/forgotPassword');
            return;
        }
        
        // Preparar datos para la vista
        $data = [
            'title' => 'Recuperar Contraseña',
            'email' => $_SESSION['forgot_data']['email'] ?? '',
            'email_err' => $_SESSION['forgot_errors']['email_err'] ?? ''
        ];
        
        // Limpiar datos temporales de sesión
        unset($_SESSION['forgot_data']);
        unset($_SESSION['forgot_errors']);
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('auth/forgotPassword', $data);
    }    /**
     * Muestra la página de reseteo de contraseña
     */
    public function resetPassword($token = null) {
        // DESHABILITADO TEMPORALMENTE: Redirigir si ya está logueado
        /*
        if (isset($_SESSION['user_id'])) {
            $this->redirect('user/dashboard');
            return;
        }
        */
        
        // Obtener token de la URL si no se pasó como parámetro
        if ($token === null) {
            $token = $_GET['token'] ?? '';
        }
        
        // Verificar token
        if (empty($token)) {
            $this->handleError('Token de recuperación inválido o expirado', 'auth/login');
            return;
        }
        
        // Verificar si se ha enviado el formulario usando el método del BaseController
        if ($this->isPost()) {
            // Procesar el reseteo de contraseña
            $formToken = $this->sanitizeInput($_POST['token'] ?? '');
            $newPassword = $_POST['newPassword'] ?? ''; // No sanitizar contraseña
            $confirmPassword = $_POST['confirmPassword'] ?? ''; // No sanitizar contraseña
              // Validaciones
            $this->validateRequired($newPassword, 'password');
            $this->validateMinLength($newPassword, 'password', 8);
            $this->validateRequired($confirmPassword, 'confirm_password');
            
            if ($newPassword !== $confirmPassword) {
                $this->errors['confirm_password_err'] = 'Las contraseñas no coinciden';
            }
            
            // Si no hay errores, resetear la contraseña
            if (!$this->hasErrors()) {
                // Instanciar el controlador de usuario para usar su método de reseteo
                $userController = new UserController();
                if ($userController->resetPassword($formToken, $newPassword)) {
                    // Éxito
                    $this->handleSuccess('Tu contraseña ha sido restablecida correctamente', 'auth/login');
                    return;
                } 
                
                // Error
                $this->handleError('El token no es válido o ha expirado', 'auth/resetPassword?token=' . $formToken);
                return;
            }
            
            // Hay errores, guardarlos en sesión
            $_SESSION['reset_errors'] = $this->getErrors();
            $this->redirect('auth/resetPassword?token=' . $formToken);
            return;
        }
        
        // Preparar datos para la vista
        $data = [
            'title' => 'Restablecer Contraseña',
            'token' => $token,
            'password_err' => $_SESSION['reset_errors']['password_err'] ?? '',
            'confirm_password_err' => $_SESSION['reset_errors']['confirm_password_err'] ?? ''
        ];
        
        // Limpiar datos de sesión temporales
        unset($_SESSION['reset_errors']);
        
        // Cargar la vista usando el método del BaseController
        $this->loadView('auth/resetPassword', $data);
    }    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = [];
        
        // Si se desea destruir la sesión completamente, borrar también la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        
        // Destruir la sesión
        session_destroy();
        
        // Mostrar mensaje de éxito y redirigir al login usando el método del BaseController
        $this->handleSuccess('Has cerrado sesión correctamente', 'auth/login');
    }
}