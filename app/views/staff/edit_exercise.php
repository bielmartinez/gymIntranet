<?php
/**
 * Vista para editar un ejercicio existente de una rutina
 */
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URLROOT ?>/staffRoutine">Rutinas</a></li>
                    <li class="breadcrumb-item"><a href="<?= URLROOT ?>/staffRoutine/edit/<?= $data['routine']->rutina_id ?>">Editar Rutina</a></li>
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
                                <label for="exercise_name" class="form-label">Nombre del ejercicio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="exercise_name" name="exercise_name" value="<?= htmlspecialchars($data['exercise']->nom) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="exercise_order" class="form-label">Orden</label>
                                <input type="number" class="form-control" id="exercise_order" name="exercise_order" value="<?= $data['exercise']->ordre ?>" min="1">
                            </div>
                            <div class="col-md-3">
                                <label for="exercise_image" class="form-label">URL de imagen</label>
                                <input type="text" class="form-control" id="exercise_image" name="exercise_image" value="<?= htmlspecialchars($data['exercise']->imatge_url ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="exercise_description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="exercise_description" name="exercise_description" rows="4" required><?= htmlspecialchars($data['exercise']->descripcio) ?></textarea>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="exercise_sets" class="form-label">Series</label>
                                <input type="number" class="form-control" id="exercise_sets" name="exercise_sets" value="<?= $data['exercise']->series ?>" min="1" max="20">
                            </div>
                            <div class="col-md-4">
                                <label for="exercise_reps" class="form-label">Repeticiones</label>
                                <input type="number" class="form-control" id="exercise_reps" name="exercise_reps" value="<?= $data['exercise']->repeticions ?>" min="1" max="100">
                            </div>
                            <div class="col-md-4">
                                <label for="exercise_rest" class="form-label">Descanso (segundos)</label>
                                <input type="number" class="form-control" id="exercise_rest" name="exercise_rest" value="<?= $data['exercise']->descans ?>" min="0" max="300">
                            </div>
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">Información Adicional</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="muscle" class="form-label">Grupo Muscular</label>
                                        <input type="text" class="form-control" id="muscle" name="muscle" value="<?= htmlspecialchars($data['additionalInfo']['muscle'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="equipment" class="form-label">Equipamiento</label>
                                        <input type="text" class="form-control" id="equipment" name="equipment" value="<?= htmlspecialchars($data['additionalInfo']['equipment'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="difficulty" class="form-label">Dificultad</label>
                                        <select class="form-select" id="difficulty" name="difficulty">
                                            <option value="">No especificada</option>
                                            <option value="principiante" <?= (isset($data['additionalInfo']['difficulty']) && $data['additionalInfo']['difficulty'] == 'principiante') ? 'selected' : '' ?>>Principiante</option>
                                            <option value="intermedio" <?= (isset($data['additionalInfo']['difficulty']) && $data['additionalInfo']['difficulty'] == 'intermedio') ? 'selected' : '' ?>>Intermedio</option>
                                            <option value="avanzado" <?= (isset($data['additionalInfo']['difficulty']) && $data['additionalInfo']['difficulty'] == 'avanzado') ? 'selected' : '' ?>>Avanzado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Previsualización de la imagen -->
                        <?php if(!empty($data['exercise']->imatge_url)): ?>
                            <div class="mb-4 text-center">
                                <label class="form-label">Previsualización:</label>
                                <div class="mt-2">
                                    <img src="<?= $data['exercise']->imatge_url ?>" alt="<?= htmlspecialchars($data['exercise']->nom) ?>" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= URLROOT ?>/staffRoutine/edit/<?= $data['routine']->rutina_id ?>" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar URL de imagen para previsualización
        const imageUrlInput = document.getElementById('exercise_image');
        const previewContainer = document.querySelector('.mb-4.text-center');
        
        // Actualizar previsualización cuando cambia la URL
        if (imageUrlInput) {
            imageUrlInput.addEventListener('blur', function() {
                updateImagePreview(this.value);
            });
        }
        
        // Función para actualizar la previsualización de la imagen
        function updateImagePreview(url) {
            // Si no hay URL, ocultar la previsualización
            if (!url) {
                if (previewContainer) {
                    previewContainer.style.display = 'none';
                }
                return;
            }
            
            // Crear o actualizar previsualización
            if (previewContainer) {
                previewContainer.style.display = 'block';
                const img = previewContainer.querySelector('img');
                if (img) {
                    img.src = url;
                }
            } else {
                // Si no existe el contenedor, crearlo
                const newPreview = document.createElement('div');
                newPreview.className = 'mb-4 text-center';
                newPreview.innerHTML = `
                    <label class="form-label">Previsualización:</label>
                    <div class="mt-2">
                        <img src="${url}" alt="Previsualización" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                `;
                
                // Insertar antes del botón de envío
                const form = document.querySelector('form');
                const submitBtn = form.querySelector('.d-flex.justify-content-between');
                form.insertBefore(newPreview, submitBtn);
            }
        }
    });
</script>