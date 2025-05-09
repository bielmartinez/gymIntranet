// Script para la página de mis reservas

document.addEventListener('DOMContentLoaded', function() {
  // Inicializar DataTable para el historial si existe
  if (typeof $ !== 'undefined' && $('#historyTable').length > 0) {
    setTimeout(function() {
      try {
        $('#historyTable').DataTable({
          language: {
            url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
          },
          order: [[1, 'desc'], [2, 'desc']], // Ordenar por fecha (desc) y hora (desc)
          responsive: true
        });
      } catch (error) {
        console.error('Error al inicializar DataTables:', error);
      }
    }, 500);
  }
  
  // Manejar el botón de cancelar reserva
  document.querySelectorAll('.cancel-reservation').forEach(button => {
    button.addEventListener('click', function() {
      const reservationId = this.getAttribute('data-reservation-id');
      document.getElementById('reservation_id_to_cancel').value = reservationId;
    });
  });
});
