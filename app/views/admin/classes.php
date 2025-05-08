<?php
// Mensajes de alerta (éxito, error, etc.)
if (isset($_SESSION['admin_message'])) {
    echo '<div class="alert alert-' . $_SESSION['admin_message_type'] . ' alert-dismissible fade show" role="alert">
            ' . $_SESSION['admin_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['admin_message']);
    unset($_SESSION['admin_message_type']);
}

// IMPORTANTE: Solo cargar las clases si no vienen ya de un filtrado
// Si hay datos de filtrado pasados desde el controlador, usarlos
if (!isset($data['classes'])) {
    // Cargar el modelo de clases
    require_once APPROOT . '/models/Class.php';
    $classModel = new Class_();
    
    // Cargar la información de las clases
    $classes = $classModel->getAllClasses();
} else {
    // Usar las clases ya filtradas
    $classes = $data['classes'];
}

// Cargar el modelo para tipos de clases si no hay datos de filtrado
if (!isset($data['classTypes'])) {
    require_once APPROOT . '/models/TypeClass.php';
    $typeClassModel = new TypeClass();
    $typeClasses = $typeClassModel->getAll();
} else {
    $typeClasses = $data['classTypes'];
}

// Cargar el modelo de personal para los monitores si no hay datos de filtrado
if (!isset($data['monitors'])) {
    require_once APPROOT . '/models/User.php';
    $userModel = new User();
    $monitors = $userModel->getAllMonitors();
} else {
    $monitors = $data['monitors'];
}

// Valores por defecto para los filtros
$filterDate = '';
$filterType = '';
$filterMonitor = '';

// Si hay filtros aplicados, establecer los valores seleccionados
if (isset($data['filters'])) {
    $filterDate = !empty($data['filters']['date']) ? $data['filters']['date'] : '';
    $filterType = !empty($data['filters']['type_id']) ? $data['filters']['type_id'] : '';
    $filterMonitor = !empty($data['filters']['monitor_id']) ? $data['filters']['monitor_id'] : '';
}
?>

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Clases</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
          <i class="fas fa-plus-circle me-2"></i>Nueva Clase
        </button>
      </div>

      <!-- Filtros de búsqueda -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
          <form id="filterForm" action="<?= URLROOT ?>/Admin/filterClasses" method="post" class="row g-3">
            <div class="col-md-3">
              <label for="filterDate" class="form-label">Fecha</label>
              <input type="date" class="form-control" id="filterDate" name="date" value="<?= $filterDate ?>">
            </div>
            <div class="col-md-3">
              <label for="filterType" class="form-label">Tipo de Clase</label>
              <select class="form-control" id="filterType" name="type_id">
                <option value="">Todos</option>
                <?php foreach ($typeClasses as $typeClass): ?>
                  <option value="<?= $typeClass->tipus_classe_id ?>" <?= ($filterType == $typeClass->tipus_classe_id) ? 'selected' : '' ?>><?= $typeClass->nom ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="filterMonitor" class="form-label">Monitor</label>
              <select class="form-control" id="filterMonitor" name="monitor_id">
                <option value="">Todos</option>
                <?php foreach ($monitors as $monitor): ?>
                  <option value="<?= $monitor->usuari_id ?>" <?= ($filterMonitor == $monitor->usuari_id) ? 'selected' : '' ?>><?= $monitor->nom . ' ' . $monitor->cognoms ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search me-2"></i>Buscar
              </button>
              <a href="<?= URLROOT ?>/Admin/classes" class="btn btn-secondary">
                <i class="fas fa-undo me-2"></i>Limpiar
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- Tabla de clases -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Clases Disponibles</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover" id="classesTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Tipo</th>
                  <th>Monitor</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Duración</th>
                  <th>Sala</th>
                  <th>Capacidad</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($classes)): ?>
                  <!-- No generamos contenido aquí, DataTables se encargará de mostrar el mensaje vacío -->
                <?php else: ?>
                  <?php foreach ($classes as $class): ?>
                    <tr>
                      <td><?= $class->classe_id ?></td>
                      <td><?= $class->tipus_nom ?></td>
                      <td><?= $class->monitor_nom ?></td>
                      <td><?= date('d/m/Y', strtotime($class->data)) ?></td>
                      <td><?= date('H:i', strtotime($class->hora)) ?></td>
                      <td><?= $class->duracio ?> min</td>
                      <td><?= $class->sala ?></td>
                      <td>
                        <?php
                          // Calcular porcentaje de ocupación para colorear la celda de capacidad
                          $porcentajeOcupacion = ($class->capacitat_maxima > 0) ? 
                                               ($class->capacitat_actual / $class->capacitat_maxima) * 100 : 0;
                          $claseBadge = $porcentajeOcupacion < 50 ? 'bg-success' : 
                                      ($porcentajeOcupacion < 80 ? 'bg-warning' : 'bg-danger');
                        ?>
                        <div class="badge <?= $claseBadge ?> text-white">
                          <?= $class->capacitat_actual ?>/<?= $class->capacitat_maxima ?>
                        </div>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-primary edit-class-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editClassModal"
                                data-class-id="<?= $class->classe_id ?>">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-class-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteClassModal"
                                data-class-id="<?= $class->classe_id ?>">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal para añadir nueva clase -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addClassModalLabel">Nueva Clase</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addClassForm" action="<?= URLROOT ?>/Admin/addClass" method="post">
          <div class="mb-3">
            <label for="classType" class="form-label">Tipo de Clase</label>
            <select class="form-control" id="classType" name="tipus_classe_id" required>
              <option value="">Seleccionar tipo</option>
              <?php foreach ($typeClasses as $typeClass): ?>
                <option value="<?= $typeClass->tipus_classe_id ?>"><?= $typeClass->nom ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="classMonitor" class="form-label">Monitor</label>
            <select class="form-control" id="classMonitor" name="monitor_id" required>
              <option value="">Seleccionar monitor</option>
              <?php foreach ($monitors as $monitor): ?>
                <option value="<?= $monitor->usuari_id ?>"><?= $monitor->nom . ' ' . $monitor->cognoms ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="classDate" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="classDate" name="data" required>
          </div>
          <div class="mb-3">
            <label for="classTime" class="form-label">Hora</label>
            <input type="time" class="form-control" id="classTime" name="hora" required>
          </div>
          <div class="mb-3">
            <label for="classDuration" class="form-label">Duración (minutos)</label>
            <input type="number" class="form-control" id="classDuration" name="duracio" min="15" max="60" step="5" required>
          </div>
          <div class="mb-3">
            <label for="classRoom" class="form-label">Sala</label>
            <input type="number" class="form-control" id="classRoom" name="sala" min="1" max="4" required>
          </div>
          <div class="mb-3">
            <label for="classCapacity" class="form-label">Capacidad Máxima</label>
            <input type="number" class="form-control" id="classCapacity" name="capacitat_maxima" min="5" max="20" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal para editar clase -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editClassModalLabel">Editar Clase</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editClassForm" action="<?= URLROOT ?>/Admin/updateClass" method="post">
          <input type="hidden" id="editClassId" name="classe_id">
          <div class="mb-3">
            <label for="editClassType" class="form-label">Tipo de Clase</label>
            <select class="form-control" id="editClassType" name="tipus_classe_id" required>
              <option value="">Seleccionar tipo</option>
              <?php foreach ($typeClasses as $typeClass): ?>
                <option value="<?= $typeClass->tipus_classe_id ?>"><?= $typeClass->nom ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="editClassMonitor" class="form-label">Monitor</label>
            <select class="form-control" id="editClassMonitor" name="monitor_id" required>
              <option value="">Seleccionar monitor</option>
              <?php foreach ($monitors as $monitor): ?>
                <option value="<?= $monitor->usuari_id ?>"><?= $monitor->nom . ' ' . $monitor->cognoms ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="editClassDate" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="editClassDate" name="data" required>
          </div>
          <div class="mb-3">
            <label for="editClassTime" class="form-label">Hora</label>
            <input type="time" class="form-control" id="editClassTime" name="hora" required>
          </div>
          <div class="mb-3">
            <label for="editClassDuration" class="form-label">Duración (minutos)</label>
            <input type="number" class="form-control" id="editClassDuration" name="duracio" min="15" max="60" step="5" required>
          </div>
          <div class="mb-3">
            <label for="editClassRoom" class="form-label">Sala</label>
            <input type="number" class="form-control" id="editClassRoom" name="sala" min="1" max="4" required>
          </div>
          <div class="mb-3">
            <label for="editClassCapacity" class="form-label">Capacidad Máxima</label>
            <input type="number" class="form-control" id="editClassCapacity" name="capacitat_maxima" min="5" max="20" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="deleteClassModal" tabindex="-1" aria-labelledby="deleteClassModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteClassModalLabel">Confirmar Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de que deseas eliminar esta clase? Esta acción no se puede deshacer.</p>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle me-2"></i> Se eliminarán también todas las reservas asociadas a esta clase.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="deleteClassForm" action="<?= URLROOT ?>/Admin/deleteClass" method="post">
          <input type="hidden" id="deleteClassId" name="classe_id">
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Añadir referencias a jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables para la tabla de clases cuando jQuery esté disponible
    setTimeout(function() {
      if (typeof $ !== 'undefined') {
        try {
          $('#classesTable').DataTable({
            language: {
              url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
              emptyTable: 'No hay clases disponibles'
            },
            order: [[3, 'asc'], [4, 'asc']], // Ordenar por fecha (col 3) y hora (col 4)
            responsive: true,
            columnDefs: [
              { targets: [7, 8], orderable: false } // Columnas no ordenables
            ]
          });
        } catch (error) {
          console.error('Error al inicializar DataTables:', error);
        }
      } else {
        console.warn('jQuery no está disponible para inicializar DataTables');
      }
    }, 500);

    // Manejar el evento de editar clase
    document.querySelectorAll('.edit-class-btn').forEach(button => {
      button.addEventListener('click', function() {
        const classId = this.getAttribute('data-class-id');
        
        // Mostrar mensaje de carga en el modal
        const editModal = document.getElementById('editClassModal');
        const loadingMessage = document.createElement('div');
        loadingMessage.className = 'text-center my-3';
        loadingMessage.innerHTML = '<div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando datos de la clase...</p>';
        
        // Añadir mensaje de carga al modal
        const modalBody = editModal.querySelector('.modal-body');
        const formElement = modalBody.querySelector('form');
        modalBody.insertBefore(loadingMessage, formElement);
        
        // Obtener datos de la clase desde el servidor
        fetch(`<?= URLROOT ?>/Admin/getClassDetails/${classId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
          })
          .then(data => {
            // Eliminar mensaje de carga
            if (loadingMessage) {
              loadingMessage.remove();
            }
            
            if (data.success) {
              console.log('Datos recibidos para editar clase:', data); // Para debug
              
              // Rellenar el formulario con los datos
              document.getElementById('editClassId').value = data.class.classe_id;
              document.getElementById('editClassType').value = data.class.tipus_classe_id;
              document.getElementById('editClassMonitor').value = data.class.monitor_id;
              document.getElementById('editClassDate').value = data.class.data;
              document.getElementById('editClassTime').value = data.class.hora;
              document.getElementById('editClassDuration').value = data.class.duracio;
              document.getElementById('editClassRoom').value = data.class.sala;
              document.getElementById('editClassCapacity').value = data.class.capacitat_maxima;
            } else {
              alert('Error al cargar los datos de la clase: ' + (data.error || 'Error desconocido'));
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error al conectar con el servidor: ' + error.message);
            
            // Eliminar mensaje de carga en caso de error
            if (loadingMessage) {
              loadingMessage.remove();
            }
          });
      });
    });

    // Manejar el evento de eliminar clase
    document.querySelectorAll('.delete-class-btn').forEach(button => {
      button.addEventListener('click', function() {
        const classId = this.getAttribute('data-class-id');
        document.getElementById('deleteClassId').value = classId;
      });
    });
    
    // Establecer la fecha actual en el filtro de fecha
    if (document.getElementById('filterDate')) {
      document.getElementById('filterDate').valueAsDate = new Date();
    }
    
    // Establecer fecha actual mínima en el formulario de crear clase
    if (document.getElementById('classDate')) {
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('classDate').min = today;
      document.getElementById('classDate').value = today;
    }
  });
</script>