<?php 
// El header y footer ahora son incluidos por direct-view.php
// No necesitamos incluirlos aquí

// Definir variables para evitar errores
$availableClasses = $data['available_classes'] ?? [];
$userReservations = $data['user_reservations'] ?? [];
$filterDate = $data['filter_date'] ?? date('Y-m-d');
$classTypes = $data['class_types'] ?? [];

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
        <h1 class="h2">Clases Disponibles</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <!-- Filtro por fecha -->
          <form action="<?= URLROOT ?>/user/filterClasses" method="post" class="me-2">
            <div class="input-group">
              <input type="date" class="form-control form-control-sm" name="date" value="<?= $filterDate ?>">
              <button class="btn btn-sm btn-outline-secondary" type="submit">Filtrar</button>
            </div>
          </form>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-filter"></i> Filtrar
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
              <li><a class="dropdown-item filter-class" href="#" data-filter="all">Todas las clases</a></li>
              <?php foreach ($classTypes as $type): ?>
              <li><a class="dropdown-item filter-class" href="#" data-filter="<?= $type->id ?>"><?= $type->nom ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>

      <!-- Mis Reservas -->
      <?php if (isset($_SESSION['user_id']) && !empty($userReservations)): ?>
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Mis Reservas</h6>
          <a href="<?= URLROOT ?>/user/myReservations" class="btn btn-sm btn-outline-primary">Ver todas</a>
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
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                // Mostrar solo las próximas 3 reservas
                $count = 0;
                foreach ($userReservations as $reservation): 
                  if ($count >= 3) break;
                  $count++;
                ?>
                <tr>
                  <td><?= $reservation->tipus_nom ?></td>
                  <td><?= date('d/m/Y', strtotime($reservation->data)) ?></td>
                  <td><?= date('H:i', strtotime($reservation->hora)) ?></td>
                  <td><?= $reservation->monitor_nom ?></td>
                  <td>
                    <form action="<?= URLROOT ?>/user/cancelReservation" method="post" style="display:inline;">
                      <input type="hidden" name="reservation_id" value="<?= $reservation->reserva_id ?>">
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">
                        <i class="fas fa-times"></i> Cancelar
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Classes Available -->
      <h2 class="mt-4">Clases Disponibles</h2>
      <div class="row">
        <?php if (empty($availableClasses)): ?>
          <div class="col-12">
            <div class="alert alert-info">
              No hay clases disponibles para la fecha seleccionada.
            </div>
          </div>
        <?php else: ?>
          <?php foreach ($availableClasses as $class): 
            // Determinar el color según el tipo de clase
            $cardColor = "primary"; // color por defecto
            
            // Buscar el tipo de clase para obtener el color personalizado si existe
            foreach ($classTypes as $type) {
              if ($type->id == $class->tipus_classe_id && !empty($type->color)) {
                $cardColor = $type->color;
                break;
              }
            }
            
            // Comprobar si el usuario ya tiene reserva para esta clase
            $userHasReservation = false;
            if (isset($_SESSION['user_id'])) {
              foreach ($userReservations as $reservation) {
                if ($reservation->classe_id == $class->classe_id) {
                  $userHasReservation = true;
                  break;
                }
              }
            }
          ?>
          <div class="col-md-4 mb-4 class-card" data-class-type="<?= $class->tipus_classe_id ?>">
            <div class="card shadow h-100">
              <div class="card-header bg-<?= $cardColor ?> text-white">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><?= $class->tipus_nom ?></h5>
                  <span class="badge bg-light text-dark">
                    <?= date('d/m/Y', strtotime($class->data)) ?>
                  </span>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                  <span><i class="far fa-clock me-2"></i> <?= date('H:i', strtotime($class->hora)) ?> - <?= date('H:i', strtotime($class->hora) + $class->duracio * 60) ?></span>
                  <span><i class="fas fa-user-friends me-2"></i> <?= $class->capacitat_actual ?>/<?= $class->capacitat_maxima ?></span>
                </div>
                <div class="mb-2">
                  <span><i class="fas fa-map-marker-alt me-2"></i> <?= $class->sala ?></span>
                </div>
                <div class="mb-3">
                  <span><i class="fas fa-user me-2"></i> <?= $class->monitor_nom ?></span>
                </div>
                <p class="small text-muted"><?= $class->tipus_descripcio ?></p>
                <div class="d-grid">
                  <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?= URLROOT ?>/auth/login" class="btn btn-secondary">Inicia sesión para reservar</a>
                  <?php elseif ($userHasReservation): ?>
                    <button class="btn btn-success" disabled>Ya reservada</button>
                  <?php elseif ($class->capacitat_actual >= $class->capacitat_maxima): ?>
                    <button class="btn btn-secondary" disabled>Clase completa</button>
                  <?php else: ?>
                    <form action="<?= URLROOT ?>/user/reserveClass" method="post">
                      <input type="hidden" name="class_id" value="<?= $class->classe_id ?>">
                      <button type="submit" class="btn btn-<?= $cardColor ?> w-100">Reservar</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>

<!-- Include FullCalendar library -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Filter classes
    document.querySelectorAll('.filter-class').forEach(item => {
      item.addEventListener('click', event => {
        event.preventDefault();
        const filter = event.target.dataset.filter;
        
        document.querySelectorAll('.class-card').forEach(card => {
          if (filter === 'all' || card.dataset.classType === filter) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  });
</script>
