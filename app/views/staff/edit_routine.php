<?php
/**
 * Vista para editar una rutina existente por parte del staff
 */

// Definir el título de la página para el header
$pageTitle = isset($data['title']) ? $data['title'] : 'Editar Rutina';

// Incluir el header principal
include_once APPROOT . '/views/shared/header/main.php';
?>

<style>
    .exercise-card {
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }
    .exercise-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .order-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>

<div class="row">
    <div class="col-12 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><?= $pageTitle ?></h1>
            <div>
                <a href="<?= URLROOT ?>/staffRoutine" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver a rutinas
                </a>
                <a href="<?= URLROOT ?>/staffRoutine/searchExercises/<?= $data['routine']->rutina_id ?>" class="btn btn-success me-2">
                    <i class="fas fa-search me-1"></i> Buscar ejercicios
                </a>
                <a href="<?= URLROOT ?>/staffRoutine/downloadPDF/<?= $data['routine']->rutina_id ?>" class="btn btn-primary">
                    <i class="fas fa-file-pdf me-1"></i> Descargar PDF
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
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Información de la Rutina</h6>
            </div>
            <div class="card-body">
                <form action="<?= URLROOT ?>/staffRoutine/edit/<?= $data['routine']->rutina_id ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nombre de la rutina *</label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                value="<?= htmlspecialchars($data['routine']->nom) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Usuario asignado *</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <?php foreach ($data['users'] as $usuario): ?>
                                    <option value="<?= $usuario->usuari_id ?>" <?= ($usuario->usuari_id == $data['routine']->usuari_id ?? null) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($usuario->nom . ' ' . $usuario->cognoms) ?> (<?= htmlspecialchars($usuario->correu) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($data['routine']->descripcio) ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección de ejercicios -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Ejercicios de la Rutina</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExerciseModal">
                    <i class="fas fa-plus me-1"></i> Añadir ejercicio
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($data['exercises'])): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta rutina aún no tiene ejercicios asignados. Añade ejercicios usando el botón "Añadir ejercicio" o 
                        busca ejercicios en la API con el botón "Buscar ejercicios".
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($data['exercises'] as $exercise): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card exercise-card shadow-sm mb-3 position-relative">
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-primary">#<?= $exercise->ordre ?></span>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($exercise->nom) ?></h5>
                                        
                                        <div class="row text-muted small mb-3">
                                            <div class="col-4 text-center border-end">
                                                <strong><?= $exercise->series ?></strong> series
                                            </div>
                                            <div class="col-4 text-center border-end">
                                                <strong><?= $exercise->repeticions ?></strong> reps
                                            </div>
                                            <div class="col-4 text-center">
                                                <strong><?= $exercise->descans ?></strong> seg
                                            </div>
                                        </div>
                                        
                                        <p class="card-text small text-truncate">
                                            <?= htmlspecialchars($exercise->descripcio) ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                                data-bs-target="#editExerciseModal" 
                                                data-id="<?= $exercise->exercici_id ?>" 
                                                data-routine="<?= $data['routine']->rutina_id ?>" 
                                                data-name="<?= htmlspecialchars($exercise->nom) ?>" 
                                                data-description="<?= htmlspecialchars($exercise->descripcio) ?>" 
                                                data-sets="<?= $exercise->series ?>" 
                                                data-reps="<?= $exercise->repeticions ?>" 
                                                data-rest="<?= $exercise->descans ?>" 
                                                data-order="<?= $exercise->ordre ?>">
                                                <i class="fas fa-edit fa-sm"></i> Editar
                                            </button>
                                            <a href="<?= URLROOT ?>/staffRoutine/deleteExercise/<?= $exercise->exercici_id ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('¿Estás seguro de que quieres eliminar este ejercicio?');">
                                                <i class="fas fa-trash fa-sm"></i> Eliminar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- Modal para añadir ejercicio manualmente -->
<div class="modal fade" id="addExerciseModal" tabindex="-1" aria-labelledby="addExerciseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addExerciseModalLabel">Añadir Ejercicio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= URLROOT ?>/staffRoutine/addExercise/<?= $data['routine']->rutina_id ?>" method="POST">
                    <input type="hidden" name="routine_id" value="<?= $data['routine']->rutina_id ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre del ejercicio *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción/Instrucciones</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="sets" class="form-label">Series *</label>
                            <input type="number" class="form-control" id="sets" name="sets" min="1" value="3" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="reps" class="form-label">Repeticiones *</label>
                            <input type="number" class="form-control" id="reps" name="reps" min="1" value="12" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="rest" class="form-label">Descanso (segundos) *</label>
                            <input type="number" class="form-control" id="rest" name="rest" min="0" value="60" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="order" class="form-label">Orden en la rutina *</label>
                        <input type="number" class="form-control" id="order" name="order" min="1" value="<?= count($data['exercises']) + 1 ?>" required>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Ejercicio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar ejercicio -->
<div class="modal fade" id="editExerciseModal" tabindex="-1" aria-labelledby="editExerciseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editExerciseModalLabel">Editar Ejercicio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editExerciseForm" action="" method="POST">
                    <input type="hidden" id="edit_exercise_id" name="exercise_id">
                    <input type="hidden" name="routine_id" value="<?= $data['routine']->rutina_id ?>">
                    
                    <div class="mb-3">
                        <label for="edit_exercise_name" class="form-label">Nombre del ejercicio *</label>
                        <input type="text" class="form-control" id="edit_exercise_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_exercise_description" class="form-label">Descripción/Instrucciones</label>
                        <textarea class="form-control" id="edit_exercise_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_exercise_sets" class="form-label">Series *</label>
                            <input type="number" class="form-control" id="edit_exercise_sets" name="sets" min="1" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="edit_exercise_reps" class="form-label">Repeticiones *</label>
                            <input type="number" class="form-control" id="edit_exercise_reps" name="reps" min="1" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="edit_exercise_rest" class="form-label">Descanso (segundos) *</label>
                            <input type="number" class="form-control" id="edit_exercise_rest" name="rest" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_exercise_order" class="form-label">Orden en la rutina *</label>
                        <input type="number" class="form-control" id="edit_exercise_order" name="order" min="1" required>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Ejercicio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el modal de edición para cargar datos cuando se abre
        const editExerciseModal = document.getElementById('editExerciseModal');
        if (editExerciseModal) {
            editExerciseModal.addEventListener('show.bs.modal', function(event) {
                // Botón que activó el modal
                const button = event.relatedTarget;
                
                // Extraer información de los atributos data-*
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const description = button.getAttribute('data-description');
                const sets = button.getAttribute('data-sets');
                const reps = button.getAttribute('data-reps');
                const rest = button.getAttribute('data-rest');
                const order = button.getAttribute('data-order');
                
                // Actualizar los campos del formulario
                document.getElementById('edit_exercise_id').value = id;
                document.getElementById('edit_exercise_name').value = name;
                document.getElementById('edit_exercise_description').value = description;
                document.getElementById('edit_exercise_sets').value = sets;
                document.getElementById('edit_exercise_reps').value = reps;
                document.getElementById('edit_exercise_rest').value = rest;
                document.getElementById('edit_exercise_order').value = order;
                
                // Configurar la acción del formulario
                const routineId = <?= $data['routine']->rutina_id ?>;
                document.getElementById('editExerciseForm').action = 
                    `<?= URLROOT ?>/staffRoutine/updateExercise/${id}/${routineId}`;
            });
        }
    });
</script>

<?php
// Incluir el footer
include_once APPROOT . '/views/shared/footer/main.php';
?>