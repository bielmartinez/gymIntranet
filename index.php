<?php
/**
 * Punto de entrada principal para GymIntranet
 * Este archivo procesa todas las solicitudes y carga el controlador y acción correspondientes
 */

// Configuración para mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración
require_once 'app/config/config.php';

// Función para cargar clases automáticamente
spl_autoload_register(function($className) {
    // Mapear controladores
    if (strpos($className, 'Controller') !== false) {
        if (file_exists(APPROOT . '/controllers/' . $className . '.php')) {
            require_once APPROOT . '/controllers/' . $className . '.php';
            return;
        }
    }
    
    // Mapear modelos
    if (file_exists(APPROOT . '/models/' . $className . '.php')) {
        require_once APPROOT . '/models/' . $className . '.php';
        return;
    }
    
    // Mapear utilidades
    if (file_exists(APPROOT . '/utils/' . $className . '.php')) {
        require_once APPROOT . '/utils/' . $className . '.php';
        return;
    }
    
    // Mapear librerías
    if (file_exists(APPROOT . '/libraries/' . $className . '.php')) {
        require_once APPROOT . '/libraries/' . $className . '.php';
        return;
    }
});

// Incluir funciones útiles
include_once APPROOT . '/utils/Logger.php';
Logger::log('INFO', 'Iniciando aplicación desde index.php');

// Procesar URL para determinar controlador/acción
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
Logger::log('INFO', 'URL solicitada: ' . $url);

// Añadir registro detallado para depuración
Logger::log('DEBUG', 'Método de solicitud: ' . $_SERVER['REQUEST_METHOD']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Logger::log('DEBUG', 'Datos POST: ' . print_r($_POST, true));
}

// Dividir la URL en segmentos
$urlParts = explode('/', $url);

// Determinar controlador, acción y parámetros
$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
$action = !empty($urlParts[1]) ? $urlParts[1] : 'index';
$params = array_slice($urlParts, 2);

// Si la acción es 'register' y el controlador es 'AdminController', usar el método registerForm 
// para GET y register para POST
if ($controllerName === 'AdminController' && $action === 'register') {
    // Usar registerForm para GET y register para POST
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = 'registerForm';
    }
    Logger::log('INFO', "Acción modificada para registro: $action (Método: {$_SERVER['REQUEST_METHOD']})");
}

// Si no hay controlador especificado, cargar la página de inicio 
// Para usuarios no autenticados, redirigir al login
if (empty($urlParts[0])) {
    if (!isset($_SESSION['user_id'])) {
        $controllerName = 'AuthController';
        $action = 'login';
    } else {
        $controllerName = 'UserController';
        $action = 'dashboard';
    }
}

// Verificar si el controlador existe
if (!file_exists(APPROOT . '/controllers/' . $controllerName . '.php')) {
    // Si no existe, mostrar página de error 404
    Logger::log('ERROR', "Controlador no encontrado: {$controllerName}");
    header("HTTP/1.0 404 Not Found");
    include_once APPROOT . '/views/shared/error/404.php';
    exit;
}

// Verificar autenticación para rutas protegidas
// Solo permitir acceso sin autenticación a AuthController
if ($controllerName !== 'AuthController' && !isset($_SESSION['user_id'])) {
    Logger::log('INFO', "Redirigiendo a login: usuario no autenticado intentando acceder a {$controllerName}->{$action}");
    header('Location: ' . URLROOT . '/auth/login');
    exit;
}

// Crear instancia del controlador
Logger::log('INFO', "Instanciando controlador: {$controllerName}");
$controller = new $controllerName();

// Verificar si el método existe
if (!method_exists($controller, $action)) {
    // Si no existe, mostrar página de error 404
    Logger::log('ERROR', "Método no encontrado: {$controllerName}->{$action}()");
    header("HTTP/1.0 404 Not Found");
    include_once APPROOT . '/views/shared/error/404.php';
    exit;
}

// Ejecutar acción del controlador con parámetros
Logger::log('INFO', "Ejecutando acción: {$controllerName}->{$action}(" . implode(', ', $params) . ")");
call_user_func_array([$controller, $action], $params);
