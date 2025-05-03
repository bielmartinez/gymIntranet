<?php
/**
 * Vista para el seguimiento de usuarios asignados al personal del gimnasio
 * Permite ver el progreso y evolución de los usuarios asignados
 */
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><?php echo $data['title']; ?></h1>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserTrackingModal">
                <i class="fas fa-user-plus me-2"></i>Asignar nuevo usuario
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
            
            <!-- Filtros de usuarios -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <form class="row g-3" id="userFilter">
                                <div class="col-md-4">
                                    <label for="nameFilter" class="form-label">Nombre/Email</label>
                                    <input type="text" class="form-control" id="nameFilter" name="nameFilter" placeholder="Buscar por nombre o email">
                                </div>
                                <div class="col-md-3">
                                    <label for="progressFilter" class="form-label">Progreso</label>
                                    <select class="form-select" id="progressFilter" name="progressFilter">
                                        <option value="">Todos</option>
                                        <option value="good">Buen progreso</option>
                                        <option value="moderate">Progreso moderado</option>
                                        <option value="poor">Progreso bajo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="activityFilter" class="form-label">Actividad</label>
                                    <select class="form-select" id="activityFilter" name="activityFilter">
                                        <option value="">Todas</option>
                                        <option value="high">Alta</option>
                                        <option value="medium">Media</option>
                                        <option value="low">Baja</option>
                                        <option value="inactive">Inactivo (30+ días)</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                                    <button type="reset" class="btn btn-secondary">Limpiar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Listado de usuarios asignados -->
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Última visita</th>
                            <th>Actividad</th>
                            <th>Progreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se cargarían los usuarios desde la base de datos -->
                        <tr>
                            <td>101</td>
                            <td>Ana García</td>
                            <td>ana@example.com</td>
                            <td>612345678</td>
                            <td>Ayer</td>
                            <td><span class="badge bg-success">Alta</span></td>
                            <td>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewUserTracking(101)">
                                        <i class="fas fa-eye" title="Ver detalles"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProgressModal" onclick="prepareProgressModal(101, 'Ana García')">
                                        <i class="fas fa-plus" title="Añadir progreso"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal" onclick="prepareNoteModal(101, 'Ana García')">
                                        <i class="fas fa-sticky-note" title="Añadir nota"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>102</td>
                            <td>Juan Pérez</td>
                            <td>juan@example.com</td>
                            <td>698765432</td>
                            <td>Hace 5 días</td>
                            <td><span class="badge bg-warning text-dark">Media</span></td>
                            <td>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewUserTracking(102)">
                                        <i class="fas fa-eye" title="Ver detalles"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProgressModal" onclick="prepareProgressModal(102, 'Juan Pérez')">
                                        <i class="fas fa-plus" title="Añadir progreso"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal" onclick="prepareNoteModal(102, 'Juan Pérez')">
                                        <i class="fas fa-sticky-note" title="Añadir nota"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>103</td>
                            <td>María López</td>
                            <td>maria@example.com</td>
                            <td>654321987</td>
                            <td>Hace 32 días</td>
                            <td><span class="badge bg-danger">Inactiva</span></td>
                            <td>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 15%;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewUserTracking(103)">
                                        <i class="fas fa-eye" title="Ver detalles"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProgressModal" onclick="prepareProgressModal(103, 'María López')">
                                        <i class="fas fa-plus" title="Añadir progreso"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal" onclick="prepareNoteModal(103, 'María López')">
                                        <i class="fas fa-sticky-note" title="Añadir nota"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de seguimiento de usuario -->
<div class="modal fade" id="viewUserTrackingModal" tabindex="-1" aria-labelledby="viewUserTrackingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewUserTrackingModalLabel">Seguimiento de Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img src="https://via.placeholder.com/150" class="rounded-circle img-fluid" alt="Foto de perfil">
                                </div>
                                <h5 id="userFullName">Ana García</h5>
                                <p class="text-muted mb-1" id="userEmail">ana@example.com</p>
                                <p class="text-muted mb-3" id="userPhone">612345678</p>
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-outline-primary me-2" onclick="sendNotification()">
                                        <i class="fas fa-bell me-1"></i> Notificar
                                    </button>
                                    <button type="button" class="btn btn-outline-success" onclick="sendRoutine()">
                                        <i class="fas fa-file-pdf me-1"></i> Enviar rutina
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <ul class="nav nav-tabs card-header-tabs" id="userTrackingTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="true">Resumen</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab" aria-controls="progress" aria-selected="false">Progreso</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false">Asistencia</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="false">Notas</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="userTrackingTabContent">
                                    <!-- Pestaña de Resumen -->
                                    <div class="tab-pane fade show active" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="card border-success">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title text-muted">Asistencia último mes</h6>
                                                        <h2 class="card-text text-success">85%</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-info">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title text-muted">Clases reservadas</h6>
                                                        <h2 class="card-text text-info">12</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-warning">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title text-muted">Inicio seguimiento</h6>
                                                        <h2 class="card-text text-warning">3 meses</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="border-bottom pb-2 mb-3">Objetivos actuales</h5>
                                                <ul class="list-group mb-4">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Perder 5 kg
                                                        <span class="badge bg-primary rounded-pill">En progreso</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Mejorar resistencia
                                                        <span class="badge bg-success rounded-pill">Completado</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Reducir % grasa corporal
                                                        <span class="badge bg-warning text-dark rounded-pill">25%</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h5 class="border-bottom pb-2 mb-3">Última actividad</h5>
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h6 class="mb-1">Clase de Spinning</h6>
                                                            <small>Ayer</small>
                                                        </div>
                                                        <small class="text-muted">Asistió y completó la clase completa</small>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h6 class="mb-1">Rutina de pesas</h6>
                                                            <small>Hace 2 días</small>
                                                        </div>
                                                        <small class="text-muted">Completó 85% de la rutina asignada</small>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h6 class="mb-1">Clase de Yoga</h6>
                                                            <small>Hace 4 días</small>
                                                        </div>
                                                        <small class="text-muted">Asistió y completó la clase completa</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pestaña de Progreso -->
                                    <div class="tab-pane fade" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                                        <div class="mb-4">
                                            <h5 class="border-bottom pb-2 mb-3">Evolución de Medidas</h5>
                                            <canvas id="progressChart" width="400" height="200"></canvas>
                                        </div>
                                        
                                        <h5 class="border-bottom pb-2 mb-3">Historial de Medidas</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Peso (kg)</th>
                                                        <th>% Grasa</th>
                                                        <th>IMC</th>
                                                        <th>Cintura (cm)</th>
                                                        <th>Cadera (cm)</th>
                                                        <th>Comentarios</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>01/04/2023</td>
                                                        <td>68.5</td>
                                                        <td>24%</td>
                                                        <td>23.1</td>
                                                        <td>76</td>
                                                        <td>98</td>
                                                        <td>Medidas iniciales</td>
                                                    </tr>
                                                    <tr>
                                                        <td>15/04/2023</td>
                                                        <td>67.2</td>
                                                        <td>23.5%</td>
                                                        <td>22.7</td>
                                                        <td>74</td>
                                                        <td>97</td>
                                                        <td>Buena progresión</td>
                                                    </tr>
                                                    <tr>
                                                        <td>01/05/2023</td>
                                                        <td>65.8</td>
                                                        <td>22.8%</td>
                                                        <td>22.2</td>
                                                        <td>72</td>
                                                        <td>96</td>
                                                        <td>Mejora constante</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Pestaña de Asistencia -->
                                    <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                                        <h5 class="border-bottom pb-2 mb-3">Calendario de Asistencia</h5>
                                        <div id="attendanceCalendar" class="mb-4"></div>
                                        
                                        <h5 class="border-bottom pb-2 mb-3">Clases Recientes</h5>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Clase</th>
                                                        <th>Monitor</th>
                                                        <th>Asistencia</th>
                                                        <th>Comentarios</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>01/05/2023</td>
                                                        <td>Spinning</td>
                                                        <td>Carlos Ruiz</td>
                                                        <td><span class="badge bg-success">Asistió</span></td>
                                                        <td>Buen rendimiento</td>
                                                    </tr>
                                                    <tr>
                                                        <td>29/04/2023</td>
                                                        <td>Yoga</td>
                                                        <td>Laura Martínez</td>
                                                        <td><span class="badge bg-success">Asistió</span></td>
                                                        <td>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>27/04/2023</td>
                                                        <td>Pilates</td>
                                                        <td>Sandra Gómez</td>
                                                        <td><span class="badge bg-danger">No asistió</span></td>
                                                        <td>Canceló por enfermedad</td>
                                                    </tr>
                                                    <tr>
                                                        <td>25/04/2023</td>
                                                        <td>Spinning</td>
                                                        <td>Carlos Ruiz</td>
                                                        <td><span class="badge bg-success">Asistió</span></td>
                                                        <td>-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Pestaña de Notas -->
                                    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="border-bottom pb-2 mb-0">Notas y Observaciones</h5>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                                <i class="fas fa-plus"></i> Añadir Nota
                                            </button>
                                        </div>
                                        
                                        <div class="card mb-3 border-left-primary">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="card-title">Revisión de rutina</h6>
                                                    <small class="text-muted">01/05/2023</small>
                                                </div>
                                                <p class="card-text">Hemos revisado la rutina actual y se han ajustado los pesos para mejorar la progresión. La usuaria muestra una buena adaptación a los ejercicios.</p>
                                                <small class="text-muted">Por: Carlos Ruiz</small>
                                            </div>
                                        </div>
                                        
                                        <div class="card mb-3 border-left-info">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="card-title">Consulta nutricional</h6>
                                                    <small class="text-muted">15/04/2023</small>
                                                </div>
                                                <p class="card-text">La usuaria ha solicitado recomendaciones alimentarias para mejorar el rendimiento. Se le ha proporcionado un plan básico de alimentación para deportistas.</p>
                                                <small class="text-muted">Por: Laura Martínez</small>
                                            </div>
                                        </div>
                                        
                                        <div class="card mb-3 border-left-warning">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="card-title">Objetivos iniciales</h6>
                                                    <small class="text-muted">01/04/2023</small>
                                                </div>
                                                <p class="card-text">Se han establecido los siguientes objetivos: perder 5kg, reducir % grasa corporal, mejorar resistencia cardiovascular. Usuario muy motivado.</p>
                                                <small class="text-muted">Por: Carlos Ruiz</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generateReport()">Generar informe</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para añadir progreso -->
<div class="modal fade" id="addProgressModal" tabindex="-1" aria-labelledby="addProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addProgressModalLabel">Registrar Progreso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="progressForm">
                    <input type="hidden" id="progressUserId" name="progressUserId">
                    <div class="mb-3">
                        <label for="progressUserName" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="progressUserName" name="progressUserName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="progressDate" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="progressDate" name="progressDate" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="weight" class="form-label">Peso (kg)</label>
                            <input type="number" class="form-control" id="weight" name="weight" step="0.1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bodyFat" class="form-label">% Grasa corporal</label>
                            <input type="number" class="form-control" id="bodyFat" name="bodyFat" step="0.1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="waist" class="form-label">Cintura (cm)</label>
                            <input type="number" class="form-control" id="waist" name="waist" step="0.1">
                        </div>
                        <div class="col-md-6">
                            <label for="hip" class="form-label">Cadera (cm)</label>
                            <input type="number" class="form-control" id="hip" name="hip" step="0.1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="progressComments" class="form-label">Comentarios</label>
                        <textarea class="form-control" id="progressComments" name="progressComments" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="saveProgress()">Guardar progreso</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para añadir nota -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="addNoteModalLabel">Añadir Nota</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="noteForm">
                    <input type="hidden" id="noteUserId" name="noteUserId">
                    <div class="mb-3">
                        <label for="noteUserName" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="noteUserName" name="noteUserName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="noteTitle" class="form-label">Título</label>
                        <input type="text" class="form-control" id="noteTitle" name="noteTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="noteContent" class="form-label">Contenido</label>
                        <textarea class="form-control" id="noteContent" name="noteContent" rows="5" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" onclick="saveNote()">Guardar nota</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para asignar nuevo usuario -->
<div class="modal fade" id="addUserTrackingModal" tabindex="-1" aria-labelledby="addUserTrackingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addUserTrackingModalLabel">Asignar Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label for="searchUser" class="form-label">Buscar usuario</label>
                        <input type="text" class="form-control" id="searchUser" name="searchUser" placeholder="Nombre o email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usuarios disponibles</label>
                        <div class="list-group" id="userSearchResults">
                            <a href="#" class="list-group-item list-group-item-action" onclick="selectUserForTracking(201, 'Roberto Sánchez', 'roberto@example.com')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Roberto Sánchez</h6>
                                    <small>ID: 201</small>
                                </div>
                                <small>roberto@example.com</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" onclick="selectUserForTracking(202, 'Elena Rodríguez', 'elena@example.com')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Elena Rodríguez</h6>
                                    <small>ID: 202</small>
                                </div>
                                <small>elena@example.com</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" onclick="selectUserForTracking(203, 'David Martín', 'david@example.com')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">David Martín</h6>
                                    <small>ID: 203</small>
                                </div>
                                <small>david@example.com</small>
                            </a>
                        </div>
                    </div>
                    <div class="mb-3 d-none" id="selectedUserSection">
                        <label class="form-label">Usuario seleccionado</label>
                        <div class="alert alert-info">
                            <h6 id="selectedUserName">-</h6>
                            <small id="selectedUserEmail">-</small>
                            <input type="hidden" id="selectedUserId" name="selectedUserId">
                        </div>
                    </div>
                    <div class="mb-3 d-none" id="initialCommentsSection">
                        <label for="initialComments" class="form-label">Comentarios iniciales</label>
                        <textarea class="form-control" id="initialComments" name="initialComments" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="assignUserBtn" disabled onclick="assignUserForTracking()">Asignar usuario</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar DataTables para la tabla de usuarios
        if (document.getElementById('usersTable')) {
            $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                order: [[5, 'asc']], // Ordenar por actividad (col 5)
                responsive: true
            });
        }
        
        // Inicializar date picker con la fecha actual
        document.getElementById('progressDate').valueAsDate = new Date();
        
        // Inicializar el gráfico de progreso con datos de ejemplo (usando Chart.js)
        if (document.getElementById('progressChart')) {
            const ctx = document.getElementById('progressChart').getContext('2d');
            const progressChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['01/04/2023', '15/04/2023', '01/05/2023'],
                    datasets: [
                        {
                            label: 'Peso (kg)',
                            data: [68.5, 67.2, 65.8],
                            borderColor: 'rgba(0, 123, 255, 1)',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: '% Grasa',
                            data: [24, 23.5, 22.8],
                            borderColor: 'rgba(220, 53, 69, 1)',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Evolución de medidas'
                        }
                    }
                }
            });
        }
    });
    
    // Función para mostrar el modal de seguimiento de usuario
    function viewUserTracking(userId) {
        // Aquí iría el código para cargar los datos del usuario
        // Por ahora simplemente abrimos el modal
        $('#viewUserTrackingModal').modal('show');
    }
    
    // Función para preparar el modal de progreso
    function prepareProgressModal(userId, userName) {
        document.getElementById('progressUserId').value = userId;
        document.getElementById('progressUserName').value = userName;
    }
    
    // Función para preparar el modal de notas
    function prepareNoteModal(userId, userName) {
        document.getElementById('noteUserId').value = userId;
        document.getElementById('noteUserName').value = userName;
    }
    
    // Función para guardar el progreso
    function saveProgress() {
        // Aquí iría el código para enviar los datos al servidor
        $('#addProgressModal').modal('hide');
        
        // Mostrar mensaje de éxito (simulado)
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            <strong>¡Éxito!</strong> El progreso ha sido registrado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.row'));
    }
    
    // Función para guardar la nota
    function saveNote() {
        // Aquí iría el código para enviar los datos al servidor
        $('#addNoteModal').modal('hide');
        
        // Mostrar mensaje de éxito (simulado)
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            <strong>¡Éxito!</strong> La nota ha sido guardada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.row'));
    }
    
    // Función para seleccionar un usuario para seguimiento
    function selectUserForTracking(userId, userName, userEmail) {
        document.getElementById('selectedUserId').value = userId;
        document.getElementById('selectedUserName').textContent = userName;
        document.getElementById('selectedUserEmail').textContent = userEmail;
        
        // Mostrar secciones y habilitar botón
        document.getElementById('selectedUserSection').classList.remove('d-none');
        document.getElementById('initialCommentsSection').classList.remove('d-none');
        document.getElementById('assignUserBtn').disabled = false;
    }
    
    // Función para asignar un usuario para seguimiento
    function assignUserForTracking() {
        // Aquí iría el código para enviar los datos al servidor
        $('#addUserTrackingModal').modal('hide');
        
        // Mostrar mensaje de éxito (simulado)
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            <strong>¡Éxito!</strong> El usuario ha sido asignado correctamente para seguimiento.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.row'));
    }
    
    // Función para generar un informe
    function generateReport() {
        // Aquí iría el código para generar el informe
        alert('Se ha generado un informe para este usuario. Se puede descargar desde el panel de informes.');
    }
    
    // Función para enviar una notificación
    function sendNotification() {
        // Aquí iría el código para mostrar el formulario de notificación
        alert('Función para enviar notificación al usuario.');
    }
    
    // Función para enviar una rutina
    function sendRoutine() {
        // Aquí iría el código para enviar la rutina
        alert('Función para enviar rutina al usuario.');
    }
</script>