<?php
/**
 * Vista para buscar ejercicios y añadirlos a una rutina
 * Versión mejorada con interfaz moderna y filtros avanzados
 */

// Definir el título de la página para el header
$pageTitle = isset($data['title']) ? $data['title'] : 'Buscar Ejercicios';

// Incluir el header principal
include_once APPROOT . '/views/shared/header/main.php';
?>

<!-- Incluir estilos específicos para la página de búsqueda de ejercicios -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/staff/search_exercises.css">

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2"><i class="fas fa-search me-2"></i><?= $pageTitle ?></h1>
            <p class="text-muted">Encuentra y añade ejercicios a la rutina <strong><?= htmlspecialchars($data['routine']->nom) ?></strong></p>
        </div>
        <div>
            <a href="<?= URLROOT ?>/staffRoutine/edit/<?= $data['routine']->rutina_id ?>" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Volver a la rutina
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['toast_message'])): ?>
        <div class="alert alert-<?= $_SESSION['toast_type'] ?> alert-dismissible fade show">
            <?= $_SESSION['toast_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['toast_message']);
            unset($_SESSION['toast_type']);
        ?>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form action="<?= URLROOT ?>/staffRoutine/searchExercises/<?= $data['routine']->rutina_id ?>" method="POST" class="filters-container" id="searchForm">                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="search_term" name="search_term" 
                                value="<?= htmlspecialchars($data['searchTerm'] ?? '') ?>" 
                                placeholder="Buscar ejercicios...">
                            <label for="search_term"><i class="fas fa-search me-2"></i>Buscar ejercicios por nombre</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="fas fa-search me-1"></i> Buscar Ejercicios
                        </button>
                    </div>
                </div><!-- Selector de grupos musculares con etiquetas -->
                <div class="filter-group">
                    <h6 class="filter-title"><i class="fas fa-dumbbell me-2"></i>Grupo Muscular:</h6>                    <div class="muscle-labels">
                        <span class="muscle-label" data-muscle="abdominales"><i class="fas fa-dot-circle me-1"></i>Abdominales</span>
                        <span class="muscle-label" data-muscle="abductores"><i class="fas fa-dot-circle me-1"></i>Abductores</span>
                        <span class="muscle-label" data-muscle="aductores"><i class="fas fa-dot-circle me-1"></i>Aductores</span>
                        <span class="muscle-label" data-muscle="biceps"><i class="fas fa-dot-circle me-1"></i>Biceps</span>
                        <span class="muscle-label" data-muscle="pantorrillas"><i class="fas fa-dot-circle me-1"></i>Pantorrillas</span>
                        <span class="muscle-label" data-muscle="pecho"><i class="fas fa-dot-circle me-1"></i>Pecho</span>
                        <span class="muscle-label" data-muscle="antebrazos"><i class="fas fa-dot-circle me-1"></i>Antebrazos</span>
                        <span class="muscle-label" data-muscle="gluteos"><i class="fas fa-dot-circle me-1"></i>Glúteos</span>
                        <span class="muscle-label" data-muscle="isquiotibiales"><i class="fas fa-dot-circle me-1"></i>Isquiotibiales</span>
                        <span class="muscle-label" data-muscle="dorsales"><i class="fas fa-dot-circle me-1"></i>Dorsales</span>
                        <span class="muscle-label" data-muscle="lumbar"><i class="fas fa-dot-circle me-1"></i>Lumbar</span>
                        <span class="muscle-label" data-muscle="espalda_media"><i class="fas fa-dot-circle me-1"></i>Espalda Media</span>
                        <span class="muscle-label" data-muscle="cuello"><i class="fas fa-dot-circle me-1"></i>Cuello</span>
                        <span class="muscle-label" data-muscle="cuadriceps"><i class="fas fa-dot-circle me-1"></i>Cuádriceps</span>
                        <span class="muscle-label" data-muscle="trapecios"><i class="fas fa-dot-circle me-1"></i>Trapecios</span>
                        <span class="muscle-label" data-muscle="triceps"><i class="fas fa-dot-circle me-1"></i>Tríceps</span>
                    </div>
                    
                    <div id="selectedMuscleLabels" class="mt-2">
                        <span class="text-muted small">Ningún grupo muscular seleccionado</span>
                    </div>
                    <!-- Campo hidden para almacenar el músculo seleccionado -->
                    <input type="hidden" id="muscles" name="muscles[]" value="<?= htmlspecialchars(implode(',', $data['muscles'] ?? [])) ?>">
                </div>
            </form>

            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <?php if (empty($data['exercises'])): ?>
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 fa-lg"></i>
                        <div>
                            No se encontraron ejercicios que coincidan con tu búsqueda. Prueba con otros términos o filtros menos restrictivos.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="results-count">
                        <i class="fas fa-dumbbell me-1"></i> Se encontraron <?= count($data['exercises']) ?> ejercicios
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($data['exercises'])): ?>
                <div class="row">
                    <?php foreach($data['exercises'] as $index => $exercise): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card exercise-card h-100">
                                <?php if(!empty($exercise->difficulty)): ?>
                                    <span class="difficulty-badge badge <?= getBadgeClass($exercise->difficulty) ?>">
                                        <?= htmlspecialchars(ucfirst($exercise->difficulty)) ?>
                                    </span>
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-primary"><?= htmlspecialchars($exercise->name) ?></h5>
                                    <div class="card-body-scrollable mb-3 flex-grow-1">
                                        <p class="card-text small text-muted"><?= nl2br(htmlspecialchars($exercise->description)) ?></p>
                                    </div>
                                    
                                    <?php if (!empty($exercise->muscles)): ?>
                                        <div class="mb-3">
                                            <?php 
                                            $muscles = explode(',', $exercise->muscles);
                                            foreach($muscles as $muscle): 
                                                $muscle = trim($muscle);
                                                if(!empty($muscle)):
                                            ?>
                                                <span class="muscle-tag">
                                                    <i class="fas fa-dot-circle me-1"></i><?= htmlspecialchars(ucfirst($muscle)) ?>
                                                </span>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-auto">                                        <button type="button" class="btn btn-outline-primary add-exercise-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addExerciseModal"
                                                data-name="<?= htmlspecialchars($exercise->name) ?>"
                                                data-description="<?= htmlspecialchars($exercise->description) ?>"
                                                data-muscle="<?= htmlspecialchars($exercise->muscles) ?>"
                                                data-difficulty="<?= htmlspecialchars($exercise->difficulty) ?>"
                                                data-equipment="<?= htmlspecialchars($exercise->equipment ?? '') ?>"
                                                data-type="<?= htmlspecialchars($exercise->type ?? '') ?>"
                                                data-index="<?= $index ?>">
                                            <i class="fas fa-plus-circle me-1"></i> Añadir a la rutina
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!$_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-search fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted">Utiliza los filtros para buscar ejercicios</h4>
                    <p class="text-muted">Puedes buscar por nombre o grupo muscular</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para añadir ejercicio a la rutina mejorado -->
<div class="modal fade" id="addExerciseModal" tabindex="-1" aria-labelledby="addExerciseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="addExerciseModalLabel"><i class="fas fa-plus-circle me-2"></i>Añadir Ejercicio a la Rutina</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= URLROOT ?>/staffRoutine/addExercise" method="POST">
                <input type="hidden" name="routine_id" value="<?= $data['routine']->rutina_id ?>">
                  <div class="modal-body">
                    <!-- Campo oculto para datos adicionales de API -->
                    <input type="hidden" name="api_data" id="api_data" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3"><label for="exercise_name" class="form-label fw-bold"><i class="fas fa-tag me-1"></i>Nombre del ejercicio *</label>
                                <input type="text" class="form-control" id="exercise_name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="exercise_description" class="form-label fw-bold"><i class="fas fa-align-left me-1"></i>Descripción/Instrucciones</label>
                                <textarea class="form-control" id="exercise_description" name="description" rows="5"></textarea>
                                <div class="form-text">Describe cómo realizar correctamente el ejercicio</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="exercise_sets" class="form-label fw-bold"><i class="fas fa-layer-group me-1"></i>Series *</label>
                                    <input type="number" class="form-control" id="exercise_sets" name="sets" min="1" value="3" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="exercise_reps" class="form-label fw-bold"><i class="fas fa-redo me-1"></i>Repeticiones *</label>
                                    <input type="number" class="form-control" id="exercise_reps" name="reps" min="1" value="12" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="exercise_rest" class="form-label fw-bold"><i class="fas fa-hourglass-half me-1"></i>Descanso (seg) *</label>
                                    <input type="number" class="form-control" id="exercise_rest" name="rest" min="0" value="60" required>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label for="exercise_order" class="form-label fw-bold"><i class="fas fa-sort-numeric-down me-1"></i>Orden en la rutina *</label>
                                <input type="number" class="form-control" id="exercise_order" name="order" min="1" value="1" required>
                                <div class="form-text">Posición del ejercicio en la rutina</div>
                            </div>

                            <div class="mt-3">
                                <label for="additional_info" class="form-label fw-bold"><i class="fas fa-info-circle me-1"></i>Información adicional</label>
                                <input type="text" class="form-control" id="additional_info" name="additional_info" placeholder="Ej: Usar mancuernas de 10kg">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer justify-content-between">
                    <div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="add_more" name="add_more" value="1">
                            <label class="form-check-label" for="add_more">Añadir otro ejercicio después</label>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Añadir a la rutina
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Función helper para determinar la clase CSS del badge según dificultad
function getBadgeClass($difficulty) {
    switch(strtolower($difficulty)) {
        case 'principiante':
            return 'bg-success';
        case 'intermedio':
            return 'bg-warning text-dark';
        case 'avanzado':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
?>

<!-- Incluir el script de búsqueda de ejercicios -->
<script src="<?= URLROOT ?>/public/js/staff/search_exercises.js"></script>