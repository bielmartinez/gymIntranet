<?php
/**
 * Vista para la consulta de usuarios por parte del personal
 * Muestra una lista de todos los usuarios del sistema pero sin opciones de administración
 */
?>

<!-- Incluir estilos específicos para la página de administración de usuarios -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/users.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><?php echo $data['title']; ?></h1>
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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'asc']] // Ordenar por ID de forma ascendente por defecto
    });
});
</script>
