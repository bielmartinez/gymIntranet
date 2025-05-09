/**
 * Funcionalidades específicas para la página de rutinas de usuario
 * Este archivo maneja las interacciones en la página de rutinas
 */

document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad para mostrar mensaje de confirmación al descargar PDF
    const pdfButtons = document.querySelectorAll('a[href*="/userRoutine/downloadPDF/"]');
    pdfButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Mostrar mensaje de descarga iniciada
            showToast('Preparando descarga del PDF...', 'info');
        });
    });
    
    // Funcionalidad para animar tarjetas al hacer hover
    const routineCards = document.querySelectorAll('.routine-card');
    routineCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
        });
    });
    
    // Funcionalidad para buscar rutinas si existe un campo de búsqueda
    const searchInput = document.getElementById('searchRoutine');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            routineCards.forEach(card => {
                const cardTitle = card.querySelector('.card-title').textContent.toLowerCase();
                const cardDescription = card.querySelector('.card-text:not(.text-muted)').textContent.toLowerCase();
                
                // Mostrar u ocultar la tarjeta según si coincide con la búsqueda
                if (cardTitle.includes(searchTerm) || cardDescription.includes(searchTerm)) {
                    card.closest('.col-lg-4').style.display = '';
                } else {
                    card.closest('.col-lg-4').style.display = 'none';
                }
            });
            
            // Verificar si hay resultados visibles
            checkNoResults();
        });
    }
    
    // Función para verificar si no hay resultados de búsqueda
    function checkNoResults() {
        const visibleCards = document.querySelectorAll('.routine-card:not([style*="display: none"])');
        const noResultsMessage = document.getElementById('noSearchResults');
        
        if (visibleCards.length === 0) {
            if (!noResultsMessage) {
                const resultsContainer = document.querySelector('.row');
                const message = document.createElement('div');
                message.id = 'noSearchResults';
                message.className = 'col-12 alert alert-info';
                message.innerHTML = '<i class="fas fa-info-circle me-2"></i>No se encontraron rutinas que coincidan con tu búsqueda.';
                resultsContainer.appendChild(message);
            }
        } else if (noResultsMessage) {
            noResultsMessage.remove();
        }
    }
});
