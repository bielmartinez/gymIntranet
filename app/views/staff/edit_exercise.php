<?php
/**
 * Vista para editar un ejercicio existente de una rutina
 */

// Definir el título de la página para el header
$pageTitle = isset($data['title']) ? $data['title'] : 'Editar Ejercicio';

// Incluir el header principal
include_once APPROOT . '/views/shared/header/main.php';
?>

<!-- Incluir estilos específicos para la página de edición de ejercicios -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/staff/edit_exercise.css">

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URLROOT ?>/staffRoutine">Rutinas</a></li>
                    <li class="breadcrumb-item"><a href="<?= URLROOT ?>/staffRoutine/edit/<?= $data['exercise']->rutina_id ?>">Editar Rutina</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Ejercicio</li>
                </ol>
            </nav>
            
            <?php if(isset($_SESSION['routine_message'])): ?>
                <div class="alert alert-<?= $_SESSION['routine_message_type'] ?? 'info' ?> alert-dismissible fade show">
                    <?= $_SESSION['routine_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                    unset($_SESSION['routine_message']);
                    unset($_SESSION['routine_message_type']);
                ?>
            <?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title m-0">Editar Ejercicio</h5>
                </div>
                <div class="card-body">
                    <form action="<?= URLROOT ?>/staffRoutine/editExercise/<?= $data['exercise']->exercici_id ?>" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre del ejercicio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($data['exercise']->nom) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="order" class="form-label">Orden</label>
                                <input type="number" class="form-control" id="order" name="order" value="<?= $data['exercise']->ordre ?>" min="1">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($data['exercise']->descripcio) ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="sets" class="form-label">Series *</label>
                                <input type="number" class="form-control" id="sets" name="sets" min="1" value="<?= $data['exercise']->series ?>" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="reps" class="form-label">Repeticiones *</label>
                                <input type="number" class="form-control" id="reps" name="reps" min="1" value="<?= $data['exercise']->repeticions ?>" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="rest" class="form-label">Descanso (segundos) *</label>
                                <input type="number" class="form-control" id="rest" name="rest" min="0" value="<?= $data['exercise']->descans ?>" required>
                            </div>
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="mb-3">
                            <label for="additional_info" class="form-label">Información adicional</label>
                            <input type="text" class="form-control" id="additional_info" name="additional_info" 
                                value="<?= htmlspecialchars($data['exercise']->info_adicional ?? '') ?>" 
                                placeholder="Ej: Usar mancuernas de 10kg">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= URLROOT ?>/staffRoutine/edit/<?= $data['exercise']->rutina_id ?>" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir scripts específicos para la página de edición de ejercicios -->
<script src="<?= URLROOT ?>/public/js/staff/edit_exercise.js"></script>

<?php
// Incluir el footer
include_once APPROOT . '/views/shared/footer/main.php';
?>