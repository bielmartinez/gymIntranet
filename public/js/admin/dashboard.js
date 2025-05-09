/**
 * Funcionalidades específicas para el dashboard de administración
 * Este archivo maneja las interacciones en el panel de control de administrador
 */

document.addEventListener('DOMContentLoaded', function() {
    // Efecto de hover para las tarjetas de acceso rápido
    const quickAccessCards = document.querySelectorAll('.quick-access .card');
    
    quickAccessCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 0 0 rgba(0, 0, 0, 0)';
        });
    });
    
    // Mostrar mensaje de bienvenida
    const userName = document.querySelector('.welcome-message')?.textContent.trim() || 'Administrador';
    const currentHour = new Date().getHours();
    let greeting = '';
    
    if (currentHour < 12) {
        greeting = 'Buenos días';
    } else if (currentHour < 19) {
        greeting = 'Buenas tardes';
    } else {
        greeting = 'Buenas noches';
    }
    
    // Mostrar mensaje si existe el elemento toast
    if (typeof showToast === 'function') {
        showToast(`${greeting}, ${userName}. Bienvenido/a al panel de administración.`, 'info', 5000);
    }
    
    // Actualizar contador de tiempo desde el último inicio de sesión
    function updateLastLoginTime() {
        const lastLoginElement = document.getElementById('lastLoginTime');
        if (lastLoginElement && lastLoginElement.dataset.time) {
            const loginTime = new Date(lastLoginElement.dataset.time);
            const now = new Date();
            const diffInMinutes = Math.floor((now - loginTime) / (1000 * 60));
            
            let timeMessage = '';
            if (diffInMinutes < 1) {
                timeMessage = 'hace menos de un minuto';
            } else if (diffInMinutes < 60) {
                timeMessage = `hace ${diffInMinutes} minuto${diffInMinutes === 1 ? '' : 's'}`;
            } else {
                const diffInHours = Math.floor(diffInMinutes / 60);
                if (diffInHours < 24) {
                    timeMessage = `hace ${diffInHours} hora${diffInHours === 1 ? '' : 's'}`;
                } else {
                    timeMessage = `hace ${Math.floor(diffInHours / 24)} día${Math.floor(diffInHours / 24) === 1 ? '' : 's'}`;
                }
            }
            
            lastLoginElement.textContent = timeMessage;
        }
    }
    
    // Si existe el elemento para el último inicio de sesión, actualizarlo cada minuto
    if (document.getElementById('lastLoginTime')) {
        updateLastLoginTime();
        setInterval(updateLastLoginTime, 60000); // Actualizar cada minuto
    }
});
