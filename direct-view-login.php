<?php
// Este archivo es mantenido sólo para propósitos de compatibilidad con versiones anteriores.
// Redirige las solicitudes antiguas de login a la nueva estructura MVC.

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir constantes del sistema con el puerto correcto
define('URLROOT', 'http://localhost:8080/gymIntranet/gymIntranet');
define('APPROOT', dirname(__FILE__) . '/app');
define('SITENAME', 'GymIntranet');

// Procesar solo solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Redirigir los datos del formulario a la nueva ruta de login
    $_SESSION['login_redirect_data'] = $_POST;
    
    // Redirigir a la nueva ruta de login
    header('Location: ' . URLROOT . '/auth/login');
    exit;
} else {
    // Si no es una solicitud POST, redirigir al login
    header('Location: ' . URLROOT . '/auth/login');
    exit;
}