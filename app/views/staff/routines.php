<?php
/**
 * Vista principal de rutinas para staff - Lista todas las rutinas
 */

// Definir el título de la página para el header
$pageTitle = isset($data['title']) ? $data['title'] : 'Gestión de Rutinas';

// Incluir el header principal
include_once APPROOT . '/views/shared/header/main.php';
?>

<!-- Incluir estilos específicos para la página de rutinas -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/staff/routines.css">

<div class="container-fluid">
    <div class="row">
        <div class="col-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?= $pageTitle ?></h1>
                <div>
                    <a href="<?= URLROOT ?>/staffRoutine/create" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Nueva Rutina
                    </a>
                </div>
            </div>

            <?php if(isset($_SESSION['routine_message'])): ?>
                <div class="alert alert-<?= $_SESSION['routine_message_type'] ?> alert-dismissible fade show">
                    <?= $_SESSION['routine_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                    unset($_SESSION['routine_message']);
                    unset($_SESSION['routine_message_type']);
                ?>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Rutinas Personalizadas</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opciones:</div>
                            <a class="dropdown-item" href="<?= URLROOT ?>/staffRoutine/create">
                                <i class="fas fa-plus-circle fa-sm fa-fw me-2 text-gray-400"></i> Nueva Rutina
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(empty($data['routines'])): ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay rutinas creadas aún. ¡Crea la primera rutina usando el botón "Nueva Rutina"!
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="routinesTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Usuario</th>
                                        <th>Ejercicios</th>
                                        <th>PDF</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['routines'] as $routine): ?>
                                        <tr>
                                            <td><?= $routine->rutina_id ?></td>
                                            <td><?= htmlspecialchars($routine->nom) ?></td>
                                            <td>
                                                <?php 
                                                // Buscar el nombre de usuario
                                                $userName = 'Usuario no encontrado';
                                                foreach($data['users'] as $user) {
                                                    if($user->id == $routine->usuari_id) {
                                                        $userName = htmlspecialchars($user->fullName);
                                                        break;
                                                    }
                                                }
                                                echo $userName;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                // Contar el número de ejercicios (esto debería optimizarse para no hacer queries en un bucle)
                                                $exerciseCount = $routine->exercise_count ?? 0;
                                                echo '<span class="badge bg-primary rounded-pill">' . $exerciseCount . ' ejercicios</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?= URLROOT ?>/staffRoutine/downloadPDF/<?= $routine->rutina_id ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </a>
                                            </td>
                                            <td>                                                <div class="btn-group">
                                                    <a href="<?= URLROOT ?>/staffRoutine/edit/<?= $routine->rutina_id ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger open-delete-modal" data-modal-id="deleteModal<?= $routine->rutina_id ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Modal de confirmación de eliminación -->
                                                <div class="modal fade" id="deleteModal<?= $routine->rutina_id ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $routine->rutina_id ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title" id="deleteModalLabel<?= $routine->rutina_id ?>">Confirmar eliminación</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Estás seguro que deseas eliminar la rutina <strong><?= htmlspecialchars($routine->nom) ?></strong>?</p>
                                                                <p>Esta acción también eliminará todos los ejercicios asociados y no se puede deshacer.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <a href="<?= URLROOT ?>/staffRoutine/delete/<?= $routine->rutina_id ?>" class="btn btn-danger">Eliminar</a>
                                                            </div>
                                                        </div>
                                                    </div>
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
    </div>
</div>

<!-- Incluir scripts específicos para la página de rutinas -->
<script src="<?= URLROOT ?>/public/js/staff/routines.js"></script>

<?php
// Incluir el footer
include_once APPROOT . '/views/shared/footer/main.php';
?>