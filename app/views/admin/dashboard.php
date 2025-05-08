<?php
/**
 * Dashboard de administración
 * Muestra estadísticas y accesos rápidos a las distintas secciones de administración
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
                            <div class="col-lg-3 col-md-6 mb-4">
                                <a href="<?php echo URLROOT; ?>/admin/registerForm" class="text-decoration-none">
                                    <div class="card bg-primary text-white shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-user-plus fa-3x mb-3"></i>
                                            <h5>Registrar Usuario</h5>
                                            <div class="text-white-50">Crear nuevas cuentas</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <a href="<?php echo URLROOT; ?>/admin/users" class="text-decoration-none">
                                    <div class="card bg-info text-white shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-users-cog fa-3x mb-3"></i>
                                            <h5>Gestionar Usuarios</h5>
                                            <div class="text-white-50">Administrar cuentas</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">                                <a href="<?php echo URLROOT; ?>/admin/classes" class="text-decoration-none">
                                    <div class="card bg-success text-white shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-dumbbell fa-3x mb-3"></i>
                                            <h5>Gestionar Clases</h5>
                                            <div class="text-white-50">Horarios y actividades</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <a href="<?php echo URLROOT; ?>/admin/notifications" class="text-decoration-none">
                                    <div class="card bg-warning text-white shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-bell fa-3x mb-3"></i>
                                            <h5>Notificaciones</h5>
                                            <div class="text-white-50">Mensajes y avisos</div>
                                        </div>
                                    </div>
                                </a>
                            </div>                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Bienvenido/a, <?php echo isset($data['user_name']) ? $data['user_name'] : 'Administrador'; ?></h1>
            </div>
      <!-- Stats at a glance -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Usuarios totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['totalUsers']) ? $data['totalUsers'] : 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
          <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Usuarios registrados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['activeUsers']) ? $data['activeUsers'] : 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Personal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['staffUsers']) ? $data['staffUsers'] : 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Clases Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['todayClasses']) ? $data['todayClasses'] : 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>                </div>
            </div>
        </div>
    </div>
    
</main>
</div>
</div>