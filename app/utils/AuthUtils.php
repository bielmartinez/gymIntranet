<?php
/**
 * Utilidades de autenticación y autorización
 * Funciones helper para verificar roles de usuario
 */

/**
 * Verifica si el usuario actual tiene rol de staff (monitor)
 * 
 * @return bool True si el usuario es staff, false en caso contrario
 */
function isStaff() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff';
}

/**
 * Verifica si el usuario actual tiene rol de administrador
 * 
 * @return bool True si el usuario es administrador, false en caso contrario
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Verifica si el usuario actual tiene rol de usuario normal
 * 
 * @return bool True si el usuario es usuario normal, false en caso contrario
 */
function isUser() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user';
}

/**
 * Verifica si el usuario está autenticado (tiene una sesión activa)
 * 
 * @return bool True si el usuario está autenticado, false en caso contrario
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}