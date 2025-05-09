<?php
/**
 * Vista para la gestión de notificaciones
 * Permite a los administradores crear y administrar notificaciones para los usuarios
 * Versión simplificada sin tipos ni destinatarios
 */
?>

<!-- Incluir estilos específicos para la página de notificaciones -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/notifications.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

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
                                    <td><?php echo $notification->id; ?></td>
                                    <td><?php echo $notification->title; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($notification->created_at)); ?></td>
                                    <td>                                        <div class="btn-group btn-group-sm action-buttons">
                                            <button type="button" class="btn btn-outline-danger delete-notification" 
                                                    data-id="<?php echo $notification->id; ?>"
                                                    data-title="<?php echo htmlspecialchars($notification->title); ?>"
                                                    onclick="console.log('Click en botón ID: <?php echo $notification->id; ?>')">
                                                <i class="fas fa-trash" title="Eliminar"></i> Eliminar
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
                    <!-- Campo oculto para el tipo de notificación -->
                    <input type="hidden" name="type" value="info">
                    <!-- Campo oculto para indicar que es una notificación global -->
                    <input type="hidden" name="is_global" value="on">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="newNotificationForm" class="btn btn-primary">Enviar Notificación</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- Definir URLROOT antes de cargar el script -->
<script>
    // Variable global para el controlador
    const URLROOT = '<?php echo URLROOT; ?>';
    
    // Función para depurar eventos de click
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('delete-notification') || 
                        (e.target.parentElement && e.target.parentElement.classList.contains('delete-notification')))) {
            console.log('Click en botón de eliminar detectado via event delegation');
            
            // Si el click fue en el ícono dentro del botón, usar el botón padre
            const button = e.target.classList.contains('delete-notification') ? e.target : e.target.parentElement;
            
            const notificationId = button.getAttribute('data-id');
            const notificationTitle = button.getAttribute('data-title');
            
            console.log('Datos del botón:', {id: notificationId, title: notificationTitle});
            
            if (confirm(`¿Está seguro de que desea eliminar la notificación "${notificationTitle}"? Esta acción no se puede deshacer.`)) {
                console.log('Confirmado, redirigiendo a:', URLROOT + '/admin/deleteNotification/' + notificationId);
                window.location.href = URLROOT + '/admin/deleteNotification/' + notificationId;
            }
        }
    });
</script>
<!-- Incluir script específico para la página de notificaciones -->
<script src="<?php echo URLROOT; ?>/js/admin/notifications-admin.js"></script>