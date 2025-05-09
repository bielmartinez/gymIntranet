<?php
/**
 * BaseController.php
 * 
 * Controlador base que proporciona funcionalidad común para todos los controladores
 * del sistema GymIntranet. Implementa métodos para cargar vistas, manejar errores,
 * redireccionar y validar datos.
 */

abstract class BaseController {
    /**
     * Almacena los mensajes de error
     * @var array
     */
    protected $errors = [];
    
    /**
     * Almacena los datos para la vista
     * @var array
     */
    protected $viewData = [];
    
    /**
     * Constructor del controlador base
     */
    public function __construct() {
        // Inicializar los datos de la vista con valores comunes
        $this->viewData = [
            'title' => SITENAME,
            'errors' => []
        ];
        
        // Añadir información del usuario si está autenticado
        if (isset($_SESSION['user_id'])) {
            $this->viewData['user_id'] = $_SESSION['user_id'];
            $this->viewData['user_name'] = $_SESSION['user_name'] ?? '';
            $this->viewData['user_role'] = $_SESSION['user_role'] ?? '';
        }
    }
    
    /**
     * Método abstracto para la acción principal del controlador
     * Debe ser implementado por todos los controladores hijos
     */
    abstract public function index();
    
    /**
     * Carga una vista con los datos proporcionados
     * @param string $view Nombre de la vista (sin extensión)
     * @param array $data Datos adicionales para la vista (opcional)
     * @return void
     */
    protected function loadView($view, $data = []) {
        // Combinar los datos proporcionados con los datos de la vista
        $viewData = array_merge($this->viewData, $data);
        
        // Extraer los datos para que sean accesibles como variables en la vista
        extract($viewData);
        
        // Determinar la ruta completa de la vista
        $viewPath = APPROOT . '/views/' . $view . '.php';
        
        // Verificar si existe la vista
        if (file_exists($viewPath)) {
            // Cargar el header
            include_once APPROOT . '/views/shared/header/main.php';
            
            // Cargar la vista
            include_once $viewPath;
            
            // Cargar el footer
            include_once APPROOT . '/views/shared/footer/main.php';
        } else {
            // Si la vista no existe, mostrar error
            die('La vista ' . $view . ' no existe');
        }
    }
    
    /**
     * Carga solo una vista sin header ni footer
     * @param string $view Nombre de la vista (sin extensión)
     * @param array $data Datos adicionales para la vista (opcional)
     * @return void
     */
    protected function loadPartialView($view, $data = []) {
        // Combinar los datos proporcionados con los datos de la vista
        $viewData = array_merge($this->viewData, $data);
        
        // Extraer los datos para que sean accesibles como variables en la vista
        extract($viewData);
        
        // Determinar la ruta completa de la vista
        $viewPath = APPROOT . '/views/' . $view . '.php';
        
        // Verificar si existe la vista
        if (file_exists($viewPath)) {
            // Cargar la vista
            include_once $viewPath;
        } else {
            // Si la vista no existe, mostrar error
            die('La vista ' . $view . ' no existe');
        }
    }
    
    /**
     * Redirecciona a una URL
     * @param string $url URL a la que redireccionar
     * @return void
     */
    protected function redirect($url) {
        header('Location: ' . URLROOT . '/' . $url);
        exit;
    }
    
    /**
     * Verifica si una petición es POST
     * @return bool True si es POST, false en caso contrario
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verifica si una petición es GET
     * @return bool True si es GET, false en caso contrario
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Valida un valor no vacío
     * @param string $value Valor a validar
     * @param string $field Nombre del campo para mensajes de error
     * @return bool True si es válido, false en caso contrario
     */
    protected function validateRequired($value, $field) {
        if (empty($value)) {
            $this->errors[$field . '_err'] = 'El campo ' . $field . ' es obligatorio';
            return false;
        }
        return true;
    }
    
    /**
     * Valida un email
     * @param string $email Email a validar
     * @param string $field Nombre del campo para mensajes de error
     * @return bool True si es válido, false en caso contrario
     */
    protected function validateEmail($email, $field = 'email') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field . '_err'] = 'Por favor, introduce un email válido';
            return false;
        }
        return true;
    }
    
    /**
     * Valida la longitud mínima de un valor
     * @param string $value Valor a validar
     * @param string $field Nombre del campo para mensajes de error
     * @param int $min Longitud mínima
     * @return bool True si es válido, false en caso contrario
     */
    protected function validateMinLength($value, $field, $min) {
        if (strlen($value) < $min) {
            $this->errors[$field . '_err'] = 'El campo ' . $field . ' debe tener al menos ' . $min . ' caracteres';
            return false;
        }
        return true;
    }
    
    /**
     * Verifica si hay errores de validación
     * @return bool True si hay errores, false en caso contrario
     */
    protected function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Obtiene los errores de validación
     * @return array Errores de validación
     */
    protected function getErrors() {
        return $this->errors;
    }
    
    /**
     * Limpia y sanea una cadena
     * @param string $input Cadena a sanear
     * @return string Cadena saneada
     */
    protected function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }
    
    /**
     * Verifica que el usuario tenga el rol requerido
     * @param array|string $roles Rol o roles permitidos
     * @return bool True si el usuario tiene el rol, false en caso contrario
     */
    protected function verifyRole($roles) {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        
        if (is_array($roles)) {
            return in_array($_SESSION['user_role'], $roles);
        }
        
        return $_SESSION['user_role'] === $roles;
    }
    
    /**
     * Verifica que el usuario esté autenticado, redirige si no lo está
     * @param string $redirectUrl URL a la que redireccionar si no está autenticado
     * @return bool True si está autenticado, false en caso contrario
     */
    protected function requireAuth($redirectUrl = '') {
        if (!isset($_SESSION['user_id'])) {
            if ($redirectUrl) {
                $this->redirect($redirectUrl);
            } else {
                $this->redirect('auth');
            }
            return false;
        }
        return true;
    }
    
    /**
     * Verifica que el usuario tenga el rol requerido, redirige si no lo tiene
     * @param array|string $roles Rol o roles permitidos
     * @param string $redirectUrl URL a la que redireccionar si no tiene el rol
     * @return bool True si tiene el rol, false en caso contrario
     */
    protected function requireRole($roles, $redirectUrl = '') {
        if (!$this->verifyRole($roles)) {
            if ($redirectUrl) {
                $this->redirect($redirectUrl);
            } else {
                $this->redirect('');
            }
            return false;
        }
        return true;
    }
      /**
     * Maneja un error de operación
     * @param string $message Mensaje de error
     * @param string $redirectUrl URL a la que redireccionar (opcional)
     * @return void
     */
    protected function handleError($message, $redirectUrl = '') {
        $_SESSION['error_msg'] = $message;
        // Añadir mensaje toast para notificaciones
        $_SESSION['toast_message'] = $message;
        $_SESSION['toast_type'] = 'error';
        
        if ($redirectUrl) {
            $this->redirect($redirectUrl);
        }
    }
    
    /**
     * Maneja un éxito de operación
     * @param string $message Mensaje de éxito
     * @param string $redirectUrl URL a la que redireccionar (opcional)
     * @return void
     */
    protected function handleSuccess($message, $redirectUrl = '') {
        $_SESSION['success_msg'] = $message;
        // Añadir mensaje toast para notificaciones
        $_SESSION['toast_message'] = $message;
        $_SESSION['toast_type'] = 'success';
        
        if ($redirectUrl) {
            $this->redirect($redirectUrl);
        }
    }
    
    /**
     * Devuelve una respuesta JSON
     * @param array $data Datos a devolver
     * @param int $statusCode Código de estado HTTP (opcional)
     * @return void
     */
    protected function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
