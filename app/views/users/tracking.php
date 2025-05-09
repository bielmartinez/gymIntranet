<?php 
// El header y footer ahora son incluidos por direct-view.php
// No necesitamos incluirlos aquí
// Solo mantenemos el logger para registro de actividad
include_once __DIR__ . '/../../utils/Logger.php';
Logger::log('INFO', 'Acceso a tracking.php');
?>

<!-- Enlazar hoja de estilos específica para tracking -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/user/tracking.css">

<div class="container-fluid">
  <div class="row">
    <main class="col-12 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Seguimiento Físico</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMeasurementModal">
            <i class="fas fa-plus me-2"></i>Nueva Medición
          </button>
        </div>
      </div>

      <div class="row mb-4">
        <!-- Tarjeta de estadísticas actuales -->
        <div class="col-lg-4">
          <div class="card shadow h-100">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Medidas Actuales</h6>
            </div>
            <div class="card-body">
              <div class="row no-gutters align-items-center mb-3">
                <div class="col-auto me-3">
                  <i class="fas fa-weight fa-2x text-gray-300"></i>
                </div>
                <div class="col">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Peso</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?php echo isset($data['lastMeasurement']->pes) ? number_format($data['lastMeasurement']->pes, 1) . ' kg' : 'No registrado'; ?>
                  </div>
                </div>
              </div>
              <div class="row no-gutters align-items-center mb-3">
                <div class="col-auto me-3">
                  <i class="fas fa-ruler-vertical fa-2x text-gray-300"></i>
                </div>
                <div class="col">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Altura</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?php echo isset($data['lastMeasurement']->alcada) ? number_format($data['lastMeasurement']->alcada, 1) . ' cm' : 'No registrada'; ?>
                  </div>
                </div>
              </div>
              <div class="row no-gutters align-items-center mb-3">
                <div class="col-auto me-3">
                  <i class="fas fa-calculator fa-2x text-gray-300"></i>
                </div>
                <div class="col">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">IMC</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?php 
                    if (isset($data['lastMeasurement']->imc)) {
                      echo number_format($data['lastMeasurement']->imc, 1);
                      
                      // Determinar categoría IMC
                      $imc = $data['lastMeasurement']->imc;
                      $imcClass = '';
                      $imcText = '';
                      
                      if ($imc < 18.5) {
                        $imcClass = 'text-info';
                        $imcText = 'Bajo peso';
                      } elseif ($imc < 25) {
                        $imcClass = 'text-success';
                        $imcText = 'Normal';
                      } elseif ($imc < 30) {
                        $imcClass = 'text-warning';
                        $imcText = 'Sobrepeso';
                      } else {
                        $imcClass = 'text-danger';
                        $imcText = 'Obesidad';
                      }
                      
                      echo '<div class="small ' . $imcClass . '">' . $imcText . '</div>';
                    } else {
                      echo 'No calculado';
                    }
                    ?>
                  </div>
                </div>
              </div>
              <div class="row no-gutters align-items-center">
                <div class="col-auto me-3">
                  <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                </div>
                <div class="col">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Última Actualización</div>
                  <div class="small text-gray-800">
                    <?php 
                      if (isset($data['lastMeasurement']->data_mesura)) {
                        echo date('d/m/Y', strtotime($data['lastMeasurement']->data_mesura));
                      } else {
                        echo 'No hay registros';
                      }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de gráfico de peso -->
        <div class="col-lg-8">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Evolución del Peso</h6>
              <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="weightChartDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="weightChartDropdown">
                  <div class="dropdown-header">Rango:</div>
                  <a class="dropdown-item chart-range" href="#" data-months="3">Últimos 3 meses</a>
                  <a class="dropdown-item chart-range" href="#" data-months="6">Últimos 6 meses</a>
                  <a class="dropdown-item chart-range" href="#" data-months="12">Último año</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#" id="downloadPdf">Descargar PDF</a>
                  <a class="dropdown-item" href="#" id="downloadCsv">Exportar datos (CSV)</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="chart-area">
                <canvas id="weightHistoryChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de historial de mediciones -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow mb-4">            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Historial de Mediciones</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Peso</th>
                      <th>IMC</th>
                      <th>Cambio</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $prevWeight = null;
                    // Mantener el orden original - de más reciente a más antiguo
                    foreach($data['measurements'] as $index => $measurement): 
                      // Calcular cambio de peso respecto a la medición anterior (que se muestra después)
                      $weightChange = '-';
                      
                      if ($index < count($data['measurements']) - 1) {
                        $prevMeasurement = $data['measurements'][$index + 1]; // Medición anterior (siguiente en la tabla)
                        $prevWeight = floatval($prevMeasurement->pes);
                        $currentWeight = floatval($measurement->pes);
                        
                        $diff = $currentWeight - $prevWeight;
                        $weightChangeClass = $diff < 0 ? 'text-success' : ($diff > 0 ? 'text-danger' : '');
                        $sign = $diff > 0 ? '+' : ''; // Signo positivo si es ganancia
                        $weightChange = '<span class="' . $weightChangeClass . '">' . $sign . number_format($diff, 1) . ' kg</span>';
                      }
                    ?>
                    <tr>
                      <td><?php echo date('d/m/Y', strtotime($measurement->data_mesura)); ?></td>
                      <td><?php echo number_format($measurement->pes, 1); ?> kg</td>
                      <td><?php echo number_format($measurement->imc, 1); ?></td>
                      <td><?php echo $weightChange; ?></td>
                      <td>
                        <button class="btn btn-sm btn-outline-danger btn-delete-measurement" data-id="<?php echo $measurement->seguiment_id; ?>">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data['measurements'])): ?>
                    <tr>
                      <td colspan="5" class="text-center">No hay mediciones registradas</td>
                    </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal de Nueva Medición -->
<div class="modal fade" id="newMeasurementModal" tabindex="-1" aria-labelledby="newMeasurementModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newMeasurementModalLabel">Nueva Medición</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="newMeasurementForm">
          <div class="mb-3">
            <label for="measurement-date" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="measurement-date" value="<?php echo date('Y-m-d'); ?>" disabled>
            <div class="form-text">La fecha se registrará automáticamente</div>
          </div>
          <div class="mb-3">
            <label for="measurement-weight" class="form-label">Peso (kg)</label>
            <input type="number" class="form-control" id="measurement-weight" name="weight" step="0.1" min="30" max="300" required>
          </div>
          <div class="mb-3">
            <label for="measurement-height" class="form-label">Altura (cm)</label>
            <input type="number" class="form-control" id="measurement-height" name="height" step="0.1" min="100" max="250" 
              value="<?php echo isset($data['lastMeasurement']->alcada) ? $data['lastMeasurement']->alcada : '175'; ?>">
            <div class="form-text">Solo necesitas indicar tu altura una vez, a menos que quieras actualizarla.</div>
          </div>
          <input type="hidden" id="measurement-id" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="saveMeasurement">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas eliminar esta medición? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <input type="hidden" id="delete-measurement-id" value="">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Definición de variables necesarias para el archivo JS -->
<script>
  // Variables para pasar al archivo JS
  const URLROOT = '<?= URLROOT ?>';
  const chartDataFromPhp = <?= json_encode($data['chartData']) ?>;
  const lastHeight = '<?= isset($data["lastMeasurement"]->alcada) ? $data["lastMeasurement"]->alcada : "175" ?>';
</script>

<!-- Enlazar archivo JavaScript específico -->
<script src="<?= URLROOT ?>/public/js/user/tracking.js"></script>
