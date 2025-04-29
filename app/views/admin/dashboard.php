<?php
/**
 * Dashboard para administradores
 */
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Panel de Administración</h1>
            <p class="lead">Bienvenido/a, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Administrador'; ?></p>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Usuarios</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">215</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Clases Activas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">24</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Reservas Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">42</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Notificaciones</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <a href="<?php echo URLROOT; ?>/admin/users" class="text-decoration-none">
                                <div class="card bg-primary text-white shadow">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users-cog fa-3x mb-3"></i>
                                        <h5>Gestionar Usuarios</h5>
                                        <div class="text-white-50">Administrar usuarios</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <a href="<?php echo URLROOT; ?>/admin/registerForm" class="text-decoration-none">
                                <div class="card bg-success text-white shadow">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-plus fa-3x mb-3"></i>
                                        <h5>Añadir Usuario</h5>
                                        <div class="text-white-50">Crear nuevo usuario</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <a href="<?php echo URLROOT; ?>/admin/classes" class="text-decoration-none">
                                <div class="card bg-info text-white shadow">
                                    <div class="card-body text-center">
                                        <i class="fas fa-dumbbell fa-3x mb-3"></i>
                                        <h5>Gestionar Clases</h5>
                                        <div class="text-white-50">Administrar clases</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <a href="<?php echo URLROOT; ?>/admin/courts" class="text-decoration-none">
                                <div class="card bg-warning text-white shadow">
                                    <div class="card-body text-center">
                                        <i class="fas fa-volleyball-ball fa-3x mb-3"></i>
                                        <h5>Gestionar Pistas</h5>
                                        <div class="text-white-50">Administrar pistas</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos usuarios registrados -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Últimos Usuarios Registrados</h6>
                    <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-sm btn-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Fecha</th>
                                    <th>Rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Admin</td>
                                    <td>admin@admin.com</td>
                                    <td>28/04/2025</td>
                                    <td><span class="badge bg-danger">Admin</span></td>
                                </tr>
                                <tr>
                                    <td>Bruno Martínez</td>
                                    <td>b.martinez@sapalomera.cat</td>
                                    <td>28/04/2025</td>
                                    <td><span class="badge bg-danger">Admin</span></td>
                                </tr>
                                <tr>
                                    <td>Laura García</td>
                                    <td>laura@example.com</td>
                                    <td>26/04/2025</td>
                                    <td><span class="badge bg-success">Usuario</span></td>
                                </tr>
                                <tr>
                                    <td>Carlos Pérez</td>
                                    <td>carlos@example.com</td>
                                    <td>25/04/2025</td>
                                    <td><span class="badge bg-info">Staff</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximas clases -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Próximas Clases</h6>
                    <a href="<?php echo URLROOT; ?>/admin/classes" class="btn btn-sm btn-primary">Ver Todas</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Clase</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Inscritos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Spinning</td>
                                    <td>Hoy</td>
                                    <td>18:30 - 19:30</td>
                                    <td>12/15</td>
                                </tr>
                                <tr>
                                    <td>Yoga</td>
                                    <td>Hoy</td>
                                    <td>20:00 - 21:00</td>
                                    <td>8/12</td>
                                </tr>
                                <tr>
                                    <td>Pilates</td>
                                    <td>Mañana</td>
                                    <td>9:00 - 10:00</td>
                                    <td>10/15</td>
                                </tr>
                                <tr>
                                    <td>Zumba</td>
                                    <td>Mañana</td>
                                    <td>18:00 - 19:00</td>
                                    <td>18/20</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>