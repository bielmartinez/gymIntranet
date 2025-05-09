// Inicializar DataTable para la tabla de rutinas si existe
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('routinesTable');
    if (table && typeof $.fn.DataTable !== 'undefined') {
        $(table).DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[0, 'desc']] // Ordenar por ID de forma descendente (las más recientes primero)
        });
    }
    
    // Solución optimizada para el problema de modales lentos o que se abren dos veces
    // Cerrar cualquier modal que pudiera estar abierto
    const closeAllModals = function() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        });
    };
    
    // Agregar event listeners a los botones de eliminar
    document.querySelectorAll('.open-delete-modal').forEach(button => {
        button.addEventListener('click', function() {
            closeAllModals(); // Cerrar cualquier modal abierto
            
            const modalId = this.getAttribute('data-modal-id');
            const modalElement = document.getElementById(modalId);
            
            if (modalElement && typeof bootstrap !== 'undefined') {
                // Pequeño retraso para asegurar que cualquier modal previo se ha cerrado
                setTimeout(() => {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }, 50);
            }
        });
    });
});
