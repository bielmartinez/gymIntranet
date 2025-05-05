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

      // Obtener el ID del usuario actual para asociarlo con las cookies
      const currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
      
      // Si no hay usuario logueado, no hacemos nada con las notificaciones
      if (!currentUserId) return;
      
      // Construir la clave de cookie específica para este usuario
      const cookieKey = `dismissed_notifications_${currentUserId}`;

      // Funciones para manejar cookies
      const cookieManager = {
        setDismissedNotifications: function(notificationIds) {
          // Establecer una cookie que expire en 30 días
          const expiryDate = new Date();
          expiryDate.setTime(expiryDate.getTime() + (30 * 24 * 60 * 60 * 1000));
          document.cookie = cookieKey + "=" + JSON.stringify(notificationIds) + 
                          "; expires=" + expiryDate.toUTCString() + "; path=/";
        },
        
        getDismissedNotifications: function() {
          const nameEQ = cookieKey + "=";
          const ca = document.cookie.split(';');
          for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) {
              const cookieValue = c.substring(nameEQ.length, c.length);
              try {
                return JSON.parse(cookieValue);
              } catch (e) {
                return [];
              }
            }
          }
          return [];
        },
        
        addDismissedNotification: function(notificationId) {
          const dismissed = this.getDismissedNotifications();
          if (!dismissed.includes(notificationId)) {
            dismissed.push(notificationId);
            this.setDismissedNotifications(dismissed);
          }
          return dismissed;
        }
      };

      // Aplicar las notificaciones descartadas guardadas en cookies al cargar la página
      function applyDismissedNotificationsFromCookies() {
        const dismissedNotifications = cookieManager.getDismissedNotifications();
        
        if (dismissedNotifications.length > 0) {
          // Contar cuántas notificaciones están visibles actualmente
          const notificationItems = document.querySelectorAll('.notification-item-container');
          let visibleCount = notificationItems.length;
          
          // Iterar sobre las notificaciones descartadas
          dismissedNotifications.forEach(id => {
            const notificationItem = document.getElementById(`notification-item-${id}`);
            if (notificationItem) {
              notificationItem.remove();
              visibleCount--;
            }
          });
          
          // Actualizar el contador de notificaciones
          updateNotificationBadgeCount(visibleCount);
          
          // Si no quedan notificaciones visibles, mostrar el mensaje de "No hay notificaciones"
          if (visibleCount === 0) {
            addNoNotificationsMessage();
          }
        }
      }
      
      // Actualizar el contador de notificaciones en la interfaz
      function updateNotificationBadgeCount(count) {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
          if (count <= 0) {
            // Si no hay notificaciones, ocultar el contador
            badge.style.display = 'none';
          } else {
            // Actualizar el valor del contador
            badge.textContent = count;
            badge.style.display = 'inline-block';
          }
        }
      }
      
      // Añadir mensaje de "No hay notificaciones" si no quedan notificaciones
      function addNoNotificationsMessage() {
        const dropdownMenu = document.querySelector('.dropdown-menu');
        if (dropdownMenu) {
          const existingMessage = dropdownMenu.querySelector('.no-notifications-message');
          if (!existingMessage) {
            const noNotificationsItem = document.createElement('li');
            noNotificationsItem.className = 'no-notifications-message';
            noNotificationsItem.innerHTML = '<a class="dropdown-item text-center" href="#">No hay notificaciones</a>';
            
            // Insertar antes del divisor
            const divider = dropdownMenu.querySelector('.dropdown-divider');
            if (divider) {
              dropdownMenu.insertBefore(noNotificationsItem, divider);
            }
          }
        }
      }

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
        // Agregar notificación a las cookies descartadas
        cookieManager.addDismissedNotification(notificationId);
        
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

            // Contar cuántas notificaciones quedan visibles
            const remainingNotifications = document.querySelectorAll('.notification-item-container');
            updateNotificationBadgeCount(remainingNotifications.length);

            // Comprobar si no hay más notificaciones para mostrar mensaje
            if (remainingNotifications.length === 0) {
              addNoNotificationsMessage();
            }
          } else {
            console.error('Error al descartar la notificación:', data.message);
          }
        })
        .catch(error => {
          console.error('Error en la petición de descarte:', error);
        });
      }
      
      // Aplicar notificaciones descartadas al cargar la página
      applyDismissedNotificationsFromCookies();
    });
  </script>
</footer>
</body>
</html>
