<?php 
// El header y footer ahora lo incluye direct-view.php, no necesitamos incluirlo aquí
?>

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Bienvenido/a, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'; ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <a href="<?php echo URLROOT; ?>/user/dashboard" class="btn btn-sm btn-outline-secondary">Mis Rutinas</a>
            <a href="<?php echo URLROOT; ?>/user/classes" class="btn btn-sm btn-outline-primary">Reservar Clase</a>
          </div>
        </div>
      </div>

      <!-- Stats at a glance -->
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Clases Reservadas</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pistas Reservadas</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-volleyball-ball fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Progreso Mensual</div>
                  <div class="row no-gutters align-items-center">
                    <div class="col-auto">
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">75%</div>
                    </div>
                    <div class="col">
                      <div class="progress progress-sm mr-2">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Próxima Clase</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">Hoy, 18:30</div>
                  <small class="text-muted">Spinning - Sala 3</small>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clock fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Próximas actividades y Links rápidos -->
      <div class="row">
        <!-- Próximas actividades -->
        <div class="col-lg-6 mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Próximas Actividades</h6>
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
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="font-weight-bold">Spinning</span></td>
                      <td>Hoy</td>
                      <td>18:30 - 19:30</td>
                      <td><span class="badge bg-success">Confirmada</span></td>
                    </tr>
                    <tr>
                      <td><span class="font-weight-bold">Pádel</span></td>
                      <td>Mañana</td>
                      <td>10:00 - 11:00</td>
                      <td><span class="badge bg-success">Confirmada</span></td>
                    </tr>
                    <tr>
                      <td><span class="font-weight-bold">Yoga</span></td>
                      <td>26/04/2025</td>
                      <td>17:00 - 18:00</td>
                      <td><span class="badge bg-success">Confirmada</span></td>
                    </tr>
                    <tr>
                      <td><span class="font-weight-bold">Tenis</span></td>
                      <td>28/04/2025</td>
                      <td>19:00 - 20:30</td>
                      <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Progreso de actividad física -->
        <div class="col-lg-6 mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Progreso de Actividad Física</h6>
              <a href="<?php echo URLROOT; ?>/user/tracking" class="btn btn-sm btn-primary">Detalles</a>
            </div>
            <div class="card-body">
              <h4 class="small font-weight-bold">Minutos de ejercicio semanal <span class="float-end">80%</span></h4>
              <div class="progress mb-4">
                <div class="progress-bar bg-success" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <h4 class="small font-weight-bold">Objetivos de entrenamiento <span class="float-end">60%</span></h4>
              <div class="progress mb-4">
                <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <h4 class="small font-weight-bold">Clases completadas <span class="float-end">75%</span></h4>
              <div class="progress mb-4">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <h4 class="small font-weight-bold">Pistas utilizadas <span class="float-end">50%</span></h4>
              <div class="progress mb-4">
                <div class="progress-bar bg-warning" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="text-center mt-4">
                <small class="text-muted">Basado en tus últimos 30 días de actividad</small>
              </div>
            </div>
          </div>
        </div>
      </div>
        
      <!-- Links rápidos -->
      <div class="col-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Accesos Rápidos</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-3 col-md-6 mb-4">
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
              <div class="col-lg-3 col-md-6 mb-4">
                <a href="<?php echo URLROOT; ?>/user/courts" class="text-decoration-none">
                  <div class="card bg-success text-white shadow">
                    <div class="card-body text-center">
                      <i class="fas fa-volleyball-ball fa-3x mb-3"></i>
                      <h5>Pistas</h5>
                      <div class="text-white-50">Reserva pistas deportivas</div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-lg-3 col-md-6 mb-4">
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
              <div class="col-lg-3 col-md-6 mb-4">
                <a href="#" class="text-decoration-none">
                  <div class="card bg-warning text-white shadow">
                    <div class="card-body text-center">
                      <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                      <h5>Eventos</h5>
                      <div class="text-white-50">Próximos eventos</div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Rutina recomendada -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow">
            <div class="card-header py-3 bg-gradient-primary text-white">
              <h6 class="m-0 font-weight-bold">Rutina Recomendada para Hoy</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <h5 class="card-title">Entrenamiento Completo de Cuerpo</h5>
                  <p class="card-text">Esta rutina está diseñada para trabajar todos los grupos musculares con ejercicios compuestos que maximizan el gasto calórico y desarrollan fuerza funcional.</p>
                  
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Ejercicio</th>
                          <th>Series</th>
                          <th>Repeticiones</th>
                          <th>Descanso</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>Sentadillas</td>
                          <td>4</td>
                          <td>12</td>
                          <td>60s</td>
                        </tr>
                        <tr>
                          <td>Press de banca</td>
                          <td>4</td>
                          <td>10</td>
                          <td>90s</td>
                        </tr>
                        <tr>
                          <td>Peso muerto</td>
                          <td>3</td>
                          <td>8</td>
                          <td>120s</td>
                        </tr>
                        <tr>
                          <td>Pull-ups</td>
                          <td>3</td>
                          <td>Máx.</td>
                          <td>90s</td>
                        </tr>
                        <tr>
                          <td>Plancha</td>
                          <td>3</td>
                          <td>60s</td>
                          <td>60s</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  
                  <div class="mt-3">
                    <a href="#" class="btn btn-success">Ver Rutina Completa</a>
                    <a href="#" class="btn btn-outline-primary ms-2">Marcar como Completada</a>
                  </div>
                </div>
                <div class="col-md-4 text-center">
                  <img src="https://via.placeholder.com/300x200?text=Imagen+Rutina" class="img-fluid rounded" alt="Rutina de entrenamiento">
                  <div class="mt-3">
                    <span class="text-muted">Duración estimada: 45-60 minutos</span>
                    <div class="d-flex justify-content-center mt-2">
                      <span class="badge bg-primary me-1">Fuerza</span>
                      <span class="badge bg-success me-1">Resistencia</span>
                      <span class="badge bg-warning">Principiante-Intermedio</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
