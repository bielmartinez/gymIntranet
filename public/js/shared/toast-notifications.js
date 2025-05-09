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
            icon = '<i class="fas fa-times-circle me-2"></i>';
            break;
        case 'warning':
            bgColorClass = 'bg-warning text-dark';
            icon = '<i class="fas fa-exclamation-triangle me-2"></i>';
            break;
        case 'info':
            bgColorClass = 'bg-info text-white';
            icon = '<i class="fas fa-info-circle me-2"></i>';
            break;
    }
    
    // Crear el elemento toast
    const toast = document.createElement('div');
    toast.className = `toast ${bgColorClass} mb-2`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-bs-delay', duration);
    
    // Estructura interna del toast
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${icon} ${sanitizedMessage}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    // Añadir el toast al contenedor
    toastContainer.appendChild(toast);
    
    // Crear y mostrar el objeto toast de Bootstrap
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Eliminar el toast del DOM después de que se oculte
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}

/**
 * Mostrar mensaje de éxito
 * @param {string} message - El mensaje a mostrar
 * @param {number} duration - Duración en milisegundos
 */
function showSuccess(message, duration = 4000) {
    showToast(message, 'success', duration);
}

/**
 * Mostrar mensaje de error
 * @param {string} message - El mensaje a mostrar
 * @param {number} duration - Duración en milisegundos
 */
function showError(message, duration = 4000) {
    showToast(message, 'error', duration);
}

/**
 * Mostrar mensaje de advertencia
 * @param {string} message - El mensaje a mostrar
 * @param {number} duration - Duración en milisegundos
 */
function showWarning(message, duration = 4000) {
    showToast(message, 'warning', duration);
}

/**
 * Mostrar mensaje informativo
 * @param {string} message - El mensaje a mostrar
 * @param {number} duration - Duración en milisegundos
 */
function showInfo(message, duration = 4000) {
    showToast(message, 'info', duration);
}
