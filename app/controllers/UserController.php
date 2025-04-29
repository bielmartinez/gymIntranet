<?php
/**
 * Controlador para la gestión de usuarios
 */
// Agregar el require de MailService
require_once dirname(__DIR__) . '/utils/MailService.php';

// Requires para PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController {
    private $userModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        // DESHABILITADO TEMPORALMENTE: Verificar autenticación para la mayoría de las acciones
        // Excepto las que están en el array de métodos públicos
        /*
        $publicMethods = ['register', 'login', 'sendPasswordReset', 'resetPassword'];
        
        $currentMethod = isset($_GET['url']) ? explode('/', $_GET['url'])[1] ?? 'index' : 'index';
        
        if (!in_array($currentMethod, $publicMethods) && !isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        */
        
        $this->userModel = new User();
    }
    
    /**
     * Método por defecto - Redirige al dashboard
     */
    public function index() {
        $this->dashboard();
    }
    
    /**
     * Muestra el dashboard del usuario
     */
    public function dashboard() {
        $data = [
            'title' => 'Dashboard',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/dashboard.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Muestra la página de clases
     */
    public function classes() {
        $data = [
            'title' => 'Clases',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'
        ];
        
        // Aquí se cargarían las clases desde el modelo
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/classes.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Muestra la página de pistas
     */
    public function courts() {
        $data = [
            'title' => 'Pistas',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'
        ];
        
        // Aquí se cargarían las pistas desde el modelo
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/courts.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Muestra la página de tracking
     */
    public function tracking() {
        $data = [
            'title' => 'Seguimiento Físico',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'
        ];
        
        // Aquí se cargarían los datos de tracking desde el modelo
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/tracking.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Muestra la página de perfil
     */
    public function profile() {
        $data = [
            'title' => 'Mi Perfil',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'
        ];
        
        // Aquí se cargarían los datos del perfil desde el modelo
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/profile.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Maneja el registro de nuevos usuarios
     * Solo administradores pueden registrar nuevos usuarios
     * 
     * @param array $userData Datos del usuario a registrar
     * @return bool Éxito o fracaso del registro
     */
    public function register($userData) {
        // Añadir logging para depuración
        Logger::log('INFO', 'Iniciando registro de usuario: ' . $userData['email']);
        Logger::log('DEBUG', 'Datos recibidos: ' . print_r($userData, true));
        
        // DESHABILITADO TEMPORALMENTE: Verificar que el usuario actual sea administrador
        /*
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['register_message'] = 'No tienes permisos para registrar usuarios';
            $_SESSION['register_message_type'] = 'danger';
            return false;
        }
        */
        
        // Validación de datos
        $errors = [];
        
        if(empty($userData['email'])) {
            $errors['email_err'] = 'El correo electrónico es obligatorio';
        } else {
            // Verificar formato de email
            if(!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email_err'] = 'Formato de correo electrónico inválido';
            }
            
            // Verificar si el email ya existe
            if($this->userModel->findByEmail($userData['email'])) {
                $errors['email_err'] = 'Este correo electrónico ya está registrado';
            }
        }
        
        if(empty($userData['password'])) {
            $errors['password_err'] = 'La contraseña es obligatoria';
        } elseif(strlen($userData['password']) < 8) {
            $errors['password_err'] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        if(empty($userData['confirmPassword'])) {
            $errors['confirm_password_err'] = 'Por favor confirma la contraseña';
        } elseif($userData['password'] !== $userData['confirmPassword']) {
            $errors['confirm_password_err'] = 'Las contraseñas no coinciden';
        }
        
        if(empty($userData['fullName'])) {
            $errors['fullName_err'] = 'El nombre completo es obligatorio';
        }
        
        if(empty($userData['role'])) {
            $errors['role_err'] = 'Debes seleccionar un rol para el usuario';
        }
        
        // Si hay errores, devolver false
        if(!empty($errors)) {
            Logger::log('WARNING', 'Errores de validación en registro: ' . print_r($errors, true));
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_data'] = $userData;
            return false;
        }
        
        // Preparar datos para crear el usuario
        $newUser = [
            'email' => $userData['email'],
            'password' => password_hash($userData['password'], PASSWORD_DEFAULT),
            'fullName' => $userData['fullName'],
            'role' => $userData['role'],
            'phone' => $userData['phone'] ?? null,
            'birthDate' => $userData['birthDate'] ?? null,
            'joinDate' => date('Y-m-d H:i:s'),
            'isActive' => true
        ];
        
        Logger::log('DEBUG', 'Intentando crear usuario: ' . print_r($newUser, true));
        
        // Crear el usuario
        if($this->userModel->create($newUser)) {
            Logger::log('INFO', 'Usuario registrado correctamente: ' . $userData['email']);
            // Enviar email de bienvenida si se seleccionó la opción
            if(isset($userData['sendWelcomeEmail']) && $userData['sendWelcomeEmail']) {
                $this->sendWelcomeEmail($newUser);
            }
            
            $_SESSION['register_message'] = 'Usuario registrado correctamente';
            $_SESSION['register_message_type'] = 'success';
            return true;
        } else {
            Logger::log('ERROR', 'Error al registrar usuario: ' . $userData['email']);
            $_SESSION['register_message'] = 'Error al registrar usuario. Inténtalo nuevamente.';
            $_SESSION['register_message_type'] = 'danger';
            return false;
        }
    }
    
    /**
     * Maneja el inicio de sesión
     */
    public function login($username, $password) {
        // Validar credenciales
        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Crear sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullName'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }

        return false;
    }

    /**
     * Envía un correo para restablecer la contraseña
     */
    public function sendPasswordReset($email) {
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            // Generar token único
            $token = bin2hex(random_bytes(16));
            $this->userModel->savePasswordResetToken($user['id'], $token);

            // Generar enlace de restablecimiento
            $resetLink = URLROOT . "/auth/resetPassword/" . $token;
            
            // Construir el contenido del correo
            $subject = "Recuperación de contraseña - Gym Intranet";
            $body = "
            <html>
            <head>
                <title>Recuperación de contraseña - Gym Intranet</title>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #4A90E2; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; background-color: #f8f9fa; }
                    .button { display: inline-block; background-color: #4A90E2; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; }
                    .footer { font-size: 12px; color: #666; text-align: center; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Recuperación de contraseña</h2>
                    </div>
                    <div class='content'>
                        <p>Hola,</p>
                        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en Gym Intranet.</p>
                        <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                        <p style='text-align: center;'>
                            <a href='$resetLink' class='button'>Restablecer contraseña</a>
                        </p>
                        <p>O copia y pega el siguiente enlace en tu navegador:</p>
                        <p>$resetLink</p>
                        <p>Este enlace expirará en 1 hora por motivos de seguridad.</p>
                        <p>Si no solicitaste restablecer tu contraseña, puedes ignorar este correo.</p>
                    </div>
                    <div class='footer'>
                        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                        <p>&copy; " . date('Y') . " Gym Intranet. Todos los derechos reservados.</p>
                    </div>
                </div>
            </body>
            </html>";
            
            // Enviar correo usando PHPMailer
            return MailService::sendMail($email, $subject, $body);
        }
        
        // Devolver true incluso si el email no existe para no revelar qué emails están registrados
        return true;
    }

    /**
     * Restablece la contraseña del usuario
     */
    public function resetPassword($token, $newPassword) {
        $user = $this->userModel->findByResetToken($token);

        if ($user && $this->userModel->isTokenValid($token)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userModel->updatePassword($user['id'], $hashedPassword);
            $this->userModel->clearResetToken($user['id']);
            return true;
        }

        return false;
    }

    /**
     * Crea un nuevo usuario (solo para administradores)
     */
    public function createUser($adminId, $userData) {
        $admin = $this->userModel->findById($adminId);

        if ($admin && $admin['role'] === 'admin') {
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $userData['password'] = $hashedPassword;
            return $this->userModel->create($userData);
        }

        return false;
    }
    
    /**
     * Actualiza la información del perfil
     */
    public function updateProfile($userId, $profileData) {
        // Validación
        // Actualización
        // Retorno de resultados
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

    /**
     * Envía un correo electrónico de bienvenida al usuario recién registrado
     * 
     * @param array $userData Datos del usuario registrado
     * @return bool Éxito o fracaso del envío
     */
    private function sendWelcomeEmail($userData) {
        $to = $userData['email'];
        $subject = "Bienvenido/a a Gym Intranet";
        
        $body = "
        <html>
        <head>
            <title>Bienvenido/a a Gym Intranet</title>
            <style>
                body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4A90E2; color: white; padding: 10px; text-align: center; }
                .content { padding: 20px; background-color: #f8f9fa; }
                .button { display: inline-block; background-color: #4A90E2; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; }
                .footer { font-size: 12px; color: #666; text-align: center; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>¡Bienvenido/a a Gym Intranet!</h2>
                </div>
                <div class='content'>
                    <p>Hola {$userData['fullName']},</p>
                    <p>Tu cuenta ha sido creada exitosamente.</p>
                    <p><strong>Datos de acceso:</strong></p>
                    <ul>
                        <li><strong>Usuario/Email:</strong> {$userData['email']}</li>
                        <li><strong>Contraseña:</strong> La contraseña que estableciste durante el registro</li>
                    </ul>
                    <p>Por favor, guarda esta información en un lugar seguro.</p>
                    <p>Puedes acceder a tu cuenta haciendo clic en el botón a continuación:</p>
                    <p style='text-align: center;'>
                        <a href='".URLROOT."/auth/login' class='button'>Iniciar sesión</a>
                    </p>
                    <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                </div>
                <div class='footer'>
                    <p>Atentamente,<br>El equipo de Gym Intranet</p>
                    <p>&copy; " . date('Y') . " Gym Intranet. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Enviar email usando PHPMailer
        return MailService::sendMail($to, $subject, $body);
    }
}
