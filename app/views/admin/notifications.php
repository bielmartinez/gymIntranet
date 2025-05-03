<?php
/**
 * Vista para la gestión de notificaciones
 * Permite a los administradores crear y administrar notificaciones para los usuarios
 * Versión simplificada sin tipos ni destinatarios
 */
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><?php echo $data['title']; ?></h1>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#newNotificationModal">
                <i class="fas fa-plus-circle me-2"></i>Nueva notificación
            </button>
        </div>
        <div class="card-body">
            <?php if(isset($_SESSION['admin_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['admin_message_type']; ?> alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['admin_message']; 
                        unset($_SESSION['admin_message']);
                        unset($_SESSION['admin_message_type']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if(empty($data['notifications'])): ?>
                <div class="alert alert-info">
                    No hay notificaciones creadas aún.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="notificationsTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['notifications'] as $notification): ?>
                                <tr>
                                    <td><?php echo $notification['id']; ?></td>
                                    <td><?php echo $notification['title']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary view-notification" 
                                                    data-id="<?php echo $notification['id']; ?>"
                                                    data-bs-toggle="modal" data-bs-target="#viewNotificationModal">
                                                <i class="fas fa-eye" title="Ver detalles"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger delete-notification" 
                                                    data-id="<?php echo $notification['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($notification['title']); ?>">
                                                <i class="fas fa-trash" title="Eliminar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para crear nueva notificación -->
<div class="modal fade" id="newNotificationModal" tabindex="-1" aria-labelledby="newNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newNotificationModalLabel">Nueva Notificación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newNotificationForm" action="<?php echo URLROOT; ?>/admin/createNotification" method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Mensaje</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="newNotificationForm" class="btn btn-primary">Enviar Notificación</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de notificación -->
<div class="modal fade" id="viewNotificationModal" tabindex="-1" aria-labelledby="viewNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewNotificationModalLabel">Detalles de la Notificación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h4 id="view-notification-title" class="mb-2"></h4>
                    <div class="d-flex justify-content-end mb-3">
                        <span class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i> <span id="view-notification-date"></span>
                        </span>
                    </div>
                    <div class="alert alert-info" id="view-notification-message"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
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
        
        // Cargar datos de la notificación al abrir el modal de detalles
        document.querySelectorAll('.view-notification').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');
                
                // Hacer una petición AJAX para obtener los detalles de la notificación
                fetch(`<?php echo URLROOT; ?>/admin/getNotificationDetails/${notificationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar la información en el modal
                            document.getElementById('view-notification-title').textContent = data.notification.title;
                            document.getElementById('view-notification-date').textContent = new Date(data.notification.created_at).toLocaleString();
                            
                            // Actualizar el contenido del mensaje
                            const messageElement = document.getElementById('view-notification-message');
                            messageElement.textContent = data.notification.message;
                        } else {
                            console.error('Error al cargar los detalles de la notificación:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la petición AJAX:', error);
                    });
            });
        });
        
        // Confirmar eliminación de notificación
        document.querySelectorAll('.delete-notification').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');
                const notificationTitle = this.getAttribute('data-title');
                
                if (confirm(`¿Está seguro de que desea eliminar la notificación "${notificationTitle}"? Esta acción no se puede deshacer.`)) {
                    window.location.href = `<?php echo URLROOT; ?>/admin/deleteNotification/${notificationId}`;
                }
            });
        });
    });
</script>