<?php
/**
 * Dashboard de administración
 * Muestra estadísticas y accesos rápidos a las distintas secciones de administración
 */
?>

<div class="container mt-4">
    <!-- Accesos rápidos en tarjetas - movidos arriba de todo -->
    <div class="row mb-4">
        <div class="col-12 mb-4">
            <h5 class="border-bottom pb-2">Accesos rápidos</h5>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-user-plus fa-3x text-primary"></i>
                    </div>
                    <h5>Registrar usuario</h5>
                    <p class="small text-muted">Crea nuevos usuarios o personal</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <a href="<?php echo URLROOT; ?>/admin/registerForm" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-users-cog fa-3x text-info"></i>
                    </div>
                    <h5>Gestionar usuarios</h5>
                    <p class="small text-muted">Administra todos los usuarios</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-info text-white">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-dumbbell fa-3x text-success"></i>
                    </div>
                    <h5>Gestionar clases</h5>
                    <p class="small text-muted">Administra las clases y horarios</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <a href="<?php echo URLROOT; ?>/admin/classes" class="btn btn-success">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-bell fa-3x text-warning"></i>
                    </div>
                    <h5>Notificaciones</h5>
                    <p class="small text-muted">Envía notificaciones a los usuarios</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <a href="<?php echo URLROOT; ?>/admin/notifications" class="btn btn-warning text-dark">Acceder</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cabecera del panel -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div>
                            <h1 class="h3 mb-2">Panel de Administración</h1>
                            <p class="mb-0">Bienvenido/a, <?php echo $data['user_name']; ?>. Gestiona todos los aspectos del sistema desde aquí.</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fas fa-tachometer-alt fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Usuarios totales</h6>
                            <h2 class="mb-0"><?php echo isset($data['totalUsers']) ? $data['totalUsers'] : 0; ?></h2>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?php echo URLROOT; ?>/admin/users" class="text-decoration-none">Ver todos <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Usuarios activos</h6>
                            <h2 class="mb-0"><?php echo isset($data['activeUsers']) ? $data['activeUsers'] : 0; ?></h2>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-user-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?php echo URLROOT; ?>/admin/users" class="text-decoration-none">Ver detalles <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Personal</h6>
                            <h2 class="mb-0"><?php echo isset($data['staffUsers']) ? $data['staffUsers'] : 0; ?></h2>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-user-tie fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?php echo URLROOT; ?>/admin/users" class="text-decoration-none">Ver personal <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>