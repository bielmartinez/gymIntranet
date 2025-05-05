<?php
/**
 * Dashboard para el personal del gimnasio
 * Muestra información relevante y accesos rápidos a funciones del personal
 */
?>

<div class="container mt-4">
    <!-- Cabecera del panel -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div>
                            <h1 class="h3 mb-2">Panel de Personal</h1>
                            <p class="mb-0">Bienvenido/a, <?php echo $data['user_name']; ?>. Gestiona tus clases y usuarios desde aquí.</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fas fa-user-tie fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    
    <!-- Accesos rápidos en tarjetas -->
    <div class="row">
        <div class="col-12 mb-4">
            <h5 class="border-bottom pb-2">Herramientas de personal</h5>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-dumbbell fa-3x text-primary"></i>
                    </div>
                    <h5>Gestionar clases</h5>
                    <p class="small text-muted">Crea y administra tus clases</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <a href="<?php echo URLROOT; ?>/staff/classes" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-user-friends fa-3x text-success"></i>
                    </div>
                    <h5>Seguimiento de usuarios</h5>
                    <p class="small text-muted">Administra el progreso de tus usuarios</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <a href="<?php echo URLROOT; ?>/staff/userTracking" class="btn btn-success">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-bell fa-3x text-warning"></i>
                    </div>
                    <h5>Enviar notificación</h5>
                    <p class="small text-muted">Comunícate con tus alumnos</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <a href="<?php echo URLROOT; ?>/staff/sendNotification" class="btn btn-warning text-dark">Acceder</a>
                </div>
            </div>
        </div>
    </div>
</div>