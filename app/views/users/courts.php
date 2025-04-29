<?php 
// El header y footer ahora lo incluye direct-view.php, no necesitamos incluirlo aquí
// Tampoco necesitamos redefenir URLROOT ya que lo define direct-view.php
?>

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reserva de Pistas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary active" id="view-calendar">Vista Calendario</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="view-list">Vista Lista</button>
          </div>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-filter"></i> Filtrar
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
              <li><a class="dropdown-item filter-court" href="#" data-filter="all">Todas las pistas</a></li>
              <li><a class="dropdown-item filter-court" href="#" data-filter="Tenis">Tenis</a></li>
              <li><a class="dropdown-item filter-court" href="#" data-filter="Pádel">Pádel</a></li>
              <li><a class="dropdown-item filter-court" href="#" data-filter="Fútbol Sala">Fútbol Sala</a></li>
              <li><a class="dropdown-item filter-court" href="#" data-filter="Baloncesto">Baloncesto</a></li>
            </ul>
          </div>
        </div>
      </div>
      
      <!-- Vista de calendario -->
      <div class="card shadow mb-4" id="calendar-view">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Disponibilidad de Pistas</h6>
          <div class="dropdown no-arrow">
            <a href="#" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newBookingModal">
              <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Reserva
            </a>
          </div>
        </div>
        <div class="card-body">
          <div id="courts-calendar"></div>
        </div>
      </div>

      <!-- Vista de lista de reservas -->
      <div class="card shadow mb-4" id="list-view" style="display: none;">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Mis Reservas de Pistas</h6>
          <a href="#" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newBookingModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Reserva
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Pista</th>
                  <th>Tipo</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Duración</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Pista 1</td>
                  <td>Pádel</td>
                  <td>25/04/2025</td>
                  <td>17:00</td>
                  <td>60 min</td>
                  <td><span class="badge bg-success">Confirmada</span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                  </td>
                </tr>
                <tr>
                  <td>Pista 3</td>
                  <td>Tenis</td>
                  <td>27/04/2025</td>
                  <td>10:00</td>
                  <td>90 min</td>
                  <td><span class="badge bg-success">Confirmada</span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                  </td>
                </tr>
                <tr>
                  <td>Pista 2</td>
                  <td>Pádel</td>
                  <td>30/04/2025</td>
                  <td>19:30</td>
                  <td>60 min</td>
                  <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Pistas destacadas disponibles hoy -->
      <div class="row mb-4">
        <div class="col-12">
          <h4 class="mb-3">Pistas Destacadas Disponibles Hoy</h4>
        </div>

        <!-- Tarjeta de pista -->
        <div class="col-lg-4 mb-4 court-card" data-court-type="Pádel">
          <div class="card shadow h-100">
            <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Pista 1 - Pádel</h5>
              <span class="badge bg-light text-success">Disponible</span>
            </div>
            <img src="https://via.placeholder.com/400x200?text=Pista+de+Padel" class="card-img-top" alt="Pista de Pádel" style="height: 180px; object-fit: cover;">
            <div class="card-body">
              <p class="card-text">Pista de pádel reglamentaria con paredes de cristal templado y césped artificial de última generación.</p>
              <div class="d-flex justify-content-between align-items-center my-3">
                <span><i class="far fa-clock me-1"></i> 60/90 min</span>
                <span><i class="fas fa-euro-sign me-1"></i> 15€/hora</span>
              </div>
              <hr>
              <h6 class="mb-3 text-center">Horas disponibles hoy:</h6>
              <div class="row g-2 mb-2">
                <div class="col-6">
                  <button class="btn btn-outline-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="1" data-time="14:00">14:00 - 15:00</button>
                </div>
                <div class="col-6">
                  <button class="btn btn-outline-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="1" data-time="15:00">15:00 - 16:00</button>
                </div>
              </div>
              <div class="row g-2">
                <div class="col-6">
                  <button class="btn btn-outline-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="1" data-time="17:00">17:00 - 18:00</button>
                </div>
                <div class="col-6">
                  <button class="btn btn-outline-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="1" data-time="20:00">20:00 - 21:00</button>
                </div>
              </div>
            </div>
            <div class="card-footer bg-light">
              <div class="d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-users me-1"></i> Aforo máx.: 4 personas</small>
                <small class="text-muted"><i class="fas fa-star me-1"></i> 4.8/5</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de pista -->
        <div class="col-lg-4 mb-4 court-card" data-court-type="Tenis">
          <div class="card shadow h-100">
            <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Pista 3 - Tenis</h5>
              <span class="badge bg-light text-success">Disponible</span>
            </div>
            <img src="https://via.placeholder.com/400x200?text=Pista+de+Tenis" class="card-img-top" alt="Pista de Tenis" style="height: 180px; object-fit: cover;">
            <div class="card-body">
              <p class="card-text">Pista de tenis de tierra batida con iluminación profesional y dimensiones reglamentarias.</p>
              <div class="d-flex justify-content-between align-items-center my-3">
                <span><i class="far fa-clock me-1"></i> 60/90 min</span>
                <span><i class="fas fa-euro-sign me-1"></i> 18€/hora</span>
              </div>
              <hr>
              <h6 class="mb-3 text-center">Horas disponibles hoy:</h6>
              <div class="row g-2 mb-2">
                <div class="col-6">
                  <button class="btn btn-outline-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="3" data-time="13:00">13:00 - 14:00</button>
                </div>
                <div class="col-6">
                  <button class="btn btn-outline-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="3" data-time="16:00">16:00 - 17:00</button>
                </div>
              </div>
              <div class="row g-2">
                <div class="col-6">
                  <button class="btn btn-outline-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="3" data-time="18:00">18:00 - 19:00</button>
                </div>
                <div class="col-6">
                  <button class="btn btn-outline-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="3" data-time="21:00">21:00 - 22:00</button>
                </div>
              </div>
            </div>
            <div class="card-footer bg-light">
              <div class="d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-users me-1"></i> Aforo máx.: 4 personas</small>
                <small class="text-muted"><i class="fas fa-star me-1"></i> 4.5/5</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de pista -->
        <div class="col-lg-4 mb-4 court-card" data-court-type="Fútbol Sala">
          <div class="card shadow h-100">
            <div class="card-header bg-gradient-warning text-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Pista 5 - Fútbol Sala</h5>
              <span class="badge bg-light text-success">Disponible</span>
            </div>
            <img src="https://via.placeholder.com/400x200?text=Pista+de+Futbol+Sala" class="card-img-top" alt="Pista de Fútbol Sala" style="height: 180px; object-fit: cover;">
            <div class="card-body">
              <p class="card-text">Pista cubierta de fútbol sala con suelo sintético de alta calidad y marcador electrónico.</p>
              <div class="d-flex justify-content-between align-items-center my-3">
                <span><i class="far fa-clock me-1"></i> 60 min</span>
                <span><i class="fas fa-euro-sign me-1"></i> 25€/hora</span>
              </div>
              <hr>
              <h6 class="mb-3 text-center">Horas disponibles hoy:</h6>
              <div class="row g-2 mb-2">
                <div class="col-6">
                  <button class="btn btn-outline-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="5" data-time="15:00">15:00 - 16:00</button>
                </div>
                <div class="col-6">
                  <button class="btn btn-outline-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="5" data-time="16:00">16:00 - 17:00</button>
                </div>
              </div>
              <div class="row g-2">
                <div class="col-6">
                  <button class="btn btn-outline-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="5" data-time="19:00">19:00 - 20:00</button>
                </div>
                <div class="col-6">
                  <button class="btn btn-outline-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#newBookingModal" data-court="5" data-time="22:00">22:00 - 23:00</button>
                </div>
              </div>
            </div>
            <div class="card-footer bg-light">
              <div class="d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-users me-1"></i> Aforo máx.: 12 personas</small>
                <small class="text-muted"><i class="fas fa-star me-1"></i> 4.7/5</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Sección de información adicional -->
      <div class="row mb-4">
        <div class="col-lg-6 mb-4">
          <div class="card shadow">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Información de Reservas</h6>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <h5><i class="fas fa-info-circle text-info me-2"></i> Política de Reservas</h5>
                <ul class="list-group list-group-flush mb-3">
                  <li class="list-group-item">Las reservas se pueden realizar con hasta 7 días de antelación.</li>
                  <li class="list-group-item">Cancelación gratuita hasta 24 horas antes de la reserva.</li>
                  <li class="list-group-item">El pago se carga automáticamente a la cuenta de socio.</li>
                  <li class="list-group-item">Se requiere presentar el carnet de socio al acceder a la pista.</li>
                </ul>
              </div>
              <div>
                <h5><i class="fas fa-phone-alt text-success me-2"></i> Contacto</h5>
                <p>Para cualquier duda o problema con las reservas, contacta con recepción:</p>
                <p><i class="fas fa-phone me-2"></i> 91 234 56 78</p>
                <p><i class="fas fa-envelope me-2"></i> reservas@gymintranet.com</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-6 mb-4">
          <div class="card shadow">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Horarios y Precios</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Tipo de Pista</th>
                      <th>Precio/Hora</th>
                      <th>Precio Fin de Semana</th>
                      <th>Horario</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Pádel</td>
                      <td>15€</td>
                      <td>18€</td>
                      <td>9:00 - 22:00</td>
                    </tr>
                    <tr>
                      <td>Tenis</td>
                      <td>18€</td>
                      <td>22€</td>
                      <td>9:00 - 22:00</td>
                    </tr>
                    <tr>
                      <td>Fútbol Sala</td>
                      <td>25€</td>
                      <td>30€</td>
                      <td>9:00 - 23:00</td>
                    </tr>
                    <tr>
                      <td>Baloncesto</td>
                      <td>20€</td>
                      <td>25€</td>
                      <td>9:00 - 22:00</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="alert alert-info mt-3">
                <i class="fas fa-lightbulb me-2"></i> <strong>Consejo:</strong> Las horas valle (12:00-16:00) tienen un 15% de descuento de lunes a viernes.
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal de Nueva Reserva -->
<div class="modal fade" id="newBookingModal" tabindex="-1" aria-labelledby="newBookingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="newBookingModalLabel">Reservar Pista</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="courtBookingForm">
          <div class="mb-3">
            <label for="court-type" class="form-label">Tipo de Pista</label>
            <select class="form-select" id="court-type" required>
              <option value="">Seleccionar...</option>
              <option value="1">Pádel - Pista 1</option>
              <option value="2">Pádel - Pista 2</option>
              <option value="3">Tenis - Pista 3</option>
              <option value="4">Tenis - Pista 4</option>
              <option value="5">Fútbol Sala - Pista 5</option>
              <option value="6">Baloncesto - Pista 6</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="booking-date" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="booking-date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="booking-start" class="form-label">Hora de inicio</label>
              <select class="form-select" id="booking-start" required>
                <option value="">Seleccionar...</option>
                <option value="09:00">09:00</option>
                <option value="10:00">10:00</option>
                <option value="11:00">11:00</option>
                <option value="12:00">12:00</option>
                <option value="13:00">13:00</option>
                <option value="14:00">14:00</option>
                <option value="15:00">15:00</option>
                <option value="16:00">16:00</option>
                <option value="17:00">17:00</option>
                <option value="18:00">18:00</option>
                <option value="19:00">19:00</option>
                <option value="20:00">20:00</option>
                <option value="21:00">21:00</option>
              </select>
            </div>
            <div class="col">
              <label for="booking-duration" class="form-label">Duración</label>
              <select class="form-select" id="booking-duration" required>
                <option value="60">60 minutos</option>
                <option value="90">90 minutos</option>
                <option value="120">120 minutos</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label for="booking-players" class="form-label">Número de jugadores</label>
            <select class="form-select" id="booking-players" required>
              <option value="2">2 jugadores</option>
              <option value="3">3 jugadores</option>
              <option value="4" selected>4 jugadores</option>
              <option value="5+">5 o más jugadores</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="booking-notes" class="form-label">Notas adicionales (opcional)</label>
            <textarea class="form-control" id="booking-notes" rows="2"></textarea>
          </div>
          <div class="alert alert-info">
            <small><i class="fas fa-info-circle me-2"></i>El precio de la reserva se cargará en tu cuenta de socio. Recuerda que puedes cancelar sin costo hasta 24h antes.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="confirmBooking">Confirmar Reserva</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts específicos para la vista de pistas -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle entre vista calendario y lista
    document.getElementById('view-calendar').addEventListener('click', function() {
      document.getElementById('calendar-view').style.display = 'block';
      document.getElementById('list-view').style.display = 'none';
      document.getElementById('view-calendar').classList.add('active');
      document.getElementById('view-list').classList.remove('active');
    });

    document.getElementById('view-list').addEventListener('click', function() {
      document.getElementById('calendar-view').style.display = 'none';
      document.getElementById('list-view').style.display = 'block';
      document.getElementById('view-list').classList.add('active');
      document.getElementById('view-calendar').classList.remove('active');
    });

    // Filtrar por tipo de pista
    document.querySelectorAll('.filter-court').forEach(item => {
      item.addEventListener('click', event => {
        event.preventDefault();
        const filter = event.target.dataset.filter;
        
        document.querySelectorAll('.court-card').forEach(card => {
          if (filter === 'all' || card.dataset.courtType === filter) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });

    // Pre-rellenar modal cuando se pulsan botones de horario
    document.querySelectorAll('[data-bs-target="#newBookingModal"]').forEach(button => {
      button.addEventListener('click', function() {
        const courtId = this.dataset.court;
        const time = this.dataset.time;
        
        if (courtId) {
          document.getElementById('court-type').value = courtId;
        }
        
        if (time) {
          const startSelect = document.getElementById('booking-start');
          for (let i = 0; i < startSelect.options.length; i++) {
            if (startSelect.options[i].value === time) {
              startSelect.selectedIndex = i;
              break;
            }
          }
        }
      });
    });

    // Manejar confirmación de reserva
    document.getElementById('confirmBooking').addEventListener('click', function() {
      // Aquí se enviaría la reserva al servidor via AJAX
      const modal = bootstrap.Modal.getInstance(document.getElementById('newBookingModal'));
      
      // Mostrar notificación de éxito
      const successAlert = document.createElement('div');
      successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
      successAlert.setAttribute('role', 'alert');
      successAlert.innerHTML = `
        <strong>¡Reserva completada!</strong> Tu pista ha sido reservada con éxito.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      document.body.appendChild(successAlert);
      
      // Cerrar el modal
      modal.hide();
      
      // Eliminar la alerta después de 3 segundos
      setTimeout(() => {
        successAlert.remove();
      }, 3000);
    });
    
    // Inicializar calendario para la vista de calendario (requiere FullCalendar)
    if (typeof FullCalendar !== 'undefined') {
      const calendarEl = document.getElementById('courts-calendar');
      if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'timeGridThreeDay',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridThreeDay,timeGridWeek'
          },
          views: {
            timeGridThreeDay: {
              type: 'timeGrid',
              duration: { days: 3 },
              buttonText: '3 días'
            }
          },
          slotMinTime: '09:00:00',
          slotMaxTime: '23:00:00',
          allDaySlot: false,
          height: 'auto',
          locale: 'es',
          events: [
            {
              title: 'Pista 1 - Reservada',
              start: '2025-04-24T10:00:00',
              end: '2025-04-24T11:00:00',
              color: '#e74a3b',
              resourceId: 'pista-1'
            },
            {
              title: 'Pista 1 - Reservada',
              start: '2025-04-24T11:00:00',
              end: '2025-04-24T12:00:00',
              color: '#e74a3b',
              resourceId: 'pista-1'
            },
            {
              title: 'Pista 2 - Reservada',
              start: '2025-04-24T17:00:00',
              end: '2025-04-24T18:30:00',
              color: '#e74a3b',
              resourceId: 'pista-2'
            },
            {
              title: 'Pista 3 - Reservada',
              start: '2025-04-24T19:00:00',
              end: '2025-04-24T20:00:00',
              color: '#e74a3b',
              resourceId: 'pista-3'
            },
            {
              title: 'Pista 1 - Tu Reserva',
              start: '2025-04-25T17:00:00',
              end: '2025-04-25T18:00:00',
              color: '#4e73df',
              resourceId: 'pista-1'
            },
            {
              title: 'Pista 3 - Tu Reserva',
              start: '2025-04-27T10:00:00',
              end: '2025-04-27T11:30:00',
              color: '#4e73df',
              resourceId: 'pista-3'
            }
          ],
          eventClick: function(info) {
            if (info.event.title.includes('Tu Reserva')) {
              alert('Esta es tu reserva. Puedes cancelarla si es necesario.');
            } else {
              alert('Esta pista ya está reservada.');
            }
          },
          dateClick: function(info) {
            const modal = new bootstrap.Modal(document.getElementById('newBookingModal'));
            document.getElementById('booking-date').value = info.dateStr.split('T')[0];
            const time = info.dateStr.split('T')[1].substr(0, 5);
            
            const startSelect = document.getElementById('booking-start');
            for (let i = 0; i < startSelect.options.length; i++) {
              if (startSelect.options[i].value === time) {
                startSelect.selectedIndex = i;
                break;
              }
            }
            
            modal.show();
          }
        });
        calendar.render();
      }
    }
  });
</script>
