/**
 * Funcionalidad para la página de envío de notificaciones
 * Gestiona la interactividad del formulario y la selección de destinatarios
 */
document.addEventListener('DOMContentLoaded', function() {
    // Gestionar visualización de secciones según el tipo de destinatario
    const recipientTypeRadios = document.querySelectorAll('input[name="recipientType"]');
    const specificUsersSection = document.getElementById('specificUsersSection');
    const classSection = document.getElementById('classSection');
    
    recipientTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'specific') {
                specificUsersSection.classList.remove('d-none');
                classSection.classList.add('d-none');
            } else if (this.value === 'class') {
                specificUsersSection.classList.add('d-none');
                classSection.classList.remove('d-none');
            } else {
                specificUsersSection.classList.add('d-none');
                classSection.classList.add('d-none');
            }
        });
    });
    
    // Filtro de búsqueda de usuarios
    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        userSearch.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#usersTable tbody tr');
            
            tableRows.forEach(row => {
                const userName = row.cells[2].textContent.toLowerCase();
                const userEmail = row.cells[3].textContent.toLowerCase();
                
                if (userName.includes(searchValue) || userEmail.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Seleccionar/deseleccionar todos los usuarios
    const selectAllUsers = document.getElementById('selectAllUsers');
    if (selectAllUsers) {
        selectAllUsers.addEventListener('change', function() {
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Validación del formulario
    const notificationForm = document.getElementById('notificationForm');
    if (notificationForm) {
        notificationForm.addEventListener('submit', function(event) {
            const recipientType = document.querySelector('input[name="recipientType"]:checked').value;
            
            if (recipientType === 'specific') {
                const selectedUsers = document.querySelectorAll('input[name="selectedUsers[]"]:checked');
                if (selectedUsers.length === 0) {
                    event.preventDefault();
                    alert('Por favor, selecciona al menos un usuario para enviar la notificación.');
                }
            } else if (recipientType === 'class') {
                const selectedClass = document.getElementById('classSelect').value;
                if (!selectedClass) {
                    event.preventDefault();
                    alert('Por favor, selecciona una clase para enviar la notificación.');
                }
            }
        });
    }
});
