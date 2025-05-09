/**
 * Funcionalidades específicas para la página de notificaciones de usuario
 */

document.addEventListener('DOMContentLoaded', function() {
    // Referencia a elementos del DOM
    const refreshBtn = document.getElementById('refresh-notifications');
    const statusAlert = document.getElementById('notification-status');
    
    // Evento para refrescar las notificaciones
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            // Mostrar animación de carga
            refreshBtn.innerHTML = '<i class="fas fa-sync-alt fa-spin me-1"></i> Actualizando...';
            refreshBtn.disabled = true;
            
            // Recargar la página después de un pequeño retraso para mostrar la animación
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    }
    
    // Gestión de borrar notificaciones
    document.querySelectorAll('.delete-notification-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationId = this.getAttribute('data-id');
            const listItem = document.getElementById(`notification-${notificationId}`);
            
            if (confirm('¿Está seguro de que desea eliminar esta notificación?')) {
                // Llamar al endpoint para eliminar
                fetch(`${URLROOT}/user/deleteNotification/${notificationId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Animar la eliminación
                        listItem.style.height = listItem.offsetHeight + 'px';
                        setTimeout(() => {
                            listItem.style.height = '0';
                            listItem.style.opacity = '0';
                            listItem.style.marginBottom = '0';
                            listItem.style.padding = '0';
                            listItem.style.overflow = 'hidden';
                            
                            setTimeout(() => {
                                listItem.remove();
                                
                                // Si no hay más notificaciones, mostrar mensaje
                                const remainingItems = document.querySelectorAll('.notification-list .list-group-item');
                                if (remainingItems.length === 0) {
                                    const noNotificationsAlert = document.createElement('div');
                                    noNotificationsAlert.className = 'alert alert-info';
                                    noNotificationsAlert.innerHTML = '<i class="fas fa-bell-slash me-2"></i>No hay notificaciones disponibles';
                                    document.querySelector('.notification-list').replaceWith(noNotificationsAlert);
                                }
                            }, 300);
                        }, 10);
                        
                        // Mostrar mensaje de éxito
                        showSuccess('Notificación eliminada correctamente');
                    } else {
                        showError('Error al eliminar la notificación');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error al procesar la solicitud');
                });
            }
        });    });
});
