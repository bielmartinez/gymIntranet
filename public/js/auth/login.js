/**
 * JavaScript específico para la página de inicio de sesión
 */

document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar la contraseña
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            // Toggle password visibility
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle eye icon
            const eyeIcon = this.querySelector('i');
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    }

    // Validación del formulario en el cliente
    const loginForm = document.querySelector('form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            let isValid = true;
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            
            // Validar email
            if (email && (email.value.trim() === '' || !isValidEmail(email.value))) {
                showError(email, 'Por favor ingresa un correo electrónico válido');
                isValid = false;
            } else if (email) {
                removeError(email);
            }
            
            // Validar password
            if (password && password.value.trim() === '') {
                showError(password, 'La contraseña es requerida');
                isValid = false;
            } else if (password) {
                removeError(password);
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Funciones auxiliares para validación
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function showError(input, message) {
        const formControl = input.parentElement;
        const invalidFeedback = formControl.querySelector('.invalid-feedback') || document.createElement('div');
        
        input.classList.add('is-invalid');
        
        if (!formControl.querySelector('.invalid-feedback')) {
            invalidFeedback.classList.add('invalid-feedback');
            invalidFeedback.innerText = message;
            formControl.appendChild(invalidFeedback);
        } else {
            invalidFeedback.innerText = message;
        }
    }
    
    function removeError(input) {
        input.classList.remove('is-invalid');
        const formControl = input.parentElement;
        const invalidFeedback = formControl.querySelector('.invalid-feedback');
        
        if (invalidFeedback) {
            invalidFeedback.remove();
        }
    }
});
