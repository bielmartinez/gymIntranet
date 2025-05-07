<?php 
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
          <form action="<?= URLROOT ?>/User/filterClasses" method="post" class="me-2" id="dateFilterForm">
            <div class="input-group">
              <input type="date" class="form-control form-control-sm" name="date" id="dateFilter" value="<?= $filterDate ?>">
              <button class="btn btn-sm btn-outline-primary" type="submit">Filtrar</button>
              <?php if($filterDate && $filterDate != date('Y-m-d')): ?>
              <button class="btn btn-sm btn-outline-secondary" type="button" id="resetDateFilter">
                <i class="fas fa-times"></i>
              </button>
              <?php endif; ?>
            </div>
          </form>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-filter"></i> Filtrar por tipo
              <span id="activeTypeFilter" class="badge bg-primary ms-1" style="display: none;"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
              <li><a class="dropdown-item filter-class" href="#" data-filter="all">Todas las clases</a></li>
              <?php foreach ($classTypes as $type): ?>
              <li><a class="dropdown-item filter-class" href="#" data-filter="<?= $type->tipus_classe_id ?>"><?= $type->nom ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>

      <!-- Mis Reservas -->
      <?php if (isset($_SESSION['user_id']) && !empty($userReservations)): ?>
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold">Mis Próximas Reservas</h6>
          <a href="<?= URLROOT ?>/User/myReservations" class="btn btn-sm btn-light">Ver todas</a>
        </div>
        <div class="card-body pb-0">
          <div class="row">
            <?php 
            // Mostrar solo las próximas 3 reservas
            $count = 0;
            $today = date('Y-m-d');
            
            foreach ($userReservations as $reservation): 
              if ($reservation->data < $today) continue; // Saltar reservas pasadas
              if ($count >= 3) break; // Mostrar máximo 3
              $count++;
            ?>
            <div class="col-md-4 mb-4">
              <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0"><?= $reservation->tipus_nom ?></h5>
                    <span class="badge bg-primary"><?= date('d/m/Y', strtotime($reservation->data)) ?></span>
                  </div>
                  <div class="mb-2">
                    <i class="far fa-clock text-muted me-2"></i>
                    <span><?= date('H:i', strtotime($reservation->hora)) ?></span>
                  </div>
                  <div class="mb-2">
                    <i class="fas fa-user text-muted me-2"></i>
                    <span><?= $reservation->monitor_nom ?></span>
                  </div>
                  <div class="d-grid mt-3">
                    <form action="<?= URLROOT ?>/User/cancelReservation" method="post">
                      <input type="hidden" name="reservation_id" value="<?= $reservation->reserva_id ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger w-100" 
                              onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">
                        <i class="fas fa-times me-1"></i> Cancelar
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            
            <?php if ($count === 0): ?>
            <div class="col-12">
              <div class="alert alert-light text-center">
                No tienes reservas próximas.
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Clases Disponibles -->
      <div class="mb-3">
        <h4>Clases disponibles para <?= date('d/m/Y', strtotime($filterDate)) ?></h4>
        <p class="text-muted">Reserva tu plaza en nuestras clases y empieza a disfrutar de nuestras actividades.</p>
      </div>

      <div class="row" id="classesContainer">
        <?php if (empty($availableClasses)): ?>
          <div class="col-12">
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              No hay clases disponibles para la fecha seleccionada.
              <a href="<?= URLROOT ?>/User/classes" class="btn btn-sm btn-primary ms-2">Ver todas las clases</a>
            </div>
          </div>
        <?php else: ?>
          <?php foreach ($availableClasses as $class): 
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
            
            // Calcular el porcentaje de ocupación para el estilo visual
            $ocupacionPorcentaje = ($class->capacitat_maxima > 0) ? 
                                  ($class->capacitat_actual / $class->capacitat_maxima) * 100 : 0;
            
            $cardStyle = "";
            $textClass = "";
            $buttonClass = "btn-primary";
            
            if ($ocupacionPorcentaje >= 80) {
              $cardStyle = "border-danger";
              $textClass = "text-danger";
              $buttonClass = "btn-danger";
            } elseif ($ocupacionPorcentaje >= 50) {
              $cardStyle = "border-warning";
              $textClass = "text-warning";
              $buttonClass = "btn-warning";
            } else {
              $cardStyle = "border-success";
              $textClass = "text-success";
              $buttonClass = "btn-success";
            }
          ?>
          <div class="col-md-4 mb-4 class-card" data-class-type="<?= $class->tipus_classe_id ?>">
            <div class="card shadow-sm h-100 <?= $cardStyle ?>">
              <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><?= $class->tipus_nom ?></h5>
                <span class="badge bg-primary">
                  <?= date('d/m/Y', strtotime($class->data)) ?>
                </span>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                  <div>
                    <i class="far fa-clock me-1"></i> 
                    <?= date('H:i', strtotime($class->hora)) ?> - <?= date('H:i', strtotime($class->hora) + $class->duracio * 60) ?>
                  </div>
                  <div class="<?= $textClass ?> fw-bold">
                    <i class="fas fa-users me-1"></i>
                    <?= $class->capacitat_actual ?>/<?= $class->capacitat_maxima ?>
                  </div>
                </div>
                
                <div class="progress mb-3" style="height: 10px;">
                  <div class="progress-bar bg-<?= $ocupacionPorcentaje >= 80 ? 'danger' : ($ocupacionPorcentaje >= 50 ? 'warning' : 'success') ?>" 
                       role="progressbar" 
                       style="width: <?= $ocupacionPorcentaje ?>%;" 
                       aria-valuenow="<?= $ocupacionPorcentaje ?>" 
                       aria-valuemin="0" 
                       aria-valuemax="100"></div>
                </div>
                
                <div class="mb-3">
                  <i class="fas fa-map-marker-alt me-2"></i> <?= $class->sala ?>
                </div>
                <div class="mb-3">
                  <i class="fas fa-user me-2"></i> <?= $class->monitor_nom ?>
                </div>
                
                <?php if (!empty($class->tipus_descripcio)): ?>
                <p class="small text-muted mb-3"><?= $class->tipus_descripcio ?></p>
                <?php endif; ?>
                
                <div class="d-grid mt-3">
                  <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?= URLROOT ?>/auth/login" class="btn btn-outline-secondary">
                      <i class="fas fa-sign-in-alt me-1"></i> Inicia sesión para reservar
                    </a>
                  <?php elseif ($userHasReservation): ?>
                    <button class="btn btn-success" disabled>
                      <i class="fas fa-check me-1"></i> Ya reservada
                    </button>
                  <?php elseif ($class->capacitat_actual >= $class->capacitat_maxima): ?>
                    <button class="btn btn-secondary" disabled>
                      <i class="fas fa-ban me-1"></i> Clase completa
                    </button>
                  <?php else: ?>
                    <form action="<?= URLROOT ?>/User/reserveClass" method="post">
                      <input type="hidden" name="class_id" value="<?= $class->classe_id ?>">
                      <button type="submit" class="btn <?= $buttonClass ?> w-100">
                        <i class="fas fa-bookmark me-1"></i> Reservar plaza
                      </button>
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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Establecer la fecha actual como valor predeterminado para el filtro si no hay filtro previo
    const dateInput = document.querySelector('input[name="date"]');
    if (dateInput && dateInput.value === "") {
      dateInput.valueAsDate = new Date();
    }
    
    // Variables para tracking de filtros activos
    let activeTypeFilter = 'all';
    const noResultsMessage = document.createElement('div');
    noResultsMessage.className = 'col-12';
    noResultsMessage.innerHTML = `
      <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        No hay clases disponibles con los filtros seleccionados.
        <button class="btn btn-sm btn-outline-primary ms-2" id="resetAllFilters">Restablecer filtros</button>
      </div>
    `;
    
    // Función para aplicar filtros
    function applyFilters() {
      let visibleCards = 0;
      
      document.querySelectorAll('.class-card').forEach(card => {
        // Verificar si cumple con el filtro de tipo
        const matchesTypeFilter = (activeTypeFilter === 'all' || card.dataset.classType === activeTypeFilter);
        
        // Mostrar u ocultar la tarjeta según los filtros
        if (matchesTypeFilter) {
          card.style.display = 'block';
          visibleCards++;
        } else {
          card.style.display = 'none';
        }
      });
      
      // Mostrar mensaje si no hay resultados
      const classesContainer = document.getElementById('classesContainer');
      if (visibleCards === 0 && !document.querySelector('.alert-info')) {
        classesContainer.appendChild(noResultsMessage);
      } else if (visibleCards > 0) {
        const existingMessage = classesContainer.querySelector('.alert-info');
        if (existingMessage && existingMessage.parentNode === classesContainer) {
          classesContainer.removeChild(existingMessage);
        }
      }
      
      // Actualizar indicador de filtro activo
      updateActiveFilterIndicator();
    }
    
    // Actualizar indicador visual de filtro activo
    function updateActiveFilterIndicator() {
      const activeFilterBadge = document.getElementById('activeTypeFilter');
      
      if (activeTypeFilter === 'all') {
        activeFilterBadge.style.display = 'none';
      } else {
        // Encontrar el nombre del tipo de clase seleccionado
        const selectedFilterElement = document.querySelector(`.filter-class[data-filter="${activeTypeFilter}"]`);
        if (selectedFilterElement) {
          activeFilterBadge.textContent = selectedFilterElement.textContent;
          activeFilterBadge.style.display = 'inline-block';
        }
      }
    }
    
    // Filtrar clases por tipo
    document.querySelectorAll('.filter-class').forEach(item => {
      item.addEventListener('click', event => {
        event.preventDefault();
        activeTypeFilter = event.target.dataset.filter;
        applyFilters();
      });
    });

    // Restablecer filtro de fecha
    const resetDateFilterButton = document.getElementById('resetDateFilter');
    if (resetDateFilterButton) {
      resetDateFilterButton.addEventListener('click', () => {
        const dateFilterForm = document.getElementById('dateFilterForm');
        const dateFilterInput = document.getElementById('dateFilter');
        dateFilterInput.valueAsDate = new Date();
        dateFilterForm.submit();
      });
    }
    
    // Restablecer todos los filtros (botón dinámico)
    document.addEventListener('click', function(event) {
      if (event.target && event.target.id === 'resetAllFilters') {
        activeTypeFilter = 'all';
        const dateFilterForm = document.getElementById('dateFilterForm');
        const dateFilterInput = document.getElementById('dateFilter');
        dateFilterInput.valueAsDate = new Date();
        dateFilterForm.submit();
      }
    });
    
    // Aplicar filtros iniciales
    applyFilters();
  });
</script>
