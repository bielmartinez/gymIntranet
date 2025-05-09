// Script para el dashboard de usuario
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips si se están usando
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Se pueden agregar más funcionalidades JavaScript específicas para el dashboard aquí
});
