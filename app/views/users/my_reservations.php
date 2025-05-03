<?php 
// Mostrar mensaje si existe
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
            ' . $_SESSION['message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Mis Reservas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <a href="<?= URLROOT ?>/user/classes" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-plus-circle me-1"></i> Nueva Reserva
          </a>
        </div>
      </div>

      <?php if (empty($data['reservations'])): ?>
        <div class="alert alert-info">
          <p>No tienes reservas activas.</p>
          <a href="<?= URLROOT ?>/user/classes" class="btn btn-primary">Reservar Clases</a>
        </div>
      <?php else: ?>
        <!-- Próximas Reservas -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Próximas Reservas</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Clase</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Instructor</th>
                    <th>Sala</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $today = date('Y-m-d');
                  $futureReservations = false;
                  
                  foreach ($data['reservations'] as $reservation): 
                    if ($reservation->data >= $today):
                      $futureReservations = true;
                  ?>
                  <tr>
                    <td><?= $reservation->tipus_nom ?></td>
                    <td><?= date('d/m/Y', strtotime($reservation->data)) ?></td>
                    <td><?= date('H:i', strtotime($reservation->hora)) ?></td>
                    <td><?= $reservation->monitor_nom ?></td>
                    <td><?= $reservation->sala ?></td>
                    <td>
                      <form action="<?= URLROOT ?>/user/cancelReservation" method="post" style="display:inline;">
                        <input type="hidden" name="reservation_id" value="<?= $reservation->reserva_id ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">
                          <i class="fas fa-times"></i> Cancelar
                        </button>
                      </form>
                    </td>
                  </tr>
                  <?php 
                    endif;
                  endforeach; 
                  
                  if (!$futureReservations):
                  ?>
                  <tr>
                    <td colspan="6" class="text-center">No tienes próximas reservas.</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <!-- Historial de Reservas -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-secondary">Historial de Reservas</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Clase</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Instructor</th>
                    <th>Sala</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $pastReservations = false;
                  
                  foreach ($data['reservations'] as $reservation): 
                    if ($reservation->data < $today):
                      $pastReservations = true;
                  ?>
                  <tr>
                    <td><?= $reservation->tipus_nom ?></td>
                    <td><?= date('d/m/Y', strtotime($reservation->data)) ?></td>
                    <td><?= date('H:i', strtotime($reservation->hora)) ?></td>
                    <td><?= $reservation->monitor_nom ?></td>
                    <td><?= $reservation->sala ?></td>
                    <td>
                      <?php if ($reservation->assistencia == 1): ?>
                        <span class="badge bg-success">Asistió</span>
                      <?php else: ?>
                        <span class="badge bg-danger">No asistió</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php 
                    endif;
                  endforeach; 
                  
                  if (!$pastReservations):
                  ?>
                  <tr>
                    <td colspan="6" class="text-center">No tienes historial de reservas.</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Código JavaScript si es necesario
  });
</script>