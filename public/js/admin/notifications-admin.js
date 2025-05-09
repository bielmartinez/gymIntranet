/**
 * Funcionalidades específicas para la administración de notificaciones
 * Este archivo maneja las interacciones en la página de gestión de notificaciones
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables para la tabla de notificaciones
    if (document.getElementById('notificationsTable')) {
        $('#notificationsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[0, 'desc']], // Ordenar por ID (columna 0) descendente por defecto
            responsive: true
        });
    }
    
    console.log('Script de notificaciones cargado correctamente');
    
    // Esperar un breve momento para asegurarse de que DataTables haya terminado de procesar la tabla
    setTimeout(function() {
        // Confirmar eliminación de notificación
        const deleteButtons = document.querySelectorAll('.delete-notification');
        console.log('Botones de eliminación encontrados:', deleteButtons.length);
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');
                const notificationTitle = this.getAttribute('data-title');
                
                console.log('Botón de eliminar pulsado para:', notificationTitle, 'ID:', notificationId);
                
                if (confirm(`¿Está seguro de que desea eliminar la notificación "${notificationTitle}"? Esta acción no se puede deshacer.`)) {
                    // La variable URLROOT se definirá en cada página que incluya este script
                    const urlRoot = typeof URLROOT !== 'undefined' ? URLROOT : '';
                    console.log('Redirigiendo a:', `${urlRoot}/admin/deleteNotification/${notificationId}`);
                    window.location.href = `${urlRoot}/admin/deleteNotification/${notificationId}`;
                }
            });
        });
    }, 100); // Pequeño retraso para asegurar que todo está cargado
});

// Log para verificar que el script se cargó completamente
console.log('Script notifications-admin.js cargado completamente');