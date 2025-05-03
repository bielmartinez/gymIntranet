<?php
/**
 * Vista para la gestión de clases por parte del personal (staff)
 * Permite ver, crear y editar clases
 */

// Cargar el modelo de clases
require_once APPROOT . '/models/Class.php';
$classModel = new Class_();

// Cargar la información de las clases
$classes = $classModel->getAllClasses();

// Cargar el modelo para tipos de clases
require_once APPROOT . '/models/TypeClass.php';
$typeClassModel = new TypeClass();
$typeClasses = $typeClassModel->getAll();

// Cargar el modelo de usuarios para los monitores
require_once APPROOT . '/models/User.php';
$userModel = new User();
$monitors = $userModel->getAllMonitors(); // Usar el nuevo método que incluye personal_id

// Obtener el ID del usuario actual (monitor)
$currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Filtrar clases por monitor actual si es necesario
$userClasses = [];
foreach ($classes as $class) {
    // Si es el monitor asignado a esta clase o es admin, añadir a la lista
    if ($class->monitor_id == $currentUserId || $_SESSION['user_role'] === 'admin') {
        $userClasses[] = $class;
    }
}
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><?php echo $data['title']; ?></h1>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#newClassModal">
                <i class="fas fa-plus-circle me-2"></i>Nueva clase
            </button>
        </div>
        <div class="card-body">
            <?php if(isset($_SESSION['staff_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['staff_message_type']; ?> alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['staff_message']; 
                        unset($_SESSION['staff_message']);
                        unset($_SESSION['staff_message_type']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Filtros de clase -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <form class="row g-3" id="classFilter">
                                <div class="col-md-3">
                                    <label for="dateFilter" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="dateFilter" name="dateFilter">
                                </div>
                                <div class="col-md-3">
                                    <label for="classTypeFilter" class="form-label">Tipo de clase</label>
                                    <select class="form-select" id="classTypeFilter" name="classTypeFilter">
                                        <option value="">Todas</option>
                                        <?php foreach ($typeClasses as $typeClass): ?>
                                            <option value="<?= $typeClass->tipus_classe_id ?>"><?= $typeClass->nom ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="roomFilter" class="form-label">Sala</label>
                                    <select class="form-select" id="roomFilter" name="roomFilter">
                                        <option value="">Todas</option>
                                        <?php 
                                        // Obtener salas únicas de las clases existentes
                                        $salas = [];
                                        foreach ($classes as $class) {
                                            if (!in_array($class->sala, $salas)) {
                                                $salas[] = $class->sala;
                                            }
                                        }
                                        foreach ($salas as $sala): 
                                            if (!empty($sala)):
                                        ?>
                                            <option value="<?= $sala ?>"><?= $sala ?></option>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                                    <button type="reset" class="btn btn-secondary">Limpiar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Listado de clases -->
            <div class="table-responsive">
                <table class="table table-hover" id="classesTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Duración</th>
                            <th>Sala</th>
                            <th>Capacidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($userClasses)): ?>
                            <!-- No generamos contenido aquí, DataTables se encargará de mostrar el mensaje -->
                        <?php else: ?>
                            <?php foreach ($userClasses as $class): 
                                // Obtener información del tipo de clase
                                $typeClass = $typeClassModel->getById($class->tipus_classe_id);
                                // Calcular porcentaje de ocupación para la barra de progreso
                                $porcentajeOcupacion = ($class->capacitat_maxima > 0) ? 
                                                      ($class->capacitat_actual / $class->capacitat_maxima) * 100 : 0;
                                $barraColor = $porcentajeOcupacion < 50 ? 'bg-success' : 
                                             ($porcentajeOcupacion < 80 ? 'bg-warning' : 'bg-danger');
                            ?>
                                <tr>
                                    <td><?= $class->classe_id ?></td>
                                    <td><?= $typeClass ? $typeClass->nom : 'N/A' ?></td>
                                    <td><?= date('d/m/Y', strtotime($class->data)) ?></td>
                                    <td><?= date('H:i', strtotime($class->hora)) ?></td>
                                    <td><?= $class->duracio ?> min</td>
                                    <td><?= $class->sala ?></td>
                                    <td>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar <?= $barraColor ?>" role="progressbar" 
                                                 style="width: <?= $porcentajeOcupacion ?>%;" 
                                                 aria-valuenow="<?= $class->capacitat_actual ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="<?= $class->capacitat_maxima ?>">
                                                <?= $class->capacitat_actual ?>/<?= $class->capacitat_maxima ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm view-class-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewClassModal" 
                                                    data-class-id="<?= $class->classe_id ?>">
                                                <i class="fas fa-eye" title="Ver detalles"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm edit-class-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editClassModal" 
                                                    data-class-id="<?= $class->classe_id ?>">
                                                <i class="fas fa-edit" title="Editar"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmDeleteClass(<?= $class->classe_id ?>)">
                                                <i class="fas fa-trash" title="Eliminar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nueva clase -->
<div class="modal fade" id="newClassModal" tabindex="-1" aria-labelledby="newClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newClassModalLabel">Nueva Clase</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newClassForm" action="<?= URLROOT ?>/staff/addClass" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="classType" class="form-label">Tipo de clase</label>
                            <select class="form-select" id="classType" name="tipus_classe_id" required>
                                <option value="">Seleccione un tipo</option>
                                <?php foreach ($typeClasses as $typeClass): ?>
                                    <option value="<?= $typeClass->tipus_classe_id ?>"><?= $typeClass->nom ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="classDate" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="classDate" name="data" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="classTime" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="classTime" name="hora" required>
                        </div>
                        <div class="col-md-4">
                            <label for="classDuration" class="form-label">Duración (minutos)</label>
                            <input type="number" class="form-control" id="classDuration" name="duracio" min="15" max="120" step="5" value="60" required>
                        </div>
                        <div class="col-md-4">
                            <label for="classRoom" class="form-label">Sala</label>
                            <input type="text" class="form-control" id="classRoom" name="sala" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="classCapacity" class="form-label">Capacidad máxima</label>
                            <input type="number" class="form-control" id="classCapacity" name="capacitat_maxima" min="1" max="50" value="20" required>
                        </div>
                        <input type="hidden" name="monitor_id" value="<?= $currentUserId ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" form="newClassForm">Crear clase</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar clase -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editClassModalLabel">Editar Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editClassForm" action="<?= URLROOT ?>/staff/updateClass" method="post">
                    <input type="hidden" id="editClassId" name="classe_id" value="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editClassType" class="form-label">Tipo de clase</label>
                            <select class="form-select" id="editClassType" name="tipus_classe_id" required>
                                <option value="">Seleccione un tipo</option>
                                <?php foreach ($typeClasses as $typeClass): ?>
                                    <option value="<?= $typeClass->tipus_classe_id ?>"><?= $typeClass->nom ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editClassDate" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="editClassDate" name="data" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editClassTime" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="editClassTime" name="hora" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editClassDuration" class="form-label">Duración (minutos)</label>
                            <input type="number" class="form-control" id="editClassDuration" name="duracio" min="15" max="120" step="5" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editClassRoom" class="form-label">Sala</label>
                            <input type="text" class="form-control" id="editClassRoom" name="sala" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editClassCapacity" class="form-label">Capacidad máxima</label>
                            <input type="number" class="form-control" id="editClassCapacity" name="capacitat_maxima" min="1" max="50" required>
                        </div>
                        <input type="hidden" id="editClassMonitor" name="monitor_id" value="<?= $currentUserId ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning" form="editClassForm">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de clase -->
<div class="modal fade" id="viewClassModal" tabindex="-1" aria-labelledby="viewClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewClassModalLabel">Detalles de la Clase</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Tipo de clase</h6>
                        <p class="lead" id="viewClassType">Cargando...</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Fecha y hora</h6>
                        <p class="lead" id="viewClassDateTime">Cargando...</p>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted">Duración</h6>
                        <p class="lead" id="viewClassDuration">Cargando...</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Sala</h6>
                        <p class="lead" id="viewClassRoom">Cargando...</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Ocupación</h6>
                        <p class="lead" id="viewClassOccupation">Cargando...</p>
                    </div>
                </div>
                
                <h5 class="border-bottom pb-2 mb-3">Alumnos inscritos</h5>
                <div id="studentTableBody">
                    <!-- Aquí se cargarán los alumnos desde la base de datos mediante JavaScript -->
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando alumnos...</span>
                        </div>
                        <p class="mt-2">Cargando alumnos...</p>
                    </div>
                </div>

                <div id="noStudents" class="alert alert-info text-center" style="display:none;">
                    <i class="fas fa-info-circle me-2"></i> No hay alumnos inscritos en esta clase.
                </div>

                <template id="studentRowTemplate">
                    <div class="card mb-2 student-card">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-1">
                                    <span class="badge bg-secondary student-id"></span>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-0 student-name"></h6>
                                </div>
                                <div class="col-md-3">
                                    <span class="student-email text-muted small"></span>
                                </div>
                                <div class="col-md-2">
                                    <span class="student-date small"></span>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input attendance-switch" type="checkbox" role="switch">
                                        <label class="form-check-label">Asistencia</label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-sm btn-outline-danger cancel-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="saveAttendance()">Guardar asistencia</button>
            </div>
        </div>
    </div>
</div>

<!-- Formulario para eliminar clase (se envía mediante JavaScript) -->
<form id="deleteClassForm" action="<?= URLROOT ?>/staff/deleteClass" method="post" style="display: none;">
    <input type="hidden" id="deleteClassId" name="classe_id">
</form>

<!-- Añadir referencias a jQuery y DataTables antes del script -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar a que jQuery esté cargado
        setTimeout(function() {
            if (typeof $ !== 'undefined') {
                try {
                    // Inicializar DataTables para la tabla de clases
                    if (document.getElementById('classesTable')) {
                        const dataTable = $('#classesTable').DataTable({
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
                                emptyTable: 'No hay clases disponibles'
                            },
                            order: [[2, 'asc'], [3, 'asc']], // Ordenar por fecha (col 2) y hora (col 3)
                            responsive: true,
                            columnDefs: [
                                { targets: 6, orderable: false }, // Columna de capacidad no ordenable
                                { targets: 7, orderable: false }  // Columna de acciones no ordenable
                            ],
                            drawCallback: function() {
                                // Reenlazar eventos después de redibujado de tabla
                                $('.view-class-btn').off().on('click', function() {
                                    const classId = $(this).attr('data-class-id');
                                    loadClassDetails(classId);
                                });
                                $('.edit-class-btn').off().on('click', function() {
                                    const classId = $(this).attr('data-class-id');
                                    loadClassForEditing(classId);
                                });
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error al inicializar DataTables:', error);
                }
            } else {
                console.warn('jQuery no está disponible para inicializar DataTables');
            }
        }, 500); // Esperar 500ms para asegurar que jQuery se ha cargado
        
        // Inicializar filtro de fechas con la fecha actual
        document.getElementById('dateFilter').valueAsDate = new Date();
        
        // Manejar el evento de ver detalles de una clase
        document.querySelectorAll('.view-class-btn').forEach(button => {
            button.addEventListener('click', function() {
                const classId = this.getAttribute('data-class-id');
                loadClassDetails(classId);
            });
        });
        
        // Manejar el evento de editar una clase
        document.querySelectorAll('.edit-class-btn').forEach(button => {
            button.addEventListener('click', function() {
                const classId = this.getAttribute('data-class-id');
                loadClassForEditing(classId);
            });
        });
    });
    
    // Función para cargar detalles de una clase
    function loadClassDetails(classId) {
        fetch(`<?= URLROOT ?>/staff/getClassDetails/${classId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar detalles de la clase
                    document.getElementById('viewClassType').textContent = data.typeClassName || 'N/A';
                    document.getElementById('viewClassDateTime').textContent = 
                        `${data.class.data} - ${data.class.hora}`;
                    document.getElementById('viewClassDuration').textContent = 
                        `${data.class.duracio} minutos`;
                    document.getElementById('viewClassRoom').textContent = data.class.sala;
                    document.getElementById('viewClassOccupation').textContent = 
                        `${data.class.capacitat_actual}/${data.class.capacitat_maxima}`;
                    
                    // Cargar estudiantes inscritos si están disponibles
                    const studentsTable = document.getElementById('studentTableBody');
                    studentsTable.innerHTML = '';
                    
                    if (data.students && data.students.length > 0) {
                        data.students.forEach(student => {
                            const template = document.getElementById('studentRowTemplate').content.cloneNode(true);
                            template.querySelector('.student-id').textContent = student.usuari_id;
                            template.querySelector('.student-name').textContent = `${student.nom} ${student.cognoms}`;
                            template.querySelector('.student-email').textContent = student.correu;
                            template.querySelector('.student-date').textContent = student.data_reserva;
                            const attendanceSwitch = template.querySelector('.attendance-switch');
                            attendanceSwitch.setAttribute('data-reservation-id', student.reserva_id);
                            attendanceSwitch.checked = student.assistencia ? true : false;
                            template.querySelector('.cancel-btn').addEventListener('click', () => {
                                cancelReservation(student.reserva_id, classId);
                            });
                            studentsTable.appendChild(template);
                        });
                    } else {
                        document.getElementById('noStudents').style.display = 'block';
                    }
                } else {
                    alert('Error al cargar los detalles de la clase');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
    }
    
    // Función para cargar una clase para edición
    function loadClassForEditing(classId) {
        fetch(`<?= URLROOT ?>/staff/getClassDetails/${classId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editClassId').value = data.class.classe_id;
                    document.getElementById('editClassType').value = data.class.tipus_classe_id;
                    document.getElementById('editClassDate').value = data.class.data;
                    document.getElementById('editClassTime').value = data.class.hora;
                    document.getElementById('editClassDuration').value = data.class.duracio;
                    document.getElementById('editClassRoom').value = data.class.sala;
                    document.getElementById('editClassCapacity').value = data.class.capacitat_maxima;
                } else {
                    alert('Error al cargar los datos de la clase');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
    }
    
    // Función para confirmar eliminación de clase
    function confirmDeleteClass(classId) {
        if (confirm(`¿Está seguro de que desea eliminar la clase #${classId}? Esta acción eliminará todas las reservas asociadas.`)) {
            document.getElementById('deleteClassId').value = classId;
            document.getElementById('deleteClassForm').submit();
        }
    }
    
    // Función para guardar la asistencia
    function saveAttendance() {
        const attendanceSwitches = document.querySelectorAll('.attendance-switch');
        const attendanceData = [];
        
        attendanceSwitches.forEach(switchElement => {
            attendanceData.push({
                reservationId: switchElement.getAttribute('data-reservation-id'),
                attended: switchElement.checked ? 1 : 0
            });
        });
        
        if (attendanceData.length === 0) {
            alert('No hay asistencias para guardar');
            return;
        }
        
        // Enviar datos al servidor
        fetch('<?= URLROOT ?>/staff/updateAttendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ attendance: attendanceData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#viewClassModal').modal('hide');
                
                // Mostrar mensaje de éxito
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.setAttribute('role', 'alert');
                alertDiv.innerHTML = `
                    <strong>¡Éxito!</strong> La asistencia ha sido registrada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.row'));
            } else {
                alert('Error al guardar la asistencia: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        });
    }
    
    // Función para cancelar una reserva
    function cancelReservation(reservationId, classId) {
        if (confirm(`¿Está seguro de que desea cancelar esta reserva?`)) {
            fetch('<?= URLROOT ?>/staff/cancelReservation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ 
                    reservationId: reservationId,
                    classId: classId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recargar la vista de los estudiantes
                    loadClassDetails(classId);
                    
                    // Mostrar mensaje de éxito
                    alert('Reserva cancelada correctamente');
                } else {
                    alert('Error al cancelar la reserva: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
        }
    }
</script>
