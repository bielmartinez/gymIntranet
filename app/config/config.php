<?php
// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ddb253123');

// App Root
define('APPROOT', dirname(dirname(__FILE__)));

// URL Root - configuración dinámica para detectar el puerto correctamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST']; 
define('URLROOT', $protocol . '://' . $host . '/gymIntranet');

// Site Name
define('SITENAME', 'Gym Intranet');

// Configuración adicional para la estructura MVC
define('URL_SUBFOLDER', 'gymIntranet/gymIntranet');  // Para URLs (sin http://)
?>
