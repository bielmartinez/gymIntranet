/**
 * Funcionalidades específicas para el dashboard de staff
 * Este archivo maneja las interacciones en el panel de control del personal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Efecto de hover para las tarjetas de acceso rápido
    const quickAccessCards = document.querySelectorAll('.row .card');
    
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
    
    // Mostrar mensaje de bienvenida si existe la función toast
    if (typeof showToast === 'function') {
        const userName = document.querySelector('.user-name')?.textContent.trim();
        
        if (userName) {
            const currentHour = new Date().getHours();
            let greeting = '';
            
            if (currentHour < 12) {
                greeting = 'Buenos días';
            } else if (currentHour < 19) {
                greeting = 'Buenas tardes';
            } else {
                greeting = 'Buenas noches';
            }
            
            showToast(`${greeting}, ${userName}. Bienvenido/a al panel de staff.`, 'info', 5000);
        }
    }

    // Formato para fechas y horas en la tabla de clases
    document.querySelectorAll('.table tbody tr').forEach(row => {
        // Puedes añadir aquí cualquier formateo o interactividad adicional para las filas de la tabla
    });

    // Manejar clics en los botones de acción de la tabla
    document.querySelectorAll('.table .btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Aquí puedes añadir efectos o confirmaciones adicionales al hacer clic en los botones
            // Por ejemplo, mostrar un indicador de carga:
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando...';
        });
    });
});
