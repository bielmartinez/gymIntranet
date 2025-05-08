<?php
/**
 * Dashboard para el personal del gimnasio
 * Muestra información relevante y accesos rápidos a funciones del personal
 */
?>

<div class="container-fluid">
    <div class="row">
        <main class="col-12 px-md-4">
            <!-- Accesos Rápidos -->
            <div class="col-12 mt-3 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Accesos Rápidos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-4">
                                <a href="<?php echo URLROOT; ?>/admin/classes" class="text-decoration-none">
                                    <div class="card bg-primary text-white shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-dumbbell fa-3x mb-3"></i>
                                            <h5>Gestionar Clases</h5>
                                            <div class="text-white-50">Administra tus clases</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <a href="<?php echo URLROOT; ?>/staff/userTracking" class="text-decoration-none">
                                    <div class="card bg-success text-white shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-user-friends fa-3x mb-3"></i>
                                            <h5>Seguimiento</h5>
                                            <div class="text-white-50">Progreso de usuarios</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-12 mb-4">
                                <a href="<?php echo URLROOT; ?>/staff/sendNotification" class="text-decoration-none">
                                    <div class="card bg-info text-white shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-bell fa-3x mb-3"></i>
                                            <h5>Notificaciones</h5>
                                            <div class="text-white-50">Mensajes a usuarios</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Bienvenido/a, <?php echo $data['user_name']; ?></h1>
            </div>
    
    <!-- Mensajes de estado -->
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
      <!-- Stats at a glance -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tus próximas clases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['staff_classes']) ? count($data['staff_classes']) : '0'; ?>
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Próxima Clase</div>
                            <?php if (isset($data['staff_classes']) && count($data['staff_classes']) > 0) : ?>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php 
                                    $nextClass = $data['staff_classes'][0];
                                    echo date('d/m/Y', strtotime($nextClass->data)) . ', ' . date('H:i', strtotime($nextClass->hora));
                                    ?>
                                </div>
                                <small class="text-muted"><?php echo $nextClass->tipus_nom; ?> - Sala <?php echo $nextClass->sala; ?></small>
                            <?php else : ?>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">No tienes clases próximas</div>
                                <small class="text-muted">Revisa tu agenda</small>
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
    
    <!-- Próximas clases del monitor -->
    <?php if (isset($data['staff_classes']) && count($data['staff_classes']) > 0) : ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Tus Próximas Clases (próximos 3 días)</h6>
                        <a href="<?php echo URLROOT; ?>/admin/classes" class="btn btn-sm btn-primary">Ver Todas</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Actividad</th>
                                        <th>Día</th>
                                        <th>Hora</th>
                                        <th>Sala</th>
                                        <th>Capacidad</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['staff_classes'] as $class) : ?>
                                        <tr>
                                            <td><span class="font-weight-bold"><?php echo $class->tipus_nom; ?></span></td>
                                            <td><?php echo date('d/m/Y', strtotime($class->data)); ?></td>
                                            <td><?php echo date('H:i', strtotime($class->hora)) . ' - ' . date('H:i', strtotime($class->hora) + ($class->duracio * 60)); ?></td>
                                            <td><?php echo $class->sala; ?></td>
                                            <td><?php echo $class->capacitat_actual . '/' . $class->capacitat_maxima; ?></td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/admin/classes" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i> Ver detalles
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>
</div>
</div>