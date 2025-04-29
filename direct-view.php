<?php
// Este archivo es mantenido sólo para propósitos de compatibilidad con versiones anteriores.
// Redirigirá todas las solicitudes antiguas a las nuevas rutas MVC.

// Iniciar sesión para mantener el estado del usuario 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir constante de URL
define('URLROOT', 'http://localhost:8080/gymIntranet/gymIntranet');

// Obtener la página solicitada
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Crear un mapa de redirecciones de las viejas rutas a las nuevas
$redirectMap = [
    'dashboard' => 'user/dashboard',
    'classes' => 'user/classes',
    'courts' => 'user/courts',
    'tracking' => 'user/tracking',
    'profile' => 'user/profile',
    'auth/login' => 'auth/login',
    'auth/forgotPassword' => 'auth/forgotPassword',
    'auth/resetPassword' => 'auth/resetPassword'
];

// Determinar la ruta de redirección
$redirectPath = isset($redirectMap[$page]) ? $redirectMap[$page] : '';

// Si no hay redirección específica, intentar inferir basado en el formato de la ruta
if (empty($redirectPath)) {
    if (strpos($page, 'admin/') === 0) {
        // Es una página de administrador
        $redirectPath = $page;
    } elseif (strpos($page, 'auth/') === 0) {
        // Es una página de autenticación
        $redirectPath = $page;
    } elseif (strpos($page, 'staff/') === 0) {
        // Es una página de personal
        $redirectPath = $page;
    } else {
        // Por defecto, asumir que es una página de usuario
        $redirectPath = 'user/' . $page;
    }
}

// Redirigir a la nueva ruta MVC
header('Location: ' . URLROOT . '/' . $redirectPath);
exit;
