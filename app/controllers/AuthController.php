<?php
/**
 * Controlador de autenticación
 * Maneja inicio de sesión, recuperación de contraseña y cierre de sesión
 */
class AuthController {
    private $userModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Método por defecto: muestra la página de login
     */
    public function index() {
        $this->login();
    }
    
    /**
     * Muestra la página de inicio de sesión
     */    public function login() {
        // Redirigir si ya está logueado
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/user/dashboard');
            exit;
        }
        
        // Verificar si hay datos de login redirigidos desde el archivo legacy
        if (isset($_SESSION['login_redirect_data'])) {
            $_POST = $_SESSION['login_redirect_data'];
            unset($_SESSION['login_redirect_data']);
            $_SERVER['REQUEST_METHOD'] = 'POST';
        }
        
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el inicio de sesión
            return $this->processLogin();
        }
        
        // Cargar vista de login
        $data = [
            'title' => 'Iniciar Sesión',
            'email' => isset($_SESSION['login_data']['email']) ? $_SESSION['login_data']['email'] : '',
            'email_err' => isset($_SESSION['login_errors']['email_err']) ? $_SESSION['login_errors']['email_err'] : '',
            'password_err' => isset($_SESSION['login_errors']['password_err']) ? $_SESSION['login_errors']['password_err'] : ''
        ];
        
        // Limpiar los datos de sesión temporales
        unset($_SESSION['login_data']);
        unset($_SESSION['login_errors']);
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/auth/login.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Procesa el formulario de inicio de sesión
     */
    private function processLogin() {
        // Validar datos del formulario
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $remember = isset($_POST['remember']) ? true : false;
        
        // Validar datos
        $errors = [];
        
        if (empty($email)) {
            $errors['email_err'] = 'Por favor ingrese su correo electrónico';
        }
        
        if (empty($password)) {
            $errors['password_err'] = 'Por favor ingrese su contraseña';
        }
        
        // Si no hay errores, intentar iniciar sesión
        if (empty($errors)) {
            $user = $this->userModel->findByUsername($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Inicio de sesión exitoso
                
                // Almacenar datos del usuario en sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullName'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirigir según el rol
                if ($user['role'] == 'admin') {
                    header('Location: ' . URLROOT . '/admin/dashboard');
                } else if ($user['role'] == 'staff') {
                    header('Location: ' . URLROOT . '/staff/dashboard');
                } else {
                    header('Location: ' . URLROOT . '/user/dashboard');
                }
                exit;
            } else {
                // Error de autenticación
                $_SESSION['login_message'] = 'Correo electrónico o contraseña incorrectos';
                $_SESSION['login_message_type'] = 'danger';
                
                // Guardar el email para mostrar en el formulario
                $_SESSION['login_data']['email'] = $email;
                
                // Redirigir de vuelta al login
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }
        } else {
            // Hay errores en el formulario
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_data']['email'] = $email;
            
            // Redirigir de vuelta al login
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }
    
    /**
     * Muestra la página de recuperación de contraseña
     */
    public function forgotPassword() {
        // DESHABILITADO TEMPORALMENTE: Redirigir si ya está logueado
        /*
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/user/dashboard');
            exit;
        }
        */
        
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar la solicitud de recuperación
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            
            // Validar email
            if (empty($email)) {
                $_SESSION['forgot_errors']['email_err'] = 'Por favor ingrese su correo electrónico';
                header('Location: ' . URLROOT . '/auth/forgotPassword');
                exit;
            }
            
            // Instanciar el controlador de usuario para usar su método de recuperación
            $userController = new UserController();
            $userController->sendPasswordReset($email);
            
            // Mostrar mensaje de éxito (siempre, para no revelar emails existentes)
            $_SESSION['forgot_message'] = 'Si tu correo está registrado, recibirás un enlace para restablecer tu contraseña';
            $_SESSION['forgot_message_type'] = 'success';
            
            header('Location: ' . URLROOT . '/auth/forgotPassword');
            exit;
        }
        
        // Cargar vista de recuperación de contraseña
        $data = [
            'title' => 'Recuperar Contraseña',
            'email' => isset($_SESSION['forgot_data']['email']) ? $_SESSION['forgot_data']['email'] : '',
            'email_err' => isset($_SESSION['forgot_errors']['email_err']) ? $_SESSION['forgot_errors']['email_err'] : ''
        ];
        
        // Limpiar datos temporales de sesión
        unset($_SESSION['forgot_data']);
        unset($_SESSION['forgot_errors']);
        
        // Cargar header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar vista
        include_once APPROOT . '/views/auth/forgotPassword.php';
        
        // Cargar footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Muestra la página de reseteo de contraseña
     */
    public function resetPassword($token = null) {
        // DESHABILITADO TEMPORALMENTE: Redirigir si ya está logueado
        /*
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/user/dashboard');
            exit;
        }
        */
        
        // Obtener token de la URL si no se pasó como parámetro
        if ($token === null) {
            $token = isset($_GET['token']) ? $_GET['token'] : '';
        }
        
        // Verificar token
        if (empty($token)) {
            $_SESSION['login_message'] = 'Token de recuperación inválido o expirado';
            $_SESSION['login_message_type'] = 'danger';
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el reseteo de contraseña
            $token = isset($_POST['token']) ? $_POST['token'] : '';
            $newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : '';
            $confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : '';
            
            // Validar datos
            $errors = [];
            
            if (empty($newPassword)) {
                $errors['password_err'] = 'Por favor ingrese su nueva contraseña';
            } elseif (strlen($newPassword) < 8) {
                $errors['password_err'] = 'La contraseña debe tener al menos 8 caracteres';
            }
            
            if (empty($confirmPassword)) {
                $errors['confirm_password_err'] = 'Por favor confirme su contraseña';
            } elseif ($newPassword !== $confirmPassword) {
                $errors['confirm_password_err'] = 'Las contraseñas no coinciden';
            }
            
            // Si no hay errores, resetear la contraseña
            if (empty($errors)) {
                // Instanciar el controlador de usuario para usar su método de reseteo
                $userController = new UserController();
                if ($userController->resetPassword($token, $newPassword)) {
                    // Éxito
                    $_SESSION['login_message'] = 'Tu contraseña ha sido restablecida correctamente';
                    $_SESSION['login_message_type'] = 'success';
                    header('Location: ' . URLROOT . '/auth/login');
                } else {
                    // Error
                    $_SESSION['reset_message'] = 'El token no es válido o ha expirado';
                    $_SESSION['reset_message_type'] = 'danger';
                    header('Location: ' . URLROOT . '/auth/resetPassword?token=' . $token);
                }
                exit;
            } else {
                // Hay errores, guardarlos en sesión
                $_SESSION['reset_errors'] = $errors;
                header('Location: ' . URLROOT . '/auth/resetPassword?token=' . $token);
                exit;
            }
        }
        
        // Cargar vista de reseteo de contraseña
        $data = [
            'title' => 'Restablecer Contraseña',
            'token' => $token
        ];
        
        // Cargar header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar vista
        include_once APPROOT . '/views/auth/resetPassword.php';
        
        // Cargar footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Si se desea destruir la sesión completamente, borrar también la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Finalmente, destruir la sesión
        session_destroy();
        
        // Redirigir al login
        header('Location: ' . URLROOT . '/auth/login');
        exit;
    }
}