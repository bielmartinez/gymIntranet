<?php 
// El header y footer ahora son incluidos por direct-view.php
// No necesitamos incluirlos aquí
?>

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Clases Disponibles</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="today-btn">Hoy</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="week-btn">Esta Semana</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="month-btn">Este Mes</button>
          </div>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-filter"></i> Filtrar
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
              <li><a class="dropdown-item filter-class" href="#" data-filter="all">Todas las clases</a></li>
              <li><a class="dropdown-item filter-class" href="#" data-filter="Yoga">Yoga</a></li>
              <li><a class="dropdown-item filter-class" href="#" data-filter="Pilates">Pilates</a></li>
              <li><a class="dropdown-item filter-class" href="#" data-filter="Funcional">Funcional</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item filter-difficulty" href="#" data-filter="all">Todos los niveles</a></li>
              <li><a class="dropdown-item filter-difficulty" href="#" data-filter="Principiante">Principiante</a></li>
              <li><a class="dropdown-item filter-difficulty" href="#" data-filter="Intermedio">Intermedio</a></li>
              <li><a class="dropdown-item filter-difficulty" href="#" data-filter="Avanzado">Avanzado</a></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Calendar View -->
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Calendario de Clases</h6>
          <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="calendarViewDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="calendarViewDropdown">
              <div class="dropdown-header">Vistas:</div>
              <a class="dropdown-item" href="#" id="weekView">Semanal</a>
              <a class="dropdown-item" href="#" id="dayView">Diaria</a>
              <a class="dropdown-item" href="#" id="listView">Lista</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div id="calendar"></div>
        </div>
      </div>

      <!-- Classes Available Today -->
      <h2 class="mt-4">Clases de Hoy</h2>
      <div class="row">
        <!-- Class Card -->
        <div class="col-md-4 mb-4 class-card" data-class-type="Yoga" data-difficulty="Principiante">
          <div class="card shadow h-100">
            <div class="card-header bg-primary text-white">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Yoga</h5>
                <span class="badge bg-light text-dark">Principiante</span>
              </div>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span><i class="far fa-clock me-2"></i> 10:00 - 11:00</span>
                <span><i class="fas fa-user-friends me-2"></i> 8/15</span>
              </div>
              <div class="mb-2">
                <span><i class="fas fa-map-marker-alt me-2"></i> Sala 2</span>
              </div>
              <div class="mb-3">
                <span><i class="fas fa-user me-2"></i> Maria Garcia</span>
              </div>
              <p class="small text-muted">Una clase de yoga suave para principiantes que combina posturas básicas con técnicas de respiración.</p>
              <div class="d-grid">
                <button class="btn" style="background-color: #b14aed; color: #fff;">Reservar</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Class Card -->
        <div class="col-md-4 mb-4 class-card" data-class-type="Funcional" data-difficulty="Intermedio">
          <div class="card shadow h-100">
            <div class="card-header bg-success text-white">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Entrenamiento Funcional</h5>
                <span class="badge bg-light text-dark">Intermedio</span>
              </div>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span><i class="far fa-clock me-2"></i> 18:30 - 19:15</span>
                <span><i class="fas fa-user-friends me-2"></i> 12/20</span>
              </div>
              <div class="mb-2">
                <span><i class="fas fa-map-marker-alt me-2"></i> Sala 1</span>
              </div>
              <div class="mb-3">
                <span><i class="fas fa-user me-2"></i> Carlos Ruiz</span>
              </div>
              <p class="small text-muted">Entrenamiento de alta intensidad que combina ejercicios funcionales para mejorar fuerza y resistencia.</p>
              <div class="d-grid">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bookClassModal" data-class-id="2">Reservar</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Class Card -->
        <div class="col-md-4 mb-4 class-card" data-class-type="Pilates" data-difficulty="Avanzado">
          <div class="card shadow h-100">
            <div class="card-header bg-warning text-white">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pilates Avanzado</h5>
                <span class="badge bg-light text-dark">Avanzado</span>
              </div>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span><i class="far fa-clock me-2"></i> 19:30 - 20:30</span>
                <span><i class="fas fa-user-friends me-2"></i> 5/12</span>
              </div>
              <div class="mb-2">
                <span><i class="fas fa-map-marker-alt me-2"></i> Sala 3</span>
              </div>
              <div class="mb-3">
                <span><i class="fas fa-user me-2"></i> Ana Martínez</span>
              </div>
              <p class="small text-muted">Clase avanzada de Pilates con ejercicios de mayor dificultad para fortalecer el core y mejorar la postura.</p>
              <div class="d-grid">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bookClassModal" data-class-id="3">Reservar</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tomorrow's Classes -->
      <h2 class="mt-4">Clases de Mañana</h2>
      <div class="row">
        <!-- More class cards could be added here -->
        <div class="col-md-4 mb-4 class-card" data-class-type="Yoga" data-difficulty="Intermedio">
          <div class="card shadow h-100">
            <div class="card-header bg-info text-white">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Yoga Intermedio</h5>
                <span class="badge bg-light text-dark">Intermedio</span>
              </div>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span><i class="far fa-clock me-2"></i> 09:00 - 10:15</span>
                <span><i class="fas fa-user-friends me-2"></i> 7/15</span>
              </div>
              <div class="mb-2">
                <span><i class="fas fa-map-marker-alt me-2"></i> Sala 2</span>
              </div>
              <div class="mb-3">
                <span><i class="fas fa-user me-2"></i> Maria Garcia</span>
              </div>
              <p class="small text-muted">Yoga de nivel intermedio con posturas más desafiantes y secuencias fluidas.</p>
              <div class="d-grid">
                <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#bookClassModal" data-class-id="4">Reservar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookClassModal" tabindex="-1" aria-labelledby="bookClassModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookClassModalLabel">Confirmar Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de que quieres reservar esta clase?</p>
        <div class="alert alert-info">
          <small>
            <i class="fas fa-info-circle me-2"></i>Recuerda que puedes cancelar tu reserva hasta 2 horas antes del inicio de la clase.
          </small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="confirmBooking">Confirmar Reserva</button>
      </div>
    </div>
  </div>
</div>

<!-- Include FullCalendar library -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridWeek',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridWeek,timeGridDay,listWeek'
      },
      slotMinTime: '07:00:00',
      slotMaxTime: '22:00:00',
      allDaySlot: false,
      height: 'auto',
      locale: 'es',
      events: [
        {
          title: 'Yoga',
          start: '2025-04-08T10:00:00',
          end: '2025-04-08T11:00:00',
          color: '#4e73df'
        },
        {
          title: 'Funcional',
          start: '2025-04-08T18:30:00',
          end: '2025-04-08T19:15:00',
          color: '#1cc88a'
        },
        {
          title: 'Pilates',
          start: '2025-04-08T19:30:00',
          end: '2025-04-08T20:30:00',
          color: '#f6c23e'
        },
        {
          title: 'Yoga Intermedio',
          start: '2025-04-09T09:00:00',
          end: '2025-04-09T10:15:00',
          color: '#36b9cc'
        }
        // More events could be added here
      ],
      eventClick: function(info) {
        // Show modal with class details
        const modal = new bootstrap.Modal(document.getElementById('bookClassModal'));
        modal.show();
      }
    });
    calendar.render();

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
    
    // Filter by difficulty
    document.querySelectorAll('.filter-difficulty').forEach(item => {
      item.addEventListener('click', event => {
        event.preventDefault();
        const filter = event.target.dataset.filter;
        
        document.querySelectorAll('.class-card').forEach(card => {
          if (filter === 'all' || card.dataset.difficulty === filter) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
    
    // Today, week, month buttons
    document.getElementById('today-btn').addEventListener('click', () => {
      calendar.today();
    });
    
    document.getElementById('week-btn').addEventListener('click', () => {
      calendar.changeView('timeGridWeek');
    });
    
    document.getElementById('month-btn').addEventListener('click', () => {
      calendar.changeView('dayGridMonth');
    });
    
    // Calendar view buttons
    document.getElementById('weekView').addEventListener('click', () => {
      calendar.changeView('timeGridWeek');
    });
    
    document.getElementById('dayView').addEventListener('click', () => {
      calendar.changeView('timeGridDay');
    });
    
    document.getElementById('listView').addEventListener('click', () => {
      calendar.changeView('listWeek');
    });
    
    // Handle booking
    document.getElementById('confirmBooking').addEventListener('click', function() {
      // Here you would send the booking to the server via AJAX
      const modal = bootstrap.Modal.getInstance(document.getElementById('bookClassModal'));
      
      // Show a success notification
      const successAlert = document.createElement('div');
      successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
      successAlert.setAttribute('role', 'alert');
      successAlert.innerHTML = `
        <strong>¡Reserva completada!</strong> Tu clase ha sido reservada con éxito.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      document.body.appendChild(successAlert);
      
      // Close the modal
      modal.hide();
      
      // Remove the alert after 3 seconds
      setTimeout(() => {
        successAlert.remove();
      }, 3000);
    });
  });
</script>
