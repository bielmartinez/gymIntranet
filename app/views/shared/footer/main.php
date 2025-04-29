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

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
    });
  </script>
</footer>
</body>
</html>
