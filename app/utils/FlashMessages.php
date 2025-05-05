<?php
/**
 * Funciones de utilidad para manejo de mensajes flash
 * Los mensajes flash se muestran una sola vez y luego se eliminan de la sesión
 */

/**
 * Establece un mensaje flash en la sesión
 * 
 * @param string $name Nombre/Identificador del mensaje
 * @param string $message Contenido del mensaje
 * @param string $class Clase CSS opcional para estilo (ej: alert alert-success)
 * @return void
 */
function flash($name = '', $message = '', $class = 'alert alert-success') {
    // Si solo se proporciona el nombre y existe en la sesión, mostrar y eliminar
    if (!empty($name) && empty($message) && isset($_SESSION['flash_messages'][$name])) {
        $flash_message = $_SESSION['flash_messages'][$name];
        
        // Eliminar después de mostrar
        unset($_SESSION['flash_messages'][$name]);
        
        return $flash_message;
    } 
    // Si se proporcionan nombre y mensaje, guardar en sesión
    elseif (!empty($name) && !empty($message)) {
        // Guardar en la sesión
        $_SESSION['flash_messages'][$name] = [
            'message' => $message,
            'class' => $class
        ];
    }
    
    return null;
}

/**
 * Comprueba si existe un mensaje flash
 * 
 * @param string $name Nombre del mensaje
 * @return boolean
 */
function hasFlash($name) {
    return !empty($_SESSION['flash_messages'][$name]);
}

/**
 * Muestra un mensaje flash con HTML
 * 
 * @param string $name Nombre del mensaje
 * @return void
 */
function displayFlash($name) {
    $flash = flash($name);
    if ($flash) {
        echo '<div class="' . $flash['class'] . '">' . $flash['message'] . '</div>';
    }
}