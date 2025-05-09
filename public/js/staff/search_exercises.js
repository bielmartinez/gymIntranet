document.addEventListener('DOMContentLoaded', function() {
    // Gestionar el selector de etiquetas de músculos
    const muscleLabels = document.querySelectorAll('.muscle-label');
    const selectedMuscleLabels = document.getElementById('selectedMuscleLabels');
    const hiddenMuscleInput = document.getElementById('muscles');
    
    // Variable para almacenar selección
    let selectedMuscle = null;
    
    // Inicializar selección ya existente
    if (hiddenMuscleInput.value) {
        selectedMuscle = hiddenMuscleInput.value;
        updateMuscleSelectionUI();
    }
    
    // Añadir evento click a las etiquetas de músculos
    muscleLabels.forEach(label => {
        label.addEventListener('click', function() {
            const muscle = this.dataset.muscle;
            toggleMuscleSelection(muscle);
            // Autoenviar el formulario cuando se selecciona un músculo
            document.getElementById('searchForm').submit();
        });
    });
    
    // Función para alternar la selección de músculos
    function toggleMuscleSelection(muscle) {
        if (selectedMuscle === muscle) {
            // Quitar músculo si ya estaba seleccionado
            selectedMuscle = null;
        } else {
            // Seleccionar músculo
            selectedMuscle = muscle;
        }
        
        // Actualizar UI y campo oculto
        updateMuscleSelectionUI();
    }
    
    // Actualizar la UI de selección de músculos
    function updateMuscleSelectionUI() {
        // Actualizar estado de las etiquetas de músculos
        muscleLabels.forEach(label => {
            const muscle = label.dataset.muscle;
            if (selectedMuscle === muscle) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
        });
        
        // Actualizar visualización de músculo seleccionado
        if (selectedMuscle) {
            // Convertir nombres de músculos a formato más amigable
            let displayName = getDisplayNameForMuscle(selectedMuscle);
            
            selectedMuscleLabels.innerHTML = `
                <span class="badge bg-primary rounded-pill me-2 mb-2">
                    ${displayName} <i class="fas fa-times ms-1" onclick="removeMuscle(event)"></i>
                </span>
            `;
        } else {
            selectedMuscleLabels.innerHTML = '<span class="text-muted small">Ningún grupo muscular seleccionado</span>';
        }
        
        // Actualizar campo oculto
        hiddenMuscleInput.value = selectedMuscle || '';
    }
    
    // Obtener nombre de visualización amigable
    function getDisplayNameForMuscle(muscle) {
        const muscleNames = {
            'abdominales': 'Abdominales',
            'abductores': 'Abductores',
            'aductores': 'Aductores',
            'biceps': 'Bíceps',
            'pantorrillas': 'Pantorrillas',
            'pecho': 'Pecho',
            'antebrazos': 'Antebrazos',
            'gluteos': 'Glúteos',
            'isquiotibiales': 'Isquiotibiales',
            'dorsales': 'Dorsales',
            'lumbar': 'Lumbar',
            'espalda_media': 'Espalda Media',
            'cuello': 'Cuello',
            'cuadriceps': 'Cuádriceps',
            'trapecios': 'Trapecios',
            'triceps': 'Tríceps'
        };
        
        return muscleNames[muscle] || muscle.charAt(0).toUpperCase() + muscle.slice(1);
    }
    
    // Función global para eliminar selección (llamada desde el icono X)
    window.removeMuscle = function(event) {
        event.preventDefault(); // Evitar que el clic se propague
        selectedMuscle = null;
        updateMuscleSelectionUI();
        // Volver a enviar el formulario para actualizar resultados
        document.getElementById('searchForm').submit();
    };
    
    // Configurar el modal de añadir ejercicio
    const addExerciseModal = document.getElementById('addExerciseModal');
    
    if (addExerciseModal) {
        addExerciseModal.addEventListener('show.bs.modal', function(event) {
            // Botón que activó el modal
            const button = event.relatedTarget;
              // Extraer información de los atributos data-*
            const name = button.getAttribute('data-name');
            const description = button.getAttribute('data-description');
            const index = button.getAttribute('data-index');
            const muscle = button.getAttribute('data-muscle') || '';
            const difficulty = button.getAttribute('data-difficulty') || '';
            const equipment = button.getAttribute('data-equipment') || '';
            const type = button.getAttribute('data-type') || '';
            
            // Crear objeto con datos de API
            const apiData = {
                name: name,
                instructions: description,
                muscle: muscle,
                difficulty: difficulty,
                equipment: equipment,
                type: type
            };
            
            // Guardar datos API en campo oculto
            document.getElementById('api_data').value = JSON.stringify(apiData);
            
            // Actualizar los campos del formulario
            document.getElementById('exercise_name').value = name || '';
            document.getElementById('exercise_description').value = description || '';
            
            // Calcular valor sugerido para el orden
            document.getElementById('exercise_order').value = parseInt(index || 0) + 1;
        });
    }
});
