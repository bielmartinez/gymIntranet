<?php 
// El header y footer ahora lo incluye direct-view.php, no necesitamos incluirlo aquí
?>

<!-- Enlazar hoja de estilos específica para el dashboard -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/user/dashboard.css">

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <!-- Links rápidos movidos al principio -->
      <div class="col-12 mt-3 mb-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Accesos Rápidos</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo URLROOT; ?>/user/classes" class="text-decoration-none">
                  <div class="card bg-primary text-white shadow">
                    <div class="card-body text-center">
                      <i class="fas fa-dumbbell fa-3x mb-3"></i>
                      <h5>Clases</h5>
                      <div class="text-white-50">Reserva tus clases</div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo URLROOT; ?>/user/tracking" class="text-decoration-none">
                  <div class="card bg-info text-white shadow">
                    <div class="card-body text-center">
                      <i class="fas fa-heartbeat fa-3x mb-3"></i>
                      <h5>Seguimiento</h5>
                      <div class="text-white-50">Controla tu progreso</div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-lg-4 col-md-12 mb-4">
                <a href="<?php echo URLROOT; ?>/userRoutine" class="text-decoration-none">
                  <div class="card bg-success text-white shadow">
                    <div class="card-body text-center">
                      <i class="fas fa-list fa-3x mb-3"></i>
                      <h5>Rutinas</h5>
                      <div class="text-white-50">Ver tus rutinas</div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Bienvenido/a, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'; ?></h1>
      </div>

      <!-- Stats at a glance -->
      <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Clases Reservadas</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?php echo isset($data['user_reservations']) ? count($data['user_reservations']) : '0'; ?>
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-6 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Próxima Clase</div>
                  <?php if (isset($data['upcoming_classes']) && count($data['upcoming_classes']) > 0) : ?>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                      <?php 
                        $nextClass = $data['upcoming_classes'][0];
                        echo date('d/m/Y', strtotime($nextClass->data)) . ', ' . date('H:i', strtotime($nextClass->hora));
                      ?>
                    </div>
                    <small class="text-muted"><?php echo $nextClass->tipus_nom; ?> - Sala <?php echo $nextClass->sala; ?></small>
                  <?php else : ?>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">No hay clases próximas</div>
                    <small class="text-muted">Reserva una clase ahora</small>
                  <?php endif; ?>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clock fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Próximas actividades -->
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Próximas Actividades (próximos 3 días)</h6>
              <a href="<?php echo URLROOT; ?>/user/classes" class="btn btn-sm btn-primary">Ver Todas</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Actividad</th>
                      <th>Día</th>
                      <th>Hora</th>
                      <th>Monitor</th>
                      <th>Sala</th>
                      <th>Plazas disponibles</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (isset($data['upcoming_classes']) && count($data['upcoming_classes']) > 0) : ?>
                      <?php foreach ($data['upcoming_classes'] as $class) : ?>
                        <tr>
                          <td><span class="font-weight-bold"><?php echo $class->tipus_nom; ?></span></td>
                          <td><?php echo date('d/m/Y', strtotime($class->data)); ?></td>
                          <td><?php echo date('H:i', strtotime($class->hora)) . ' - ' . date('H:i', strtotime($class->hora) + ($class->duracio * 60)); ?></td>
                          <td><?php echo $class->monitor_nom; ?></td>
                          <td><?php echo $class->sala; ?></td>
                          <td><?php echo ($class->capacitat_maxima - $class->capacitat_actual) . '/' . $class->capacitat_maxima; ?></td>
                          <td>
                            <?php $isReserved = false; ?>
                            <?php if (isset($data['user_reservations'])) : ?>
                              <?php foreach ($data['user_reservations'] as $reservation) : ?>
                                <?php if ($reservation->classe_id == $class->classe_id) : ?>
                                  <?php $isReserved = true; ?>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <?php if ($isReserved) : ?>
                              <span class="badge bg-success">Reservada</span>
                            <?php else : ?>
                              <?php if ($class->capacitat_actual < $class->capacitat_maxima) : ?>
                                <form action="<?php echo URLROOT; ?>/user/reserveClass" method="post" class="d-inline">
                                  <input type="hidden" name="class_id" value="<?php echo $class->classe_id; ?>">
                                  <button type="submit" class="btn btn-sm btn-outline-primary">Reservar</button>
                                </form>
                              <?php else : ?>
                                <span class="badge bg-danger">Completa</span>
                              <?php endif; ?>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <tr>
                        <td colspan="7" class="text-center">No hay clases programadas para los próximos 3 días</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>    </main>
  </div>
</div>

<!-- Enlazar archivo JavaScript específico para el dashboard -->
<script src="<?= URLROOT ?>/public/js/user/dashboard.js"></script>
