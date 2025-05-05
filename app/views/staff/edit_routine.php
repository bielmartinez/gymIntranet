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
    .exercise-image {
        height: 200px;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        background-color: #f8f9fa;
    }
    .exercise-icon {
        height: 150px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fa;
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
                <?php if (!empty($data['routine']->ruta_pdf)): ?>
                    <a href="<?= URLROOT ?>/staffRoutine/downloadPDF/<?= $data['routine']->rutina_id ?>" class="btn btn-primary me-2">
                        <i class="fas fa-download me-1"></i> Descargar PDF
                    </a>
                <?php else: ?>
                    <a href="<?= URLROOT ?>/staffRoutine/generatePDF/<?= $data['routine']->rutina_id ?>" class="btn btn-warning">
                        <i class="fas fa-file-pdf me-1"></i> Generar PDF
                    </a>
                <?php endif; ?>
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
                <span class="badge <?= !empty($data['routine']->ruta_pdf) ? 'bg-success' : 'bg-danger' ?>">
                    <?= !empty($data['routine']->ruta_pdf) ? 'PDF Generado' : 'Sin PDF' ?>
                </span>
            </div>
            <div class="card-body">
                <form action="<?= URLROOT ?>/staffRoutine/edit/<?= $data['routine']->rutina_id ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre de la rutina *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                value="<?= htmlspecialchars($data['routine']->nom) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="usuario_id" class="form-label">Usuario asignado *</label>
                            <select class="form-select" id="usuario_id" name="usuario_id" required>
                                <?php foreach ($data['usuarios'] as $usuario): ?>
                                    <option value="<?= $usuario->id ?>" <?= $usuario->id == $data['routine']->usuari_id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($usuario->fullName) ?> (<?= htmlspecialchars($usuario->email) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($data['routine']->descripcio) ?></textarea>
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
                                <div class="card exercise-card position-relative">
                                    <div class="order-badge"><?= $exercise->ordre ?></div>
                                    
                                    <?php if (!empty($exercise->imatge_url)): ?>
                                        <div class="exercise-image" style="background-image: url('<?= htmlspecialchars($exercise->imatge_url) ?>')"></div>
                                    <?php else: ?>
                                        <div class="exercise-icon">
                                            <i class="fas fa-dumbbell fa-4x text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($exercise->nom) ?></h5>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="badge bg-primary">Series: <?= $exercise->series ?></span>
                                            <span class="badge bg-info">Reps: <?= $exercise->repeticions ?></span>
                                            <span class="badge bg-secondary">Descanso: <?= $exercise->descans ?>s</span>
                                        </div>
                                        <p class="card-text small">
                                            <?= nl2br(htmlspecialchars($exercise->descripcio)) ?>
                                        </p>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-exercise-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editExerciseModal"
                                                    data-id="<?= $exercise->exercici_id ?>"
                                                    data-name="<?= htmlspecialchars($exercise->nom) ?>"
                                                    data-description="<?= htmlspecialchars($exercise->descripcio) ?>"
                                                    data-series="<?= $exercise->series ?>"
                                                    data-reps="<?= $exercise->repeticions ?>"
                                                    data-rest="<?= $exercise->descans ?>"
                                                    data-image="<?= htmlspecialchars($exercise->imatge_url) ?>"
                                                    data-order="<?= $exercise->ordre ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteExerciseModal<?= $exercise->exercici_id ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modal de confirmación de eliminación -->
                                <div class="modal fade" id="deleteExerciseModal<?= $exercise->exercici_id ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Confirmar eliminación</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Estás seguro de que deseas eliminar el ejercicio <strong><?= htmlspecialchars($exercise->nom) ?></strong>?</p>
                                                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <a href="<?= URLROOT ?>/staffRoutine/deleteExercise/<?= $exercise->exercici_id ?>" class="btn btn-danger">Eliminar</a>
                                            </div>
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
                    <div class="mb-3">
                        <label for="exercise_name" class="form-label">Nombre del ejercicio *</label>
                        <input type="text" class="form-control" id="exercise_name" name="exercise_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exercise_description" class="form-label">Descripción/Instrucciones</label>
                        <textarea class="form-control" id="exercise_description" name="exercise_description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="exercise_sets" class="form-label">Series *</label>
                            <input type="number" class="form-control" id="exercise_sets" name="exercise_sets" min="1" value="3" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="exercise_reps" class="form-label">Repeticiones *</label>
                            <input type="number" class="form-control" id="exercise_reps" name="exercise_reps" min="1" value="12" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="exercise_rest" class="form-label">Descanso (segundos) *</label>
                            <input type="number" class="form-control" id="exercise_rest" name="exercise_rest" min="0" value="60" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exercise_image" class="form-label">URL de imagen (opcional)</label>
                        <input type="url" class="form-control" id="exercise_image" name="exercise_image" placeholder="https://ejemplo.com/imagen.jpg">
                    </div>
                    
                    <div class="mb-3">
                        <label for="exercise_order" class="form-label">Orden en la rutina *</label>
                        <input type="number" class="form-control" id="exercise_order" name="exercise_order" min="1" value="<?= count($data['exercises']) + 1 ?>" required>
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
                    
                    <div class="mb-3">
                        <label for="edit_exercise_name" class="form-label">Nombre del ejercicio *</label>
                        <input type="text" class="form-control" id="edit_exercise_name" name="exercise_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_exercise_description" class="form-label">Descripción/Instrucciones</label>
                        <textarea class="form-control" id="edit_exercise_description" name="exercise_description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_exercise_sets" class="form-label">Series *</label>
                            <input type="number" class="form-control" id="edit_exercise_sets" name="exercise_sets" min="1" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="edit_exercise_reps" class="form-label">Repeticiones *</label>
                            <input type="number" class="form-control" id="edit_exercise_reps" name="exercise_reps" min="1" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="edit_exercise_rest" class="form-label">Descanso (segundos) *</label>
                            <input type="number" class="form-control" id="edit_exercise_rest" name="exercise_rest" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_exercise_image" class="form-label">URL de imagen (opcional)</label>
                        <input type="url" class="form-control" id="edit_exercise_image" name="exercise_image">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_exercise_order" class="form-label">Orden en la rutina *</label>
                        <input type="number" class="form-control" id="edit_exercise_order" name="exercise_order" min="1" required>
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
        // Configurar los botones de edición
        const editButtons = document.querySelectorAll('.edit-exercise-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');
                const series = this.getAttribute('data-series');
                const reps = this.getAttribute('data-reps');
                const rest = this.getAttribute('data-rest');
                const image = this.getAttribute('data-image');
                const order = this.getAttribute('data-order');
                
                document.getElementById('edit_exercise_id').value = id;
                document.getElementById('edit_exercise_name').value = name;
                document.getElementById('edit_exercise_description').value = description;
                document.getElementById('edit_exercise_sets').value = series;
                document.getElementById('edit_exercise_reps').value = reps;
                document.getElementById('edit_exercise_rest').value = rest;
                document.getElementById('edit_exercise_image').value = image;
                document.getElementById('edit_exercise_order').value = order;
                
                // Configurar la acción del formulario
                const routineId = <?= $data['routine']->rutina_id ?>;
                document.getElementById('editExerciseForm').action = 
                    `<?= URLROOT ?>/staffRoutine/updateExercise/${id}`;
            });
        });
    });
</script>

<?php
// Incluir el footer
include_once APPROOT . '/views/shared/footer/main.php';
?>