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

// Definir la fecha actual para filtrado
$today = date('Y-m-d');

// Separar reservas en futuras y pasadas
$futureReservations = [];
$pastReservations = [];

foreach ($data['reservations'] as $reservation) {
    if ($reservation->data >= $today) {
        $futureReservations[] = $reservation;
    } else {
        $pastReservations[] = $reservation;
    }
}
?>

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Mis Reservas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <a href="<?= URLROOT ?>/user/classes" class="btn btn-sm btn-primary">
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
          <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Próximas Reservas</h6>
            <span class="badge bg-light text-dark"><?= count($futureReservations) ?> reservas</span>
          </div>
          <div class="card-body">
            <?php if (empty($futureReservations)): ?>
              <div class="alert alert-light text-center">
                <p>No tienes próximas reservas.</p>
                <a href="<?= URLROOT ?>/user/classes" class="btn btn-primary btn-sm">Reservar ahora</a>
              </div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($futureReservations as $reservation): ?>
                  <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 border-left-primary shadow-sm">
                      <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold text-primary"><?= $reservation->tipus_nom ?></h6>
                        <span class="badge bg-primary"><?= date('d/m/Y', strtotime($reservation->data)) ?></span>
                      </div>
                      <div class="card-body">
                        <div class="mb-2">
                          <i class="far fa-clock text-muted me-2"></i>
                          <span><?= date('H:i', strtotime($reservation->hora)) ?> - <?= date('H:i', strtotime($reservation->hora) + $reservation->duracio * 60) ?></span>
                        </div>
                        <div class="mb-2">
                          <i class="fas fa-map-marker-alt text-muted me-2"></i>
                          <span><?= $reservation->sala ?></span>
                        </div>
                        <div class="mb-3">
                          <i class="fas fa-user text-muted me-2"></i>
                          <span><?= $reservation->monitor_nom ?></span>
                        </div>
                        <p class="small text-muted mb-3">
                          <?= mb_substr($reservation->tipus_descripcio, 0, 100) . (strlen($reservation->tipus_descripcio) > 100 ? '...' : '') ?>
                        </p>
                        <div class="d-grid">
                          <button class="btn btn-outline-danger btn-sm cancel-reservation" 
                                  data-reservation-id="<?= $reservation->reserva_id ?>"
                                  data-bs-toggle="modal" 
                                  data-bs-target="#cancelModal">
                            <i class="fas fa-times me-1"></i> Cancelar reserva
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
        
        <!-- Historial de Reservas -->
        <div class="card shadow mb-4">
          <div class="card-header bg-secondary text-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Historial de Reservas</h6>
            <span class="badge bg-light text-dark"><?= count($pastReservations) ?> reservas</span>
          </div>
          <div class="card-body">
            <?php if (empty($pastReservations)): ?>
              <div class="alert alert-light text-center">
                <p>No tienes historial de reservas.</p>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover" id="historyTable">
                  <thead class="table-light">
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
                    <?php foreach ($pastReservations as $reservation): ?>
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
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<!-- Modal de confirmación para cancelar reserva -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="cancelModalLabel">Cancelar Reserva</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de que deseas cancelar esta reserva?</p>
        <p class="text-danger">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="cancelReservationForm" action="<?= URLROOT ?>/user/cancelReservation" method="post">
          <input type="hidden" id="reservation_id_to_cancel" name="reservation_id" value="">
          <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Añadir referencia a jQuery y DataTables solo si hay historial de reservas -->
<?php if (!empty($pastReservations)): ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?php endif; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable para el historial si existe
    <?php if (!empty($pastReservations)): ?>
    setTimeout(function() {
      if (typeof $ !== 'undefined') {
        try {
          $('#historyTable').DataTable({
            language: {
              url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            order: [[1, 'desc'], [2, 'desc']], // Ordenar por fecha (desc) y hora (desc)
            responsive: true
          });
        } catch (error) {
          console.error('Error al inicializar DataTables:', error);
        }
      }
    }, 500);
    <?php endif; ?>
    
    // Manejar el botón de cancelar reserva
    document.querySelectorAll('.cancel-reservation').forEach(button => {
      button.addEventListener('click', function() {
        const reservationId = this.getAttribute('data-reservation-id');
        document.getElementById('reservation_id_to_cancel').value = reservationId;
      });
    });
  });
</script>