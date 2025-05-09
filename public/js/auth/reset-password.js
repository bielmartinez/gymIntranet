/**
 * JavaScript para la página de restablecimiento de contraseña
 */

document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const form = document.querySelector('form');
    
    if (form && newPassword && confirmPassword) {
        // Validación de coincidencia de contraseñas
        form.addEventListener('submit', function(event) {
            if (newPassword.value !== confirmPassword.value) {
                event.preventDefault();
                showError(confirmPassword, 'Las contraseñas no coinciden');
            } else {
                // Validar complejidad de la contraseña
                const passwordStrength = validatePasswordStrength(newPassword.value);
                if (passwordStrength !== true) {
                    event.preventDefault();
                    showError(newPassword, passwordStrength);
                }
            }
        });
        
        // Validación en tiempo real
        confirmPassword.addEventListener('input', function() {
            if (newPassword.value !== confirmPassword.value) {
                showError(this, 'Las contraseñas no coinciden');
            } else {
                removeError(this);
            }
        });
        
        newPassword.addEventListener('input', function() {
            const passwordStrength = validatePasswordStrength(this.value);
            if (passwordStrength !== true) {
                showError(this, passwordStrength);
            } else {
                removeError(this);
            }
            
            // Validar nuevamente la confirmación
            if (confirmPassword.value) {
                if (this.value !== confirmPassword.value) {
                    showError(confirmPassword, 'Las contraseñas no coinciden');
                } else {
                    removeError(confirmPassword);
                }
            }
        });
    }
    
    // Función para validar la fortaleza de la contraseña
    function validatePasswordStrength(password) {
        if (password.length < 8) {
            return 'La contraseña debe tener al menos 8 caracteres';
        }
        
        return true;
    }
    
    // Funciones para mostrar y ocultar errores
    function showError(input, message) {
        removeError(input);
        
        input.classList.add('is-invalid');
        
        const invalidFeedback = document.createElement('div');
        invalidFeedback.classList.add('invalid-feedback');
        invalidFeedback.textContent = message;
        
        const parent = input.parentElement;
        parent.appendChild(invalidFeedback);
    }
    
    function removeError(input) {
        input.classList.remove('is-invalid');
        
        const parent = input.parentElement;
        const invalidFeedback = parent.querySelector('.invalid-feedback');
        
        if (invalidFeedback) {
            invalidFeedback.remove();
        }
    }
});
