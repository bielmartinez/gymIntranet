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
            const routineId = document.getElementById('routine_id').value;
            document.getElementById('editExerciseForm').action = 
                `${URLROOT}/staffRoutine/updateExercise/${id}/${routineId}`;
        });
    }
});
