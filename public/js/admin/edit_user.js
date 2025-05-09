/**
 * Funcionalidad para la página de edición de usuarios
 * Gestiona la validación del formulario y las interacciones de la interfaz
 */
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const userForm = document.getElementById('editUserForm');
    if (userForm) {
        userForm.addEventListener('submit', function(event) {
            // Validar el formulario antes de enviar
            if (!validateForm()) {
                event.preventDefault();
            }
        });
    }

    // Función para validar el formulario
    function validateForm() {
        let isValid = true;
        
        // Validar campos requeridos
        const requiredFields = userForm.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Validar formato de correo electrónico
        const emailField = document.getElementById('email');
        if (emailField && emailField.value.trim() !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value.trim())) {
                isValid = false;
                emailField.classList.add('is-invalid');
            } else {
                emailField.classList.remove('is-invalid');
            }
        }
        
        return isValid;
    }
    
    // Agregar eventos para la validación en tiempo real
    const inputFields = userForm.querySelectorAll('input, select, textarea');
    inputFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
            
            // Validación especial para email
            if (this.id === 'email' && this.value.trim() !== '') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(this.value.trim())) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            }
        });
    });
    
    // Manejar la interacción con la casilla de cambio de contraseña
    const changePasswordCheck = document.getElementById('changePassword');
    const passwordFields = document.getElementById('passwordFields');
    
    if (changePasswordCheck && passwordFields) {
        changePasswordCheck.addEventListener('change', function() {
            if (this.checked) {
                passwordFields.classList.remove('d-none');
                document.getElementById('password').setAttribute('required', '');
            } else {
                passwordFields.classList.add('d-none');
                document.getElementById('password').removeAttribute('required');
            }
        });
    }
});
