<?php
/**
 * Vista para que el administrador gestione las reservas de una clase específica
 */
?>

<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-12">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/classes">Clases</a></li>
          <li class="breadcrumb-item active">Reservas de Clase</li>
        </ol>
      </nav>
      
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">
            <i class="fas fa-calendar-check me-2"></i>
            Reservas para: <?php echo htmlspecialchars($data['class']->tipus_nom); ?>
          </h5>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-md-6">
              <h6 class="card-subtitle mb-2 text-muted">Detalles de la Clase</h6>
              <table class="table table-bordered">
                <tr>
                  <th>Fecha:</th>
                  <td><?php echo date('d/m/Y', strtotime($data['class']->data)); ?></td>
                </tr>
                <tr>
                  <th>Hora:</th>
                  <td><?php echo date('H:i', strtotime($data['class']->hora)); ?></td>
                </tr>
                <tr>
                  <th>Monitor:</th>
                  <td><?php echo htmlspecialchars($data['class']->monitor_nom); ?></td>
                </tr>
                <tr>
                  <th>Sala:</th>
                  <td><?php echo htmlspecialchars($data['class']->sala); ?></td>
                </tr>
                <tr>
                  <th>Capacidad:</th>
                  <td><?php echo $data['class']->capacitat_actual . '/' . $data['class']->capacitat_maxima; ?></td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Importante:</strong> Debe cancelar todas las reservas antes de poder eliminar esta clase.
              </div>
              
              <div class="d-grid gap-2">
                <?php if (!empty($data['reservations'])): ?>
                <button type="button" class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#cancelAllReservationsModal">
                  <i class="fas fa-trash-alt me-2"></i>Cancelar Todas las Reservas
                </button>
                <?php endif; ?>

                <a href="<?php echo URLROOT; ?>/admin/classes" class="btn btn-secondary">
                  <i class="fas fa-arrow-left me-2"></i>Volver a Clases
                </a>
              </div>
            </div>
          </div>
          
          <?php if (empty($data['reservations'])): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              No hay reservas para esta clase.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Usuario</th>
                    <th>Fecha de Reserva</th>
                    <th>Asistencia</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($data['reservations'] as $reservation): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($reservation->usuario_nombre); ?></td>
                      <td><?php echo date('d/m/Y H:i', strtotime($reservation->data_reserva)); ?></td>
                      <td>
                        <?php if ($reservation->assistencia): ?>
                          <span class="badge bg-success">Asistió</span>
                        <?php else: ?>
                          <span class="badge bg-warning">Pendiente</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <form action="<?php echo URLROOT; ?>/admin/cancelReservation" method="post" onsubmit="return confirm('¿Está seguro de cancelar esta reserva?');">
                          <input type="hidden" name="reserva_id" value="<?php echo $reservation->reserva_id; ?>">
                          <input type="hidden" name="classe_id" value="<?php echo $data['class']->classe_id; ?>">
                          <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-times me-1"></i>Cancelar Reserva
                          </button>
                        </form>
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
  </div>
</div>

<!-- Modal para confirmar cancelación de todas las reservas -->
<div class="modal fade" id="cancelAllReservationsModal" tabindex="-1" aria-labelledby="cancelAllReservationsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="cancelAllReservationsModalLabel">Confirmar Cancelación Masiva</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>¿Está seguro de que desea cancelar <strong>TODAS</strong> las reservas para esta clase?</p>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Esta acción <strong>no se puede deshacer</strong> y afectará a todos los usuarios que tienen reservas para esta clase.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form action="<?php echo URLROOT; ?>/admin/cancelAllReservations" method="post">
          <input type="hidden" name="classe_id" value="<?php echo $data['class']->classe_id; ?>">
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash-alt me-2"></i>Eliminar Todas las Reservas
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
