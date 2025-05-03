</div> <!-- Cierre del div principal del contenido -->

<footer class="bg-dark text-light py-4 mt-5">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> GymIntranet. Todos los derechos reservados.</p>
      </div>
      <div class="col-md-6 text-end">
        <a href="#" class="text-light me-2"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="text-light me-2"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
  </div>

  <!-- Scripts principales -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>

  <!-- Script personalizado para inicializar tooltips y popovers de Bootstrap -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Inicializar todos los tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });

      // Inicializar todos los popovers
      var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
      popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
      });

      // Detectar clics en los botones de descartar notificación
      document.querySelectorAll('.btn-dismiss-notification').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation(); // Evitar que el clic llegue al enlace de la notificación

          const notificationId = this.getAttribute('data-id');
          dismissNavbarNotification(notificationId);
        });
      });

      // Función para descartar una notificación desde el navbar
      function dismissNavbarNotification(notificationId) {
        // Realizar petición AJAX para marcar la notificación como leída
        fetch(`<?php echo URLROOT; ?>/user/dismissNotification/${notificationId}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Eliminar la notificación de la interfaz
            const notificationItem = document.getElementById(`notification-item-${notificationId}`);
            if (notificationItem) {
              notificationItem.remove();
            }

            // Actualizar el contador de notificaciones
            const badge = document.getElementById('notificationBadge');
            if (badge) {
              let count = parseInt(badge.textContent);
              count--;

              if (count <= 0) {
                // Si no hay más notificaciones, ocultar el contador
                badge.style.display = 'none';
              } else {
                // Actualizar el valor del contador
                badge.textContent = count;
              }
            }

            // Comprobar si no hay más notificaciones para mostrar mensaje
            const remainingNotifications = document.querySelectorAll('.notification-item-container');
            if (remainingNotifications.length === 0) {
              const dropdownMenu = document.querySelector('.dropdown-menu');
              if (dropdownMenu) {
                const noNotificationsItem = document.createElement('li');
                noNotificationsItem.innerHTML = '<a class="dropdown-item text-center" href="#">No hay notificaciones</a>';

                // Insertar antes del divisor
                const divider = dropdownMenu.querySelector('.dropdown-divider');
                if (divider) {
                  dropdownMenu.insertBefore(noNotificationsItem, divider);
                }
              }
            }
          } else {
            console.error('Error al descartar la notificación:', data.message);
          }
        })
        .catch(error => {
          console.error('Error en la petición de descarte:', error);
        });
      }
    });
  </script>
</footer>
</body>
</html>
