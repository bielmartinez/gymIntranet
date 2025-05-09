/**
 * JavaScript específico para la página de registro de usuarios
 */

document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle eye icon
            const eyeIcon = this.querySelector('i');
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Form validation
    const registerForm = document.getElementById('registerForm');
    const confirmPassword = document.getElementById('confirmPassword');
    
    if (registerForm && confirmPassword && password) {
        registerForm.addEventListener('submit', function(event) {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                event.preventDefault();
            } else {
                confirmPassword.setCustomValidity('');
            }
            
            // Validación adicional en el lado del cliente
            validatePassword(password);
        });
        
        // Validación en tiempo real de contraseña
        password.addEventListener('input', function() {
            validatePassword(this);
        });
    }
    
    // Role dependent fields
    const roleSelect = document.getElementById('role');
    const membershipSelect = document.getElementById('membershipType');
    
    if (roleSelect && membershipSelect) {
        roleSelect.addEventListener('change', function() {
            if (this.value === 'user') {
                membershipSelect.setAttribute('required', 'required');
                membershipSelect.value = membershipSelect.value || 'basic';
            } else {
                membershipSelect.removeAttribute('required');
                if (this.value === 'staff' || this.value === 'admin') {
                    membershipSelect.value = 'none';
                }
            }
        });
    }
      // Función para validar la contraseña
    function validatePassword(passwordInput) {
        const password = passwordInput.value;
        
        // Validar longitud mínima (solo se requiere que tenga al menos 8 caracteres)
        if (password.length < 8) {
            passwordInput.setCustomValidity('La contraseña debe tener al menos 8 caracteres');
            return;
        }
        
        // Si pasa la validación de longitud, no hay error
        passwordInput.setCustomValidity('');
    }
});
