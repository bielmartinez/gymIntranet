<?php 
// El header y footer ahora son incluidos por direct-view.php
// No necesitamos incluirlos aquí
// Solo mantenemos el logger para registro de actividad
include_once __DIR__ . '/../../utils/Logger.php';
Logger::log('INFO', 'Acceso a staff/class_management.php');
?>

<div class="container-fluid">
  <div class="row">
    <!-- Main content -->
    <main class="col-12 px-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Clases</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newClassModal">
            <i class="fas fa-plus me-1"></i> Nueva Clase
          </button>
        </div>
      </div>

      <!-- Filtros y selector de vista -->
      <div class="row mb-4">
        <div class="col-md-8">
          <div class="btn-group me-3">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="viewWeek">Vista Semanal</button>
            <button type="button" class="btn btn-sm btn-outline-secondary active" id="viewList">Vista Lista</button>
          </div>
          
          <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              Filtrar por tipo
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Todas las clases</a></li>
              <li><a class="dropdown-item" href="#">Yoga</a></li>
              <li><a class="dropdown-item" href="#">Pilates</a></li>
              <li><a class="dropdown-item" href="#">Funcional</a></li>
              <li><a class="dropdown-item" href="#">Spinning</a></li>
              <li><a class="dropdown-item" href="#">Zumba</a></li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <div class="input-group">
            <input type="text" class="form-control form-control-sm" placeholder="Buscar clase...">
            <button class="btn btn-sm btn-outline-secondary" type="button">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Vista de calendario semanal (oculto por defecto) -->
      <div id="calendarView" style="display: none;" class="mb-4">
        <div id="staffCalendar"></div>
      </div>

      <!-- Vista de lista (visible por defecto) -->
      <div id="listView" class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Mis Clases</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Clase</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Duración</th>
                  <th>Sala</th>
                  <th>Inscritos</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Yoga Avanzado</td>
                  <td>10/04/2025</td>
                  <td>09:00</td>
                  <td>60 min</td>
                  <td>Sala 3</td>
                  <td>12/15</td>
                  <td><span class="badge bg-success">Confirmada</span></td>
                  <td>
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editClassModal">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                        <i class="fas fa-clipboard-list"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Pilates Intermedio</td>
                  <td>10/04/2025</td>
                  <td>11:00</td>
                  <td>55 min</td>
                  <td>Sala 2</td>
                  <td>8/12</td>
                  <td><span class="badge bg-success">Confirmada</span></td>
                  <td>
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editClassModal">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                        <i class="fas fa-clipboard-list"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Funcional HIIT</td>
                  <td>11/04/2025</td>
                  <td>17:30</td>
                  <td>45 min</td>
                  <td>Sala 1</td>
                  <td>5/20</td>
                  <td><span class="badge bg-warning">Baja asistencia</span></td>
                  <td>
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editClassModal">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                        <i class="fas fa-clipboard-list"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Spinning Intenso</td>
                  <td>12/04/2025</td>
                  <td>10:00</td>
                  <td>50 min</td>
                  <td>Sala Spinning</td>
                  <td>18/20</td>
                  <td><span class="badge bg-success">Confirmada</span></td>
                  <td>
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editClassModal">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                        <i class="fas fa-clipboard-list"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Estadísticas de asistencia -->
      <div class="row">
        <div class="col-lg-6">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Asistencia Media por Tipo de Clase</h6>
            </div>
            <div class="card-body">
              <div class="chart-container" style="position: relative; height:250px;">
                <canvas id="classTypeChart"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Asistencia por Día de la Semana</h6>
            </div>
            <div class="card-body">
              <div class="chart-container" style="position: relative; height:250px;">
                <canvas id="weekdayChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal Nueva Clase -->
<div class="modal fade" id="newClassModal" tabindex="-1" aria-labelledby="newClassModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newClassModalLabel">Crear Nueva Clase</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="className" class="form-label">Nombre de la clase</label>
              <input type="text" class="form-control" id="className" placeholder="ej. Yoga Principiantes">
            </div>
            <div class="col-md-6">
              <label for="classType" class="form-label">Tipo de clase</label>
              <select class="form-select" id="classType">
                <option selected>Selecciona el tipo...</option>
                <option value="yoga">Yoga</option>
                <option value="pilates">Pilates</option>
                <option value="funcional">Funcional</option>
                <option value="spinning">Spinning</option>
                <option value="zumba">Zumba</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="classDate" class="form-label">Fecha</label>
              <input type="date" class="form-control" id="classDate">
            </div>
            <div class="col-md-3">
              <label for="startTime" class="form-label">Hora inicio</label>
              <input type="time" class="form-control" id="startTime">
            </div>
            <div class="col-md-3">
              <label for="duration" class="form-label">Duración (min)</label>
              <input type="number" class="form-control" id="duration" min="30" max="120" value="60">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="classRoom" class="form-label">Sala</label>
              <select class="form-select" id="classRoom">
                <option selected>Selecciona la sala...</option>
                <option value="1">Sala 1 - Multiusos</option>
                <option value="2">Sala 2 - Yoga/Pilates</option>
                <option value="3">Sala 3 - Funcional</option>
                <option value="4">Sala Spinning</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="capacity" class="form-label">Capacidad máxima</label>
              <input type="number" class="form-control" id="capacity" min="5" max="30" value="15">
            </div>
          </div>
          <div class="mb-3">
            <label for="classDescription" class="form-label">Descripción</label>
            <textarea class="form-control" id="classDescription" rows="3" placeholder="Describe brevemente la clase..."></textarea>
          </div>
          <div class="mb-3">
            <label for="classLevel" class="form-label">Nivel</label>
            <div class="btn-group w-100" role="group" aria-label="Nivel de clase">
              <input type="radio" class="btn-check" name="classLevel" id="levelBeginner" autocomplete="off">
              <label class="btn btn-outline-success" for="levelBeginner">Principiante</label>
              
              <input type="radio" class="btn-check" name="classLevel" id="levelIntermediate" autocomplete="off" checked>
              <label class="btn btn-outline-warning" for="levelIntermediate">Intermedio</label>
              
              <input type="radio" class="btn-check" name="classLevel" id="levelAdvanced" autocomplete="off">
              <label class="btn btn-outline-danger" for="levelAdvanced">Avanzado</label>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="repeatClass">
                <label class="form-check-label" for="repeatClass">
                  Repetir semanalmente
                </label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn" style="background-color: #f2f6f8; color: #000;">Crear Clase</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Clase -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editClassModalLabel">Editar Clase</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Similar al formulario de nueva clase pero con datos prellenados -->
        <form>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="editClassName" class="form-label">Nombre de la clase</label>
              <input type="text" class="form-control" id="editClassName" value="Yoga Avanzado">
            </div>
            <div class="col-md-6">
              <label for="editClassType" class="form-label">Tipo de clase</label>
              <select class="form-select" id="editClassType">
                <option>Selecciona el tipo...</option>
                <option value="yoga" selected>Yoga</option>
                <option value="pilates">Pilates</option>
                <option value="funcional">Funcional</option>
                <option value="spinning">Spinning</option>
                <option value="zumba">Zumba</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="editClassDate" class="form-label">Fecha</label>
              <input type="date" class="form-control" id="editClassDate" value="2025-04-10">
            </div>
            <div class="col-md-3">
              <label for="editStartTime" class="form-label">Hora inicio</label>
              <input type="time" class="form-control" id="editStartTime" value="09:00">
            </div>
            <div class="col-md-3">
              <label for="editDuration" class="form-label">Duración (min)</label>
              <input type="number" class="form-control" id="editDuration" min="30" max="120" value="60">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Control de Asistencia -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attendanceModalLabel">Control de Asistencia - Yoga Avanzado (10/04/2025)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-2">
            <h6>Lista de Inscritos (12/15)</h6>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="markAllPresent">
              <label class="form-check-label" for="markAllPresent">Marcar todos como presentes</label>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Socio</th>
                  <th>Email</th>
                  <th>Asistencia</th>
                  <th>Comentarios</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Ana Martínez</td>
                  <td>ana.martinez@email.com</td>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="attendanceCheck1" checked>
                      <label class="form-check-label" for="attendanceCheck1">Presente</label>
                    </div>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Añadir comentario">
                      <i class="fas fa-comment"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>Carlos López</td>
                  <td>carlos.lopez@email.com</td>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="attendanceCheck2" checked>
                      <label class="form-check-label" for="attendanceCheck2">Presente</label>
                    </div>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Añadir comentario">
                      <i class="fas fa-comment"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>Elena Rodríguez</td>
                  <td>elena.rodriguez@email.com</td>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="attendanceCheck3">
                      <label class="form-check-label" for="attendanceCheck3">Presente</label>
                    </div>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Ha enviado justificante">
                      <i class="fas fa-comment"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Guardar Asistencia</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Cambio entre vistas de lista y calendario
  const viewWeekBtn = document.getElementById('viewWeek');
  const viewListBtn = document.getElementById('viewList');
  const calendarView = document.getElementById('calendarView');
  const listView = document.getElementById('listView');
  
  viewWeekBtn.addEventListener('click', function() {
    calendarView.style.display = 'block';
    listView.style.display = 'none';
    viewWeekBtn.classList.add('active');
    viewListBtn.classList.remove('active');
    
    // Inicializar calendario si no se ha hecho ya
    if (!window.staffCalendarObj) {
      initializeCalendar();
    }
  });
  
  viewListBtn.addEventListener('click', function() {
    calendarView.style.display = 'none';
    listView.style.display = 'block';
    viewWeekBtn.classList.remove('active');
    viewListBtn.classList.add('active');
  });
  
  // Función para inicializar el calendario
  function initializeCalendar() {
    const calendarEl = document.getElementById('staffCalendar');
    window.staffCalendarObj = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridWeek',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridWeek,timeGridDay'
      },
      slotMinTime: '07:00:00',
      slotMaxTime: '22:00:00',
      allDaySlot: false,
      locale: 'es',
      events: [
        {
          title: 'Yoga Avanzado',
          start: '2025-04-10T09:00:00',
          end: '2025-04-10T10:00:00',
          backgroundColor: '#4e73df'
        },
        {
          title: 'Pilates Intermedio',
          start: '2025-04-10T11:00:00',
          end: '2025-04-10T11:55:00',
          backgroundColor: '#1cc88a'
        },
        {
          title: 'Funcional HIIT',
          start: '2025-04-11T17:30:00',
          end: '2025-04-11T18:15:00',
          backgroundColor: '#36b9cc'
        },
        {
          title: 'Spinning Intenso',
          start: '2025-04-12T10:00:00',
          end: '2025-04-12T10:50:00',
          backgroundColor: '#f6c23e'
        }
      ]
    });
    window.staffCalendarObj.render();
  }
  
  // Gráficos de estadísticas
  const classTypeCtx = document.getElementById('classTypeChart').getContext('2d');
  const classTypeChart = new Chart(classTypeCtx, {
    type: 'bar',
    data: {
      labels: ['Yoga', 'Pilates', 'Funcional', 'Spinning', 'Zumba'],
      datasets: [{
        label: '% de asistencia',
        data: [85, 72, 65, 90, 78],
        backgroundColor: [
          '#150000'
        ],
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          ticks: {
            callback: function(value) {
              return value + '%';
            }
          }
        }
      }
    }
  });
  
  const weekdayCtx = document.getElementById('weekdayChart').getContext('2d');
  const weekdayChart = new Chart(weekdayCtx, {
    type: 'line',
    data: {
      labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
      datasets: [{
        label: 'Núm. de asistentes',
        data: [42, 38, 45, 40, 52, 35, 20],
        borderColor: '#4e73df',
        backgroundColor: 'rgba(78, 115, 223, 0.2)',
        fill: true,
        tension: 0.1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
});
</script>
