// Script para la página de vista de rutina
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips si se están usando
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Hacer que las tarjetas de ejercicio abran su modal al hacer clic
    document.querySelectorAll('.exercise-card').forEach(card => {
        card.addEventListener('click', function() {
            // Obtener el ID del ejercicio desde el elemento de datos
            const exerciseId = this.getAttribute('data-exercise-id');
            if (exerciseId) {
                // Abrir el modal correspondiente
                const modal = new bootstrap.Modal(document.getElementById(`exerciseDetailModal${exerciseId}`));
                modal.show();
            }
        });
    });
});
