<?php 
// Verificar que el usuario sea administrador
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . URLROOT);
    exit;
}
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Usuarios</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/admin/registerForm" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['register_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['register_message_type']; ?> alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['register_message']; 
                unset($_SESSION['register_message']);
                unset($_SESSION['register_message_type']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Usuarios</h6>
            <div>
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" placeholder="Buscar usuario..." id="userSearch">
                    <button class="btn btn-sm btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Membresía</th>
                            <th>Estado</th>
                            <th>Fecha de Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplo de datos, se reemplazará con datos reales -->
                        <tr>
                            <td>1</td>
                            <td>Juan Pérez</td>
                            <td>juanperez</td>
                            <td>juan@example.com</td>
                            <td><span class="badge bg-primary">Usuario</span></td>
                            <td>Premium</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>10/03/2025</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>María López</td>
                            <td>marialopez</td>
                            <td>maria@example.com</td>
                            <td><span class="badge bg-warning">Staff</span></td>
                            <td>-</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>15/02/2025</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Carlos González</td>
                            <td>carlosg</td>
                            <td>carlos@example.com</td>
                            <td><span class="badge bg-danger">Admin</span></td>
                            <td>-</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>05/01/2025</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <nav aria-label="Navegación de páginas">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Estadísticas de usuarios -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución de Usuarios por Rol</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:250px;">
                        <canvas id="userRoleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Nuevos Registros por Mes</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:250px;">
                        <canvas id="userRegistrationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtrado de usuarios en la tabla
    const userSearch = document.getElementById('userSearch');
    const tableRows = document.querySelectorAll('tbody tr');
    
    userSearch.addEventListener('keyup', function() {
        const searchTerm = userSearch.value.toLowerCase();
        
        tableRows.forEach(row => {
            const userName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const userLogin = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const userEmail = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            
            if(userName.includes(searchTerm) || userLogin.includes(searchTerm) || userEmail.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Configurar gráficos de ejemplo
    if(typeof Chart !== 'undefined') {
        // Gráfico de roles
        const roleCtx = document.getElementById('userRoleChart').getContext('2d');
        const roleChart = new Chart(roleCtx, {
            type: 'pie',
            data: {
                labels: ['Usuarios', 'Personal', 'Administradores'],
                datasets: [{
                    data: [65, 25, 10],
                    backgroundColor: ['#150000', '#f2f6f8', '#454ade'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#e02d1b'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Gráfico de registros
        const regCtx = document.getElementById('userRegistrationChart').getContext('2d');
        const regChart = new Chart(regCtx, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Nuevos Usuarios',
                    backgroundColor: '#36b9cc',
                    hoverBackgroundColor: '#2c9faf',
                    borderColor: '#36b9cc',
                    data: [10, 15, 8, 12, 17, 6],
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
