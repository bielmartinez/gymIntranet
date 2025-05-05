<?php
/**
 * Vista para buscar ejercicios en la API externa
 */

// Definir el título de la página para el header
$pageTitle = isset($data['title']) ? $data['title'] : 'Buscar Ejercicios';
$routineId = isset($data['routineId']) ? $data['routineId'] : '';

// Incluir el header principal
include_once APPROOT . '/views/shared/header/main.php';
?>

<style>
    .exercise-card {
        transition: all 0.3s ease;
        height: 100%;
    }
    .exercise-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .exercise-image {
        height: 200px;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><?= $pageTitle ?></h1>
        <div>
            <?php if (isset($data['routineId'])): ?>
            <a href="<?= URLROOT ?>/staffRoutine/edit/<?= $data['routineId'] ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver a la rutina
            </a>
            <?php else: ?>
            <a href="<?= URLROOT ?>/staffRoutine" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver a rutinas
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mensaje de alerta -->
    <?php if(isset($_SESSION['exercise_message'])): ?>
        <div class="alert alert-<?= $_SESSION['exercise_message_type'] ?> alert-dismissible fade show">
            <?= $_SESSION['exercise_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['exercise_message']);
            unset($_SESSION['exercise_message_type']);
        ?>
    <?php endif; ?>

    <!-- Formulario de búsqueda -->
    <div class="row">
        <div class="col-lg-3 col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filtros de búsqueda</h6>
                </div>
                <div class="card-body">
                    <form id="searchForm" class="mb-4">
                        <div class="mb-3">
                            <label for="queryType" class="form-label">Tipo de búsqueda</label>
                            <select class="form-select" id="queryType" required>
                                <option value="name">Nombre del ejercicio</option>
                                <option value="muscle">Grupo muscular</option>
                                <option value="type">Tipo de ejercicio</option>
                                <option value="equipment">Equipo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="searchQuery" class="form-label">Término de búsqueda</label>
                            <input type="text" class="form-control" id="searchQuery" required 
                                placeholder="Ej: press banca, biceps, mancuernas...">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                    </form>
                    
                    <hr>
                    <h6 class="text-primary mb-3">Búsquedas rápidas</h6>
                    
                    <div class="mb-2">
                        <p class="mb-1 fw-bold small text-muted">Grupos musculares:</p>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="muscle" data-query="chest">Pecho</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="muscle" data-query="biceps">Bíceps</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="muscle" data-query="triceps">Tríceps</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="muscle" data-query="lats">Espalda</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="muscle" data-query="abdominals">Abdomen</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="muscle" data-query="quadriceps">Piernas</button>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <p class="mb-1 fw-bold small text-muted">Equipamiento:</p>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="equipment" data-query="dumbbell">Mancuernas</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="equipment" data-query="barbell">Barra</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="equipment" data-query="bodyweight">Sin equipo</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-search" data-type="equipment" data-query="machine">Máquinas</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9 col-md-8">
            <!-- Spinner de carga -->
            <div id="loadingSpinner" class="text-center py-5 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Buscando ejercicios...</p>
            </div>
            
            <!-- Resultados de la búsqueda -->
            <div id="searchResults">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-dumbbell fa-3x mb-3"></i>
                    <p>Utiliza los filtros para buscar ejercicios</p>
                    <p class="small">Podrás añadirlos directamente a la rutina seleccionada</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para añadir ejercicio desde API -->
<div class="modal fade" id="addExerciseModal" tabindex="-1" aria-labelledby="addExerciseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExerciseModalLabel">Añadir Ejercicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addExerciseForm" action="<?= URLROOT ?>/staffRoutine/addExerciseFromApi/<?= $routineId ?>" method="post">
                    <input type="hidden" name="name" id="exercise_name">
                    <input type="hidden" name="description" id="exercise_description">
                    <input type="hidden" name="image_url" id="exercise_image_url">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="series" class="form-label">Series</label>
                                <input type="number" class="form-control" id="series" name="series" value="3" min="1" max="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="repetitions" class="form-label">Repeticiones</label>
                                <input type="number" class="form-control" id="repetitions" name="repetitions" value="12" min="1" max="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rest" class="form-label">Descanso (segundos)</label>
                                <input type="number" class="form-control" id="rest" name="rest" value="60" min="0" max="300">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order" class="form-label">Orden</label>
                                <input type="number" class="form-control" id="order" name="order" value="1" min="1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Detalles del ejercicio:</label>
                        <div id="exercise_details" class="border p-3 rounded bg-light"></div>
                    </div>
                    
                    <div class="text-center">
                        <img id="exercise_preview" src="" alt="Vista previa del ejercicio" class="img-fluid rounded" style="max-height: 200px; display: none;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="addExerciseForm" class="btn btn-primary">Añadir ejercicio</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchResults = document.getElementById('searchResults');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const quickSearchButtons = document.querySelectorAll('.quick-search');
    
    // Configuración de los botones de búsqueda rápida
    quickSearchButtons.forEach(button => {
        button.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            const query = this.getAttribute('data-query');
            
            document.getElementById('queryType').value = type;
            document.getElementById('searchQuery').value = query;
            
            searchExercises(type, query);
        });
    });
    
    // Función para manejar la búsqueda del formulario
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const queryType = document.getElementById('queryType').value;
        const searchQuery = document.getElementById('searchQuery').value.trim();
        
        if (!searchQuery) {
            alert('Por favor ingresa un término de búsqueda');
            return;
        }
        
        searchExercises(queryType, searchQuery);
    });
    
    // Función para realizar la búsqueda de ejercicios
    function searchExercises(type, query) {
        // Mostrar spinner de carga y ocultar resultados anteriores
        loadingSpinner.classList.remove('d-none');
        searchResults.innerHTML = '';
        
        // Realizar la petición AJAX
        fetch(`<?= URLROOT ?>/staffRoutine/apiSearch?type=${type}&query=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Ocultar spinner de carga
            loadingSpinner.classList.add('d-none');
            
            // Verificar si hay un error en la respuesta
            if (data.error) {
                searchResults.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.error}
                    </div>
                `;
                return;
            }

            // Mostrar resultados
            displaySearchResults(data);
        })
        .catch(error => {
            loadingSpinner.classList.add('d-none');
            searchResults.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Error al buscar ejercicios: ${error.message || 'Error desconocido'}
                </div>
            `;
            console.error('Error al buscar ejercicios:', error);
        });
    }
    
    // Función para mostrar los resultados de búsqueda
    function displaySearchResults(data) {
        if (!data || data.length === 0) {
            searchResults.innerHTML = `
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    No se encontraron ejercicios con esos criterios de búsqueda.
                </div>
            `;
            return;
        }
        
        // Crear la cuadrícula de resultados
        let resultsHTML = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';
        
        data.forEach(exercise => {
            const imageUrl = exercise.gifUrl || '';
            const exerciseName = exercise.name || 'Ejercicio sin nombre';
            const muscleTarget = exercise.target || exercise.bodyPart || 'No especificado';
            const equipment = exercise.equipment || 'No especificado';
            const description = Array.isArray(exercise.instructions) ? exercise.instructions.join('. ') : '';
            
            resultsHTML += `
                <div class="col">
                    <div class="card h-100 exercise-card shadow-sm">
                        <div class="exercise-image" style="${imageUrl ? 'background-image: url(\'' + imageUrl + '\')' : ''}">
                            ${!imageUrl ? '<i class="fas fa-image fa-3x"></i>' : ''}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">${exerciseName}</h5>
                            <p class="card-text small text-muted">
                                <strong>Músculo:</strong> ${muscleTarget}<br>
                                <strong>Equipo:</strong> ${equipment}
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-sm btn-primary add-exercise-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#addExerciseModal"
                                        data-name="${exerciseName}"
                                        data-image="${imageUrl}"
                                        data-muscle="${muscleTarget}"
                                        data-equipment="${equipment}"
                                        data-description="${description}">
                                    <i class="fas fa-plus me-1"></i> Añadir a rutina
                                </button>
                                <button type="button" class="btn btn-sm btn-success" 
                                        onclick="addDirectlyWithParams('${exerciseName.replace(/'/g, "\\'")}', '${description.replace(/'/g, "\\'")}', '${imageUrl}', 3, 12, 60, 1, '${muscleTarget}', '${equipment}')">
                                    <i class="fas fa-bolt me-1"></i> Añadir directamente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        resultsHTML += '</div>';
        searchResults.innerHTML = resultsHTML;
        
        // Configurar los botones de añadir ejercicio
        document.querySelectorAll('.add-exercise-btn').forEach(button => {
            button.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const image = this.getAttribute('data-image');
                const muscle = this.getAttribute('data-muscle');
                const equipment = this.getAttribute('data-equipment');
                const description = this.getAttribute('data-description');
                
                document.getElementById('exercise_name').value = name;
                document.getElementById('exercise_description').value = description;
                document.getElementById('exercise_image_url').value = image;
                document.getElementById('exercise_details').innerHTML = `
                    <p><strong>Nombre:</strong> ${name}</p>
                    <p><strong>Músculo objetivo:</strong> ${muscle}</p>
                    <p><strong>Equipo:</strong> ${equipment}</p>
                    <p><strong>Descripción:</strong> ${description}</p>
                `;
                
                // Mostrar imagen de previsualización
                const previewImage = document.getElementById('exercise_preview');
                if (image) {
                    previewImage.src = image;
                    previewImage.style.display = 'block';
                } else {
                    previewImage.style.display = 'none';
                }
            });
        });
    }

    // Botón de añadir directamente dentro del modal
    document.getElementById('btnAddDirectly').addEventListener('click', function() {
        // Tomar los valores del formulario
        const name = document.getElementById('exercise_name').value;
        const description = document.getElementById('exercise_description').value;
        const imageUrl = document.getElementById('exercise_image_url').value;
        const series = document.getElementById('series').value;
        const repetitions = document.getElementById('repetitions').value;
        const rest = document.getElementById('rest').value;
        const order = document.getElementById('order').value;

        // Llamar a la función addDirectly con estos valores
        addDirectlyWithParams(name, description, imageUrl, series, repetitions, rest, order);
    });
});

// Función para añadir ejercicio directamente con parámetros específicos
function addDirectlyWithParams(name, description, imageUrl, series, repetitions, rest, order, muscle = '', equipment = '') {
    // Crear formulario dinámicamente
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= URLROOT ?>/staffRoutine/addExerciseFromApi/<?= $routineId ?>';
    form.style.display = 'none';

    // Añadir los campos al formulario
    const fields = {
        'name': name,
        'description': description,
        'image_url': imageUrl,
        'series': series,
        'repetitions': repetitions,
        'rest': rest,
        'order': order,
        'muscle': muscle,
        'equipment': equipment
    };

    for (const [key, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }

    // Añadir el formulario al documento y enviarlo
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php
// Incluir el footer
include_once APPROOT . '/views/shared/footer/main.php';
?>