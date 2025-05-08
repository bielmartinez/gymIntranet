<?php
/**
 * Vista para que los usuarios vean sus notificaciones (versión simplificada)
 */

// Obtener las notificaciones leídas de la cookie si existe
$readNotifications = [];
if (isset($_SESSION['user_id'])) {
  $userId = $_SESSION['user_id'];
  $cookieName = "read_notifications_{$userId}";
  if (isset($_COOKIE[$cookieName])) {
    $readNotifications = json_decode($_COOKIE[$cookieName], true) ?: [];
  }
}
?>

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Notificaciones</h1>
        <div id="notification-actions" class="btn-toolbar mb-2 mb-md-0">
          <button type="button" id="refresh-notifications" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-sync me-1"></i> Actualizar
          </button>
        </div>
      </div>

      <div id="notification-status" style="display: none;" class="alert alert-success">
        Operación completada con éxito.
      </div>

      <?php 
      // Filtrar notificaciones leídas
      $filteredNotifications = [];
      foreach($data['notifications'] as $notification) {
        $notifId = is_object($notification) ? $notification->id : $notification['id'];
        if (!in_array($notifId, $readNotifications)) {
          $filteredNotifications[] = $notification;
        }
      }
      
      if(empty($filteredNotifications)): 
      ?>
        <div class="alert alert-info">
          <i class="fas fa-bell-slash me-2"></i>No hay notificaciones disponibles
        </div>
      <?php else: ?>
        <div class="row">
          <div class="col-12">
            <div class="list-group notification-list mb-4">
              <?php foreach($filteredNotifications as $notification): 
                // Verificar si es un objeto o un array
                $id = is_object($notification) ? $notification->id : $notification['id'];
                $title = is_object($notification) ? $notification->title : $notification['title'];
                $message = is_object($notification) ? $notification->message : $notification['message'];
                $created_at = is_object($notification) ? $notification->created_at : $notification['created_at'];
              ?>
                <div class="list-group-item list-group-item-action notification-item" id="notification-<?php echo $id; ?>">
                  <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                    <h5 class="mb-1">
                      <i class="fas fa-info-circle text-info me-2"></i>
                      <?php echo htmlspecialchars($title); ?>
                    </h5>
                    <div class="d-flex align-items-center">
                      <small class="text-muted me-3">
                        <?php 
                          $date = new DateTime($created_at);
                          $now = new DateTime();
                          $interval = $date->diff($now);
                          
                          if ($interval->days == 0) {
                            if ($interval->h == 0) {
                              if ($interval->i == 0) {
                                echo "Ahora mismo";
                              } else {
                                echo "Hace " . $interval->i . " minutos";
                              }
                            } else {
                              echo "Hace " . $interval->h . " horas";
                            }
                          } else if ($interval->days == 1) {
                            echo "Ayer";
                          } else {
                            echo date('d/m/Y', strtotime($created_at));
                          }
                        ?>
                      </small>
                      <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary mark-read" data-id="<?php echo $id; ?>" title="Marcar como leída">
                          <i class="fas fa-check"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <p class="mb-1"><?php echo htmlspecialchars($message); ?></p>
                </div>
              <?php endforeach; ?>
            </div>
            
            <?php if($data['totalPages'] > 1): ?>
              <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo $data['currentPage'] <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo URLROOT; ?>/user/notifications/<?php echo $data['currentPage']-1; ?>" aria-label="Previous">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  </li>
                  
                  <?php for($i = 1; $i <= $data['totalPages']; $i++): ?>
                    <li class="page-item <?php echo $data['currentPage'] == $i ? 'active' : ''; ?>">
                      <a class="page-link" href="<?php echo URLROOT; ?>/user/notifications/<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                  <?php endfor; ?>
                  
                  <li class="page-item <?php echo $data['currentPage'] >= $data['totalPages'] ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo URLROOT; ?>/user/notifications/<?php echo $data['currentPage']+1; ?>" aria-label="Next">
                      <span aria-hidden="true">&raquo;</span>
                    </a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<style>
  .notification-item {
    transition: all 0.3s ease;
  }
  
  .notification-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
  }
  
  .notification-item.fade-out {
    opacity: 0;
    height: 0;
    padding: 0;
    margin: 0;
    overflow: hidden;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Manejar clic en botón "Marcar como leído"
  document.querySelectorAll('.mark-read').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const notificationId = this.getAttribute('data-id');
      markAsRead(notificationId);
    });
  });
  
  // Manejar clic en "Actualizar"
  document.getElementById('refresh-notifications').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.reload();
  });
  
  // Función para marcar como leído
  function markAsRead(notificationId, animate = true) {
    fetch(`<?php echo URLROOT; ?>/user/markNotificationAsRead/${notificationId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        if (animate) {
          // Animar la desaparición de la notificación
          const notification = document.getElementById(`notification-${notificationId}`);
          if (notification) {
            notification.classList.add('fade-out');
            setTimeout(() => {
              notification.remove();
              checkEmptyNotifications();
            }, 300);
          }
        }
      } else {
        console.error('Error al marcar como leída:', data.message);
        showStatusMessage('Error al marcar la notificación como leída', 'danger');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showStatusMessage('Error de conexión', 'danger');
    });
  }
  
  // Verificar si no hay más notificaciones después de eliminar
  function checkEmptyNotifications() {
    const remainingNotifications = document.querySelectorAll('.notification-item:not(.fade-out)');
    if (remainingNotifications.length === 0) {
      const notificationList = document.querySelector('.notification-list');
      if (notificationList) {
        notificationList.innerHTML = `
          <div class="alert alert-info">
            <i class="fas fa-bell-slash me-2"></i>No hay notificaciones disponibles
          </div>
        `;
      }
    }
  }
  
  // Mostrar mensaje de estado
  function showStatusMessage(message, type = 'success') {
    const statusDiv = document.getElementById('notification-status');
    statusDiv.className = `alert alert-${type}`;
    statusDiv.textContent = message;
    statusDiv.style.display = 'block';
    
    // Ocultar después de 3 segundos
    setTimeout(() => {
      statusDiv.style.display = 'none';
    }, 3000);
  }
});
</script>