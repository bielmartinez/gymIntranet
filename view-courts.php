<?php
// Incluye las dependencias requeridas
require_once 'app/config/config.php';
require_once 'app/libraries/Database.php'; 
require_once 'app/models/Court.php';

// Configuración para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir constantes
define('URLROOT', 'http://localhost/gymIntranet');
define('APPROOT', dirname(__FILE__) . '/app');
define('SITENAME', 'GymIntranet');

// Incluir encabezado
include_once __DIR__ . '/app/views/shared/header/main.php';

// Cargar el archivo de vista de pistas
$filePath = __DIR__ . '/app/views/users/courts.php';

if (file_exists($filePath)) {
    // Incluir la vista de pistas
    include_once $filePath;
} else {
    // Mostrar error si el archivo no existe
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>Error: Archivo no encontrado</h4>";
    echo "<p>No se pudo encontrar el archivo de vista: $filePath</p>";
    echo "</div>";
    echo "</div>";
}

// Incluir pie de página
include_once __DIR__ . '/app/views/shared/footer/main.php';
?>