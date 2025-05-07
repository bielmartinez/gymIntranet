/**
 * Sistema de notificaciones Toast para GymIntranet
 * Este archivo proporciona funcionalidades para mostrar mensajes de notificación tipo toast
 * que aparecen temporalmente en la pantalla para proporcionar feedback al usuario.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Comprobar si hay un mensaje toast en la sesión
    const toastMessage = document.querySelector('meta[name="toast_message"]');
    const toastType = document.querySelector('meta[name="toast_type"]');
    
    if (toastMessage && toastMessage.content && toastType && toastType.content) {
        showToast(toastMessage.content, toastType.content);
    }
    
    // Configurar para mostrar toasts cuando se envíen eventos personalizados
    document.addEventListener('showToast', function(e) {
        showToast(e.detail.message, e.detail.type);
    });
});

/**
 * Muestra una notificación toast personalizada
 * @param {string} message - El mensaje a mostrar
 * @param {string} type - El tipo de notificación ('success', 'error', 'warning', 'info')
 * @param {number} duration - Duración en milisegundos que se mostrará la notificación (opcional)
 */
function showToast(message, type = 'success', duration = 4000) {
    // Prevenir XSS sanitizando el mensaje
    const sanitizedMessage = message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
    
    // Crear el contenedor principal del toast si no existe
    let toastContainer = document.getElementById('toast-container');
    
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Generar un ID único para este toast
    const toastId = 'toast-' + Date.now();
    
    // Determinar la clase de color según el tipo
    let bgColorClass = 'bg-success text-white';
    let icon = '<i class="bi bi-check-circle-fill me-2"></i>';
    
    switch (type.toLowerCase()) {
        case 'error':
            bgColorClass = 'bg-danger text-white';
            icon = '<i class="bi bi-x-circle-fill me-2"></i>';
            break;
        case 'warning':
            bgColorClass = 'bg-warning text-dark';
            icon = '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
            break;
        case 'info':
            bgColorClass = 'bg-info text-dark';
            icon = '<i class="bi bi-info-circle-fill me-2"></i>';
            break;
    }
    
    // Crear el elemento toast
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast ${bgColorClass} mb-2`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Crear el contenido del toast
    toast.innerHTML = `
        <div class="toast-header ${bgColorClass}">
            ${icon}
            <strong class="me-auto">GymIntranet</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${sanitizedMessage}
        </div>
    `;
    
    // Añadir el toast al contenedor
    toastContainer.appendChild(toast);
    
    // Inicializar el toast usando Bootstrap
    const toastInstance = new bootstrap.Toast(toast, {
        autohide: true,
        delay: duration
    });
    
    // Mostrar el toast
    toastInstance.show();
    
    // Eliminar el toast del DOM cuando se oculte
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

/**
 * Muestra una notificación de éxito
 * @param {string} message - El mensaje a mostrar
 * @param {number} duration - Duración en milisegundos (opcional)
 */
function showSuccessToast(message, duration = 4000) {
    showToast(message, 'success', duration);
}

/**
 * Muestra una notificación de error
 * @param {string} message - El mensaje a mostrar
 * @param {number} duration - Duración en milisegundos (opcional)
 */
function showErrorToast(message, duration = 4000) {
    showToast(message, 'error', duration);
}

/**
 * Muestra una notificación de advertencia
 * @param {string} message - El mensaje a mostrar
 * @param {number} duration - Duración en milisegundos (opcional)
 */
function showWarningToast(message, duration = 4000) {
    showToast(message, 'warning', duration);
}

/**
 * Muestra una notificación informativa
 * @param {string} message - El mensaje a mostrar
 * @param {number} duration - Duración en milisegundos (opcional)
 */
function showInfoToast(message, duration = 4000) {
    showToast(message, 'info', duration);
}