<?php
/**
 * Vista para que los usuarios vean sus notificaciones (versión simplificada)
 */
?>

<!-- Incluir estilos específicos para la página de notificaciones de usuarios -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/user/notifications.css">

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
      </div>      <?php 
      // Ya no filtramos las notificaciones leídas
      $filteredNotifications = $data['notifications'];
      
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
                    </h5>                    <div class="d-flex align-items-center">
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

<!-- Incluir script específico para notificaciones de usuario -->
<script src="<?php echo URLROOT; ?>/js/user/notifications.js"></script>
<script>
  // Variable global para el controlador
  const URLROOT = '<?php echo URLROOT; ?>';
</script>