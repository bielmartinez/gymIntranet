<?php
/**
 * Vista para la gestión de usuarios
 * Muestra una lista de todos los usuarios del sistema con opciones para administrarlos
 */
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><?php echo $data['title']; ?></h1>
            <a href="<?php echo URLROOT; ?>/admin/registerForm" class="btn btn-light">
                <i class="fas fa-plus-circle me-2"></i>Nuevo usuario
            </a>
        </div>
        <div class="card-body">
            <?php if(isset($_SESSION['admin_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['admin_message_type']; ?> alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['admin_message']; 
                        unset($_SESSION['admin_message']);
                        unset($_SESSION['admin_message_type']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if(empty($data['users'])): ?>
                <div class="alert alert-info">
                    No hay usuarios registrados en el sistema.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Teléfono</th>
                                <th>Fecha registro</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['users'] as $user): 
                                // Verificar si es un objeto o un array
                                $id = is_object($user) ? $user->id : $user['id'];
                                $fullName = is_object($user) ? $user->fullName : $user['fullName'];
                                $email = is_object($user) ? $user->email : $user['email'];
                                $role = is_object($user) ? $user->role : $user['role'];
                                $phone = is_object($user) ? ($user->phone ?? 'No registrado') : ($user['phone'] ?? 'No registrado');
                                $createdAt = is_object($user) ? $user->createdAt : $user['createdAt'];
                                $isActive = is_object($user) ? $user->isActive : $user['isActive'];
                            ?>
                                <tr>
                                    <td><?php echo $id; ?></td>
                                    <td><?php echo $fullName; ?></td>
                                    <td><?php echo $email; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $role === 'admin' ? 'danger' : 
                                                ($role === 'staff' ? 'warning' : 'info'); 
                                        ?>">
                                            <?php echo ucfirst($role); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $phone; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($createdAt)); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $isActive ? 'success' : 'secondary'; ?>">
                                            <?php echo $isActive ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if($isActive): ?>
                                            <!-- Si el usuario está activo, mostrar botón de eliminar -->
                                            <a href="<?php echo URLROOT; ?>/admin/deleteUser/<?php echo $id; ?>" 
                                                class="btn btn-outline-danger btn-sm" title="Eliminar"
                                                onclick="return confirm('¿Está seguro de que desea desactivar este usuario? El usuario perderá acceso al sistema pero podrá ser reactivado más adelante.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php else: ?>
                                            <!-- Si el usuario está inactivo, mostrar botón de reactivar -->
                                            <a href="<?php echo URLROOT; ?>/admin/reactivateUser/<?php echo $id; ?>" 
                                                class="btn btn-outline-success btn-sm" title="Reactivar"
                                                onclick="return confirm('¿Está seguro de que desea reactivar este usuario?')">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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
                order: [[0, 'desc']], // Ordenar por ID (columna 0) descendente por defecto
                responsive: true
            });
        }
    });
</script>
