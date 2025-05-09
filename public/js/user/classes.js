// Script para la página de clases de usuario
document.addEventListener('DOMContentLoaded', function() {
  // Establecer la fecha actual como valor predeterminado para el filtro si no hay filtro previo
  const dateInput = document.querySelector('input[name="date"]');
  if (dateInput) {
    // Establecer la fecha mínima como hoy
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
    
    if (dateInput.value === "") {
      dateInput.valueAsDate = new Date();
    } else if (dateInput.value < today) {
      // Si la fecha seleccionada es anterior a hoy, actualizar a la fecha actual
      dateInput.valueAsDate = new Date();
    }
  }
  
  // Variables para tracking de filtros activos
  let activeTypeFilter = 'all';
  const noResultsMessage = document.createElement('div');
  noResultsMessage.className = 'col-12';
  noResultsMessage.innerHTML = `
    <div class="alert alert-info">
      <i class="fas fa-info-circle me-2"></i>
      No hay clases disponibles con los filtros seleccionados.
      <button class="btn btn-sm btn-outline-primary ms-2" id="resetAllFilters">Restablecer filtros</button>
    </div>
  `;
  
  // Función para aplicar filtros
  function applyFilters() {
    let visibleCards = 0;
    
    document.querySelectorAll('.class-card').forEach(card => {
      // Verificar si cumple con el filtro de tipo
      const matchesTypeFilter = (activeTypeFilter === 'all' || card.dataset.classType === activeTypeFilter);
      
      // Mostrar u ocultar la tarjeta según los filtros
      if (matchesTypeFilter) {
        card.style.display = 'block';
        visibleCards++;
      } else {
        card.style.display = 'none';
      }
    });
    
    // Mostrar mensaje si no hay resultados
    const classesContainer = document.getElementById('classesContainer');
    if (visibleCards === 0 && !document.querySelector('.alert-info')) {
      classesContainer.appendChild(noResultsMessage);
    } else if (visibleCards > 0) {
      const existingMessage = classesContainer.querySelector('.alert-info');
      if (existingMessage && existingMessage.parentNode === classesContainer) {
        classesContainer.removeChild(existingMessage);
      }
    }
    
    // Actualizar indicador de filtro activo
    updateActiveFilterIndicator();
  }
  
  // Actualizar indicador visual de filtro activo
  function updateActiveFilterIndicator() {
    const activeFilterBadge = document.getElementById('activeTypeFilter');
    
    if (activeTypeFilter === 'all') {
      activeFilterBadge.style.display = 'none';
    } else {
      // Encontrar el nombre del tipo de clase seleccionado
      const selectedFilterElement = document.querySelector(`.filter-class[data-filter="${activeTypeFilter}"]`);
      if (selectedFilterElement) {
        activeFilterBadge.textContent = selectedFilterElement.textContent;
        activeFilterBadge.style.display = 'inline-block';
      }
    }
  }
  
  // Filtrar clases por tipo
  document.querySelectorAll('.filter-class').forEach(item => {
    item.addEventListener('click', event => {
      event.preventDefault();
      activeTypeFilter = event.target.dataset.filter;
      applyFilters();
    });
  });

  // Restablecer filtro de fecha
  const resetDateFilterButton = document.getElementById('resetDateFilter');
  if (resetDateFilterButton) {
    resetDateFilterButton.addEventListener('click', () => {
      const dateFilterForm = document.getElementById('dateFilterForm');
      const dateFilterInput = document.getElementById('dateFilter');
      dateFilterInput.valueAsDate = new Date();
      dateFilterForm.submit();
    });
  }

  // Restablecer todos los filtros (botón dinámico)
  document.addEventListener('click', function(event) {
    if (event.target && event.target.id === 'resetAllFilters') {
      // Reiniciar el filtro de tipo
      activeTypeFilter = 'all';
      
      // En lugar de enviar el formulario con la fecha actual, redirigir a la página sin filtros
      window.location.href = URLROOT + '/user/classes';
    }
  });

  // Eliminar todos los filtros (botón estático)
  const clearAllFiltersBtn = document.getElementById('clearAllFiltersBtn');
  if (clearAllFiltersBtn) {
    clearAllFiltersBtn.addEventListener('click', () => {
      // Reiniciar el filtro de tipo
      activeTypeFilter = 'all';
      
      // En lugar de enviar el formulario con la fecha actual, redirigir a la página sin filtros
      window.location.href = URLROOT + '/user/classes';
    });
  }
  
  // Aplicar filtros iniciales
  applyFilters();
});
