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
    private $reservationModel;
    private $classModel;
    
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
        
        // Inicializar los otros modelos solo si son necesarios
        if (file_exists(APPROOT . '/models/Reservation.php')) {
            require_once APPROOT . '/models/Reservation.php';
            $this->reservationModel = new Reservation();
        }
        
        if (file_exists(APPROOT . '/models/Class.php')) {
            require_once APPROOT . '/models/Class.php';
            $this->classModel = new Class_();
        }
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
        // Cargar reservas del usuario
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        $userReservations = [];
        
        if (isset($_SESSION['user_id'])) {
            $userReservations = $reservationModel->findByUserId($_SESSION['user_id']);
        }
        
        // Cargar clases de los próximos 3 días
        require_once APPROOT . '/models/Class.php';
        $classModel = new Class_();
        $upcomingClasses = $classModel->getUpcomingClasses(3); // Próximos 3 días
        
        $data = [
            'title' => 'Dashboard',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario',
            'user_reservations' => $userReservations,
            'upcoming_classes' => $upcomingClasses
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
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        // Cargar modelo de tipos de clases
        require_once APPROOT . '/models/TypeClass.php';
        $typeClassModel = new TypeClass();
        
        // Obtener todos los tipos de clases
        $classTypes = $typeClassModel->getAll();
        
        // Obtener las clases disponibles y las reservas del usuario
        $availableClasses = $reservationModel->getAvailableClasses();
        $userReservations = [];
        
        if (isset($_SESSION['user_id'])) {
            $userReservations = $reservationModel->findByUserId($_SESSION['user_id']);
        }
        
        $data = [
            'title' => 'Clases',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario',
            'available_classes' => $availableClasses,
            'user_reservations' => $userReservations,
            'class_types' => $classTypes
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/classes.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Filtrar clases por fecha
     */
    public function filterClasses() {
        // Verificar si se recibió una fecha
        if (!isset($_POST['date']) || empty($_POST['date'])) {
            // Redirigir a la página de clases sin filtro
            header('Location: ' . URLROOT . '/user/classes');
            exit;
        }
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        // Cargar modelo de tipos de clases
        require_once APPROOT . '/models/TypeClass.php';
        $typeClassModel = new TypeClass();
        
        // Obtener todos los tipos de clases
        $classTypes = $typeClassModel->getAll();
        
        // Obtener la fecha del formulario
        $filterDate = $_POST['date'];
        
        // Obtener las clases disponibles para esa fecha
        $availableClasses = $reservationModel->getAvailableClasses($filterDate);
        $userReservations = [];
        
        if (isset($_SESSION['user_id'])) {
            $userReservations = $reservationModel->findByUserId($_SESSION['user_id']);
        }
        
        $data = [
            'title' => 'Clases - ' . $filterDate,
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario',
            'available_classes' => $availableClasses,
            'user_reservations' => $userReservations,
            'filter_date' => $filterDate,
            'class_types' => $classTypes
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/classes.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Reservar una clase
     */
    public function reserveClass() {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Debes iniciar sesión para reservar una clase';
            $_SESSION['message_type'] = 'danger';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'Debes iniciar sesión para reservar una clase';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        
        // Verificar si se recibió un ID de clase
        if (!isset($_POST['class_id']) || empty($_POST['class_id'])) {
            $_SESSION['message'] = 'No se ha seleccionado ninguna clase';
            $_SESSION['message_type'] = 'danger';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'No se ha seleccionado ninguna clase';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/classes');
            exit;
        }
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        $classId = $_POST['class_id'];
        $userId = $_SESSION['user_id'];
        
        // Verificar si el usuario ya tiene una reserva para esta clase
        if ($reservationModel->userHasReservation($userId, $classId)) {
            $_SESSION['message'] = 'Ya tienes una reserva para esta clase';
            $_SESSION['message_type'] = 'warning';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'Ya tienes una reserva para esta clase';
            $_SESSION['toast_type'] = 'warning';
            header('Location: ' . URLROOT . '/user/classes');
            exit;
        }
        
        // Verificar si la clase tiene plazas disponibles
        if (!$reservationModel->isClassAvailable($classId)) {
            $_SESSION['message'] = 'Esta clase no tiene plazas disponibles';
            $_SESSION['message_type'] = 'danger';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'Esta clase no tiene plazas disponibles';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/classes');
            exit;
        }
        
        // Crear la reserva
        $reservationModel->setUserId($userId);
        $reservationModel->setClassId($classId);
        $reservationModel->setAttendance(0); // No ha asistido todavía
        
        if ($reservationModel->create()) {
            $_SESSION['message'] = 'Reserva realizada con éxito';
            $_SESSION['message_type'] = 'success';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'Reserva realizada con éxito';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error al realizar la reserva';
            $_SESSION['message_type'] = 'danger';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'Error al realizar la reserva';
            $_SESSION['toast_type'] = 'error';
        }
        
        header('Location: ' . URLROOT . '/user/classes');
        exit;
    }
    
    /**
     * Cancelar una reserva
     */
    public function cancelReservation() {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Debes iniciar sesión para cancelar una reserva';
            $_SESSION['message_type'] = 'danger';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'Debes iniciar sesión para cancelar una reserva';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        
        // Verificar si se recibió un ID de reserva
        if (!isset($_POST['reservation_id']) || empty($_POST['reservation_id'])) {
            $_SESSION['message'] = 'No se ha seleccionado ninguna reserva';
            $_SESSION['message_type'] = 'danger';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'No se ha seleccionado ninguna reserva';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/classes');
            exit;
        }
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        $reservationId = $_POST['reservation_id'];
        
        // Verificar que la reserva pertenezca al usuario actual
        $reservationModel->findById($reservationId);
        
        if ($reservationModel->getUserId() != $_SESSION['user_id']) {
            $_SESSION['message'] = 'No tienes permiso para cancelar esta reserva';
            $_SESSION['message_type'] = 'danger';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'No tienes permiso para cancelar esta reserva';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . URLROOT . '/user/classes');
            exit;
        }
        
        // Cancelar la reserva
        $reservationModel->setId($reservationId);
        if ($reservationModel->cancel()) {
            $_SESSION['message'] = 'Reserva cancelada con éxito';
            $_SESSION['message_type'] = 'success';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'Reserva cancelada con éxito';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error al cancelar la reserva';
            $_SESSION['message_type'] = 'danger';
            // Añadir notificación toast
            $_SESSION['toast_message'] = 'Error al cancelar la reserva';
            $_SESSION['toast_type'] = 'error';
        }
        
        header('Location: ' . URLROOT . '/user/classes');
        exit;
    }
    
    /**
     * Ver las reservas del usuario
     */
    public function myReservations() {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Debes iniciar sesión para ver tus reservas';
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        
        // Cargar modelo de reservas
        require_once APPROOT . '/models/Reservation.php';
        $reservationModel = new Reservation();
        
        // Obtener las reservas del usuario
        $userReservations = $reservationModel->findByUserId($_SESSION['user_id']);
        
        $data = [
            'title' => 'Mis Reservas',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario',
            'reservations' => $userReservations
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista
        include_once APPROOT . '/views/users/my_reservations.php';
        
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
     * Método profile redirige al dashboard ya que se ha eliminado la funcionalidad de perfil
     */
    public function profile() {
        // Redirigir al dashboard ya que la funcionalidad de perfil ha sido eliminada
        $_SESSION['toast_message'] = 'La sección de perfil no está disponible';
        $_SESSION['toast_type'] = 'info';
        header('Location: ' . URLROOT . '/user/dashboard');
        exit;
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
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Procesar datos del formulario
            $data = [
                'id' => $_SESSION['user_id'],
                'name' => trim($_POST['name']),
                'lastname' => trim($_POST['lastname']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'birthdate' => trim($_POST['birthdate'])
            ];
            
            // Validación de datos
            if (empty($data['name']) || empty($data['lastname']) || empty($data['email'])) {
                flash('profile_message', 'Por favor, rellene todos los campos obligatorios', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Por favor, rellene todos los campos obligatorios';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/users/profile');
                exit;
            }
            
            // Actualizar perfil
            if ($this->userModel->updateProfile($data)) {
                // Actualizar los datos de la sesión
                $_SESSION['user_name'] = $data['name'];
                $_SESSION['user_lastname'] = $data['lastname'];
                $_SESSION['user_email'] = $data['email'];
                
                flash('profile_message', 'Perfil actualizado con éxito', 'alert alert-success');
                $_SESSION['toast_message'] = 'Perfil actualizado correctamente';
                $_SESSION['toast_type'] = 'success';
            } else {
                flash('profile_message', 'Error al actualizar el perfil', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Error al actualizar el perfil';
                $_SESSION['toast_type'] = 'error';
            }
            
            header('Location: ' . URLROOT . '/users/profile');
            exit;
        }
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Procesar datos del formulario
            $data = [
                'id' => $_SESSION['user_id'],
                'current_password' => trim($_POST['current_password']),
                'new_password' => trim($_POST['new_password']),
                'confirm_password' => trim($_POST['confirm_password'])
            ];
            
            // Validación de datos
            if (empty($data['current_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
                flash('password_message', 'Por favor, rellene todos los campos', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Por favor, rellene todos los campos';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/users/profile');
                exit;
            }
            
            if ($data['new_password'] !== $data['confirm_password']) {
                flash('password_message', 'Las contraseñas no coinciden', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Las contraseñas no coinciden';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/users/profile');
                exit;
            }
            
            if (strlen($data['new_password']) < 6) {
                flash('password_message', 'La contraseña debe tener al menos 6 caracteres', 'alert alert-danger');
                $_SESSION['toast_message'] = 'La contraseña debe tener al menos 6 caracteres';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/users/profile');
                exit;
            }
            
            // Verificar la contraseña actual
            $user = $this->userModel->getUserById($data['id']);
            if (!$user || !password_verify($data['current_password'], $user->contrasenya)) {
                flash('password_message', 'Contraseña actual incorrecta', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Contraseña actual incorrecta';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/users/profile');
                exit;
            }
            
            // Cambiar contraseña
            if ($this->userModel->changePassword($data['id'], $data['new_password'])) {
                flash('password_message', 'Contraseña cambiada con éxito', 'alert alert-success');
                $_SESSION['toast_message'] = 'Contraseña cambiada correctamente';
                $_SESSION['toast_type'] = 'success';
            } else {
                flash('password_message', 'Error al cambiar la contraseña', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Error al cambiar la contraseña';
                $_SESSION['toast_type'] = 'error';
            }
            
            header('Location: ' . URLROOT . '/users/profile');
            exit;
        }
    }
    
    /**
     * Procesar reserva de clase
     */
    public function processReservation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $classId = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
            $userId = $_SESSION['user_id'];
            
            if (empty($classId)) {
                flash('reservation_message', 'Error al procesar la reserva', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Error al procesar la reserva';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/users/classes');
                exit;
            }
            
            // Verificar si la clase existe y tiene capacidad disponible
            $class = $this->classModel->getClassById($classId);
            if (!$class || $class->capacitat_actual >= $class->capacitat_maxima) {
                flash('reservation_message', 'No hay plazas disponibles para esta clase', 'alert alert-danger');
                $_SESSION['toast_message'] = 'No hay plazas disponibles para esta clase';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . URLROOT . '/users/classes');
                exit;
            }
            
            // Verificar si el usuario ya tiene una reserva para esta clase
            if ($this->reservationModel->hasUserReservation($userId, $classId)) {
                flash('reservation_message', 'Ya tienes una reserva para esta clase', 'alert alert-warning');
                $_SESSION['toast_message'] = 'Ya tienes una reserva para esta clase';
                $_SESSION['toast_type'] = 'warning';
                header('Location: ' . URLROOT . '/users/classes');
                exit;
            }
            
            // Procesar la reserva
            if ($this->reservationModel->addReservation($userId, $classId)) {
                // Incrementar el contador de capacidad actual
                if ($this->classModel->incrementCapacity($classId)) {
                    flash('reservation_message', 'Reserva realizada con éxito', 'alert alert-success');
                    $_SESSION['toast_message'] = 'Reserva realizada correctamente';
                    $_SESSION['toast_type'] = 'success';
                } else {
                    // Rollback: eliminar la reserva si no se pudo actualizar la capacidad
                    $this->reservationModel->deleteReservation($userId, $classId);
                    flash('reservation_message', 'Error al procesar la reserva', 'alert alert-danger');
                    $_SESSION['toast_message'] = 'Error al procesar la reserva';
                    $_SESSION['toast_type'] = 'error';
                }
            } else {
                flash('reservation_message', 'Error al procesar la reserva', 'alert alert-danger');
                $_SESSION['toast_message'] = 'Error al procesar la reserva';
                $_SESSION['toast_type'] = 'error';
            }
            
            header('Location: ' . URLROOT . '/users/classes');
            exit;
        }
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
        $subject = "Benvingut/da a Gym Intranet!";
        
        $body = "
        <html>
        <head>
            <title>Benvingut/da a Gym Intranet!</title>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; background-color: #f9f9f9; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
                .header { background: linear-gradient(135deg, #150000 0%, #3a0000 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; letter-spacing: 1px; }
                .header img { max-width: 120px; margin-bottom: 15px; }
                .content { padding: 30px; background-color: #ffffff; }
                .welcome-message { font-size: 18px; margin-bottom: 25px; color: #444; text-align: center; }
                .info-box { background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #150000; }
                .benefits { margin: 30px 0; }
                .benefit-item { display: flex; align-items: center; margin-bottom: 15px; }
                .benefit-icon { width: 30px; text-align: center; margin-right: 15px; color: #150000; font-size: 20px; }
                .button-container { text-align: center; margin: 30px 0; }
                .button { display: inline-block; background-color: #150000; color: white; text-decoration: none; padding: 12px 30px; border-radius: 30px; font-weight: bold; transition: background-color 0.3s; }
                .button:hover { background-color: #3a0000; }
                .social-links { text-align: center; margin-top: 30px; }
                .social-links a { display: inline-block; margin: 0 10px; color: #444; text-decoration: none; }
                .social-icon { font-size: 24px; }
                .footer { background-color: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>BENVINGUT/DA A GYM INTRANET!</h1>
                </div>
                <div class='content'>
                    <p class='welcome-message'>Hola <strong>{$userData['fullName']}</strong>, ens alegra tenir-te amb nosaltres!</p>
                    
                    <p>El teu compte ha estat creat amb èxit i ja pots començar a gaudir de tots els beneficis del nostre centre esportiu.</p>
                    
                    <div class='info-box'>
                        <h3>💡 INFORMACIÓ D'ACCÉS</h3>
                        <p>Pots iniciar sessió a la nostra plataforma amb les següents dades:</p>
                        <p><strong>Email:</strong> {$userData['email']}</p>
                        <p><strong>Contrasenya:</strong> La que has establert durant el registre</p>
                    </div>
                    
                    <div class='benefits'>
                        <h3>QUÈ POTS FER A LA NOSTRA PLATAFORMA?</h3>
                        <div class='benefit-item'>
                            <div class='benefit-icon'>🏋️</div>
                            <div>Reservar classes dirigides amb els nostres millors instructors</div>
                        </div>
                        <div class='benefit-item'>
                            <div class='benefit-icon'>🎾</div>
                            <div>Reservar pistes esportives per a les teves activitats favorites</div>
                        </div>
                        <div class='benefit-item'>
                            <div class='benefit-icon'>📊</div>
                            <div>Fer seguiment del teu progrés físic personal</div>
                        </div>
                        <div class='benefit-item'>
                            <div class='benefit-icon'>📱</div>
                            <div>Accedir a la teva informació des de qualsevol dispositiu</div>
                        </div>
                    </div>
                    
                    <div class='button-container'>
                        <a href='".URLROOT."/auth/login' class='button'>ACCEDIR ARA</a>
                    </div>
                    
                    <p>Si tens alguna pregunta o necessites ajuda, no dubtis en contactar amb el nostre equip de suport.</p>
                    
                    <div class='social-links'>
                        <p>Segueix-nos a les xarxes socials:</p>
                        <a href='#' class='social-icon'>📱</a>
                        <a href='#' class='social-icon'>📘</a>
                        <a href='#' class='social-icon'>📸</a>
                    </div>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " Gym Intranet. Tots els drets reservats.</p>
                    <p>Aquest és un missatge automàtic, si us plau no responguis a aquest correu.</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Enviar email usando PHPMailer
        return MailService::sendMail($to, $subject, $body);
    }
    
    /**
     * Muestra las notificaciones del usuario (versión simplificada)
     * @param int $page Página actual (para paginación)
     */
    public function notifications($page = 1) {
        // Cargar modelo de notificaciones
        require_once APPROOT . '/models/Notification.php';
        $notificationModel = new Notification();
        
        // Configuración de paginación
        $perPage = 10;
        
        // Obtener todas las notificaciones (ahora simplificado, sin filtrar por usuario)
        $allNotifications = $notificationModel->getAllNotifications();
        $totalNotifications = count($allNotifications);
        $totalPages = ceil($totalNotifications / $perPage);
        
        // Ajustar la página actual si es necesario
        if ($page < 1) $page = 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;
        
        // Obtener solo las notificaciones para la página actual
        $offset = ($page - 1) * $perPage;
        $notifications = array_slice($allNotifications, $offset, $perPage);
        
        $data = [
            'title' => 'Notificaciones',
            'notifications' => $notifications,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalNotifications' => $totalNotifications
        ];
        
        // Cargar el header
        include_once APPROOT . '/views/shared/header/main.php';
        
        // Cargar la vista de notificaciones
        include_once APPROOT . '/views/users/notifications.php';
        
        // Cargar el footer
        include_once APPROOT . '/views/shared/footer/main.php';
    }
    
    /**
     * Obtiene el número de notificaciones disponibles
     * Versión simplificada que ahora devuelve todas las notificaciones
     * @return int Número de notificaciones
     */
    public function getUnreadNotificationsCount() {
        // Cargar modelo de notificaciones
        require_once APPROOT . '/models/Notification.php';
        $notificationModel = new Notification();
        
        // Obtener conteo total de notificaciones (simplificado)
        return $notificationModel->getNotificationsCount();
    }

    /**
     * Marca una notificación como leída (mediante AJAX)
     * @param int $notificationId ID de la notificación a marcar
     */
    public function markNotificationAsRead($notificationId = null) {
        // Verificar si es una solicitud AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => 'Solicitud no permitida']);
            return;
        }
        
        // Verificar si hay un usuario logueado
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }
        
        // Verificar ID de notificación
        if (!$notificationId) {
            // Intentar obtenerlo del cuerpo de la solicitud
            $json = file_get_contents('php://input');
            $data = json_decode($json);
            $notificationId = $data->notificationId ?? null;
            
            if (!$notificationId) {
                echo json_encode(['success' => false, 'message' => 'ID de notificación no proporcionado']);
                return;
            }
        }
        
        // Cargar modelo de notificaciones
        require_once APPROOT . '/models/Notification.php';
        $notificationModel = new Notification();
        
        // Marcar como leída
        $success = $notificationModel->markAsRead($notificationId, $_SESSION['user_id']);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Notificación marcada como leída']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al marcar la notificación']);
        }
    }
    
    /**
     * Descarta una notificación (mediante AJAX)
     * @param int $notificationId ID de la notificación a descartar
     */
    public function dismissNotification($notificationId = null) {
        // Verificar si es una solicitud AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => 'Solicitud no permitida']);
            return;
        }
        
        // Verificar si hay un usuario logueado
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }
        
        // Verificar ID de notificación
        if (!$notificationId) {
            // Intentar obtenerlo del cuerpo de la solicitud
            $json = file_get_contents('php://input');
            $data = json_decode($json);
            $notificationId = $data->notificationId ?? null;
            
            if (!$notificationId) {
                echo json_encode(['success' => false, 'message' => 'ID de notificación no proporcionado']);
                return;
            }
        }
        
        // Cargar modelo de notificaciones
        require_once APPROOT . '/models/Notification.php';
        $notificationModel = new Notification();
        
        // Descartar notificación
        $success = $notificationModel->dismissNotification($notificationId, $_SESSION['user_id']);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Notificación descartada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al descartar la notificación']);
        }
    }
}
