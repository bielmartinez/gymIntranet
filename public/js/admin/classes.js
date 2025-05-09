/**
 * Funcionalidades específicas para la administración de clases
 * Este archivo maneja las interacciones en la página de gestión de clases
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables para la tabla de clases cuando jQuery esté disponible
    setTimeout(function() {
        if (typeof $ !== 'undefined') {
            try {
                $('#classesTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                        emptyTable: 'No hay clases disponibles'
                    },
                    order: [[3, 'asc'], [4, 'asc']], // Ordenar por fecha (col 3) y hora (col 4)
                    responsive: true,
                    columnDefs: [
                        { targets: [7, 8], orderable: false } // Columnas no ordenables
                    ]
                });
            } catch (error) {
                console.error('Error al inicializar DataTables:', error);
            }
        } else {
            console.warn('jQuery no está disponible para inicializar DataTables');
        }
    }, 500);

    // Manejar el evento de editar clase
    document.querySelectorAll('.edit-class-btn').forEach(button => {
        button.addEventListener('click', function() {
            const classId = this.getAttribute('data-class-id');
            
            // Mostrar mensaje de carga en el modal
            const editModal = document.getElementById('editClassModal');
            const loadingMessage = document.createElement('div');
            loadingMessage.className = 'text-center my-3';
            loadingMessage.innerHTML = '<div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando datos de la clase...</p>';
            
            // Añadir mensaje de carga al modal
            const modalBody = editModal.querySelector('.modal-body');
            const formElement = modalBody.querySelector('form');
            modalBody.insertBefore(loadingMessage, formElement);
            
            // Obtener datos de la clase desde el servidor
            fetch(`${URLROOT}/Admin/getClassDetails/${classId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    // Eliminar mensaje de carga
                    if (loadingMessage) {
                        loadingMessage.remove();
                    }
                    
                    if (data.success) {
                        console.log('Datos recibidos para editar clase:', data); // Para debug
                        
                        // Rellenar el formulario con los datos
                        document.getElementById('editClassId').value = data.class.classe_id;
                        document.getElementById('editClassType').value = data.class.tipus_classe_id;
                        document.getElementById('editClassMonitor').value = data.class.monitor_id;
                        document.getElementById('editClassDate').value = data.class.data;
                        document.getElementById('editClassTime').value = data.class.hora;
                        document.getElementById('editClassDuration').value = data.class.duracio;
                        document.getElementById('editClassRoom').value = data.class.sala;
                        document.getElementById('editClassCapacity').value = data.class.capacitat_maxima;
                    } else {
                        alert('Error al cargar los datos de la clase: ' + (data.error || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al conectar con el servidor: ' + error.message);
                    
                    // Eliminar mensaje de carga en caso de error
                    if (loadingMessage) {
                        loadingMessage.remove();
                    }
                });
        });
    });

    // Manejar el evento de eliminar clase
    document.querySelectorAll('.delete-class-btn').forEach(button => {
        button.addEventListener('click', function() {
            const classId = this.getAttribute('data-class-id');
            document.getElementById('deleteClassId').value = classId;
        });
    });
    
    // Establecer la fecha actual en el filtro de fecha
    const filterDateElement = document.getElementById('filterDate');
    if (filterDateElement && !filterDateElement.value) {
        filterDateElement.valueAsDate = new Date();
    }
    
    // Establecer fecha actual mínima en el formulario de crear clase
    const classDateElement = document.getElementById('classDate');
    if (classDateElement) {
        const today = new Date().toISOString().split('T')[0];
        classDateElement.min = today;
        if (!classDateElement.value) {
            classDateElement.value = today;
        }
    }
    
    // Validación de capacidad máxima
    const capacityInputs = document.querySelectorAll('input[name="capacitat_maxima"], input[name="edit_capacitat_maxima"]');
    capacityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const value = parseInt(this.value);
            if (isNaN(value) || value <= 0) {
                this.value = 1;
                alert('La capacidad debe ser un número mayor que cero.');
            }
        });
    });
    
    // Validación de duración
    const durationInputs = document.querySelectorAll('input[name="duracio"], input[name="edit_duracio"]');
    durationInputs.forEach(input => {
        input.addEventListener('change', function() {
            const value = parseInt(this.value);
            if (isNaN(value) || value <= 0) {
                this.value = 30;
                alert('La duración debe ser un número mayor que cero.');
            }
        });
    });
});
