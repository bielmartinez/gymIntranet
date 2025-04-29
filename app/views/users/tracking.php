<?php 
// El header y footer ahora son incluidos por direct-view.php
// No necesitamos incluirlos aquí
// Solo mantenemos el logger para registro de actividad
include_once __DIR__ . '/../../utils/Logger.php';
Logger::log('INFO', 'Acceso a tracking.php');
?>

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
        <!-- Current Stats Card -->
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
                  <div class="h5 mb-0 font-weight-bold text-gray-800">74.8 kg</div>
                </div>
              </div>
              <div class="row no-gutters align-items-center mb-3">
                <div class="col-auto me-3">
                  <i class="fas fa-ruler-vertical fa-2x text-gray-300"></i>
                </div>
                <div class="col">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Altura</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">175 cm</div>
                </div>
              </div>
              <div class="row no-gutters align-items-center mb-3">
                <div class="col-auto me-3">
                  <i class="fas fa-calculator fa-2x text-gray-300"></i>
                </div>
                <div class="col">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">IMC</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">24.4</div>
                  <div class="small text-success">Normal</div>
                </div>
              </div>
              <div class="row no-gutters align-items-center">
                <div class="col-auto me-3">
                  <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                </div>
                <div class="col">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Última Actualización</div>
                  <div class="small text-gray-800">05 de Abril, 2025</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Weight Chart Card -->
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
                  <a class="dropdown-item" href="#" id="month3">Últimos 3 meses</a>
                  <a class="dropdown-item" href="#" id="month6">Últimos 6 meses</a>
                  <a class="dropdown-item" href="#" id="year1">Último año</a>
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

      <!-- Progress & Goals -->
      <div class="row mb-4">
        <div class="col-lg-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Mi Progreso</h6>
            </div>
            <div class="card-body">
              <h4 class="small font-weight-bold">Objetivo de peso <span class="float-end">80%</span></h4>
              <div class="progress mb-4">
                <div class="progress-bar bg-success" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <h4 class="small font-weight-bold">Asistencia semanal <span class="float-end">60%</span></h4>
              <div class="progress mb-4">
                <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <h4 class="small font-weight-bold">Calorías diarias <span class="float-end">40%</span></h4>
              <div class="progress mb-4">
                <div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="d-grid mt-4">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#setGoalsModal">Ajustar Objetivos</button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Historial de Mediciones</h6>
              <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-sort fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                  <div class="dropdown-header">Ordenar por:</div>
                  <a class="dropdown-item" href="#">Más reciente</a>
                  <a class="dropdown-item" href="#">Más antiguo</a>
                </div>
              </div>
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
                    <tr>
                      <td>05/04/2025</td>
                      <td>74.8 kg</td>
                      <td>24.4</td>
                      <td><span class="text-success">-0.4 kg</span></td>
                      <td>
                        <button class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td>27/03/2025</td>
                      <td>75.2 kg</td>
                      <td>24.6</td>
                      <td><span class="text-success">-1.3 kg</span></td>
                      <td>
                        <button class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td>15/02/2025</td>
                      <td>76.5 kg</td>
                      <td>25.0</td>
                      <td><span class="text-success">-1.5 kg</span></td>
                      <td>
                        <button class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td>05/01/2025</td>
                      <td>78.0 kg</td>
                      <td>25.5</td>
                      <td>-</td>
                      <td>
                        <button class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- BMI Chart -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Evolución del IMC</h6>
            </div>
            <div class="card-body">
              <div class="chart-area">
                <canvas id="bmiChart"></canvas>
              </div>
              <div class="mt-4 text-center small">
                <span class="me-3">
                  <i class="fas fa-circle text-danger"></i> Obeso
                </span>
                <span class="me-3">
                  <i class="fas fa-circle text-warning"></i> Sobrepeso
                </span>
                <span class="me-3">
                  <i class="fas fa-circle text-success"></i> Normal
                </span>
                <span class="me-3">
                  <i class="fas fa-circle text-info"></i> Bajo peso
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- New Measurement Modal -->
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
            <input type="date" class="form-control" id="measurement-date" value="<?php echo date('Y-m-d'); ?>">
          </div>
          <div class="mb-3">
            <label for="measurement-weight" class="form-label">Peso (kg)</label>
            <input type="number" class="form-control" id="measurement-weight" step="0.1" min="30" max="300" required>
          </div>
          <div class="mb-3">
            <label for="measurement-height" class="form-label">Altura (cm)</label>
            <input type="number" class="form-control" id="measurement-height" step="0.1" min="100" max="250" value="175">
            <div class="form-text">Solo necesitas indicar tu altura una vez, a menos que quieras actualizarla.</div>
          </div>
          <div class="mb-3">
            <label for="measurement-notes" class="form-label">Notas (opcional)</label>
            <textarea class="form-control" id="measurement-notes" rows="3" placeholder="Añade notas sobre tu progreso, dieta, entrenamiento..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="saveMeasurement">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Set Goals Modal -->
<div class="modal fade" id="setGoalsModal" tabindex="-1" aria-labelledby="setGoalsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="setGoalsModalLabel">Mis Objetivos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="goalsForm">
          <div class="mb-3">
            <label for="goal-weight" class="form-label">Peso Objetivo (kg)</label>
            <input type="number" class="form-control" id="goal-weight" step="0.1" min="30" max="300" value="70">
          </div>
          <div class="mb-3">
            <label for="goal-weekly-sessions" class="form-label">Sesiones semanales</label>
            <select class="form-select" id="goal-weekly-sessions">
              <option value="2">2 sesiones</option>
              <option value="3">3 sesiones</option>
              <option value="4">4 sesiones</option>
              <option value="5" selected>5 sesiones</option>
              <option value="6">6 sesiones</option>
              <option value="7">7 sesiones</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="goal-calories" class="form-label">Calorías diarias objetivo</label>
            <input type="number" class="form-control" id="goal-calories" step="50" min="1000" max="5000" value="2000">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="saveGoals">Guardar Objetivos</button>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Weight History Chart
    const weightCtx = document.getElementById('weightHistoryChart').getContext('2d');
    const weightHistoryChart = new Chart(weightCtx, {
      type: 'line',
      data: {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril'],
        datasets: [{
          label: 'Peso (kg)',
          data: [78, 76.5, 75.2, 74.8],
          backgroundColor: 'rgba(78, 115, 223, 0.05)',
          borderColor: 'rgba(78, 115, 223, 1)',
          pointRadius: 3,
          pointBackgroundColor: 'rgba(78, 115, 223, 1)',
          pointBorderColor: 'rgba(78, 115, 223, 1)',
          pointHoverRadius: 5,
          pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
          pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
          pointHitRadius: 10,
          pointBorderWidth: 2,
          lineTension: 0.3
        }, {
          label: 'Peso Objetivo',
          data: [78, 75, 73, 70],
          backgroundColor: 'rgba(28, 200, 138, 0.05)',
          borderColor: 'rgba(28, 200, 138, 1)',
          borderDash: [5, 5],
          pointRadius: 0,
          borderWidth: 2,
          lineTension: 0.3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
          }
        },
        scales: {
          y: {
            beginAtZero: false,
            grid: {
              color: "rgb(234, 236, 244)",
              zeroLineColor: "rgb(234, 236, 244)",
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 5,
              padding: 10
            }
          },
          x: {
            grid: {
              display: false,
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 7,
              padding: 10
            }
          }
        },
        plugins: {
          legend: {
            display: true
          },
          tooltip: {
            backgroundColor: "rgb(255,255,255)",
            bodyColor: "#858796",
            titleMarginBottom: 10,
            titleColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            padding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10
          }
        }
      }
    });

    // BMI Chart
    const bmiCtx = document.getElementById('bmiChart').getContext('2d');
    const bmiChart = new Chart(bmiCtx, {
      type: 'line',
      data: {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril'],
        datasets: [{
          label: 'IMC',
          data: [25.5, 25.0, 24.6, 24.4],
          backgroundColor: 'rgba(78, 115, 223, 0.05)',
          borderColor: 'rgba(78, 115, 223, 1)',
          pointRadius: 3,
          pointBackgroundColor: 'rgba(78, 115, 223, 1)',
          pointBorderColor: 'rgba(78, 115, 223, 1)',
          pointHoverRadius: 5,
          pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
          pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
          pointHitRadius: 10,
          pointBorderWidth: 2,
          lineTension: 0.3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
          }
        },
        scales: {
          y: {
            beginAtZero: false,
            grid: {
              color: "rgb(234, 236, 244)",
              zeroLineColor: "rgb(234, 236, 244)",
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 5,
              padding: 10
            }
          },
          x: {
            grid: {
              display: false,
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 7,
              padding: 10
            }
          }
        },
        plugins: {
          annotation: {
            annotations: {
              underweight: {
                type: 'box',
                yMin: 0,
                yMax: 18.5,
                backgroundColor: 'rgba(54, 185, 204, 0.1)',
                borderColor: 'transparent'
              },
              normal: {
                type: 'box',
                yMin: 18.5,
                yMax: 25,
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderColor: 'transparent'
              },
              overweight: {
                type: 'box',
                yMin: 25,
                yMax: 30,
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                borderColor: 'transparent'
              },
              obese: {
                type: 'box',
                yMin: 30,
                yMax: 40,
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                borderColor: 'transparent'
              }
            }
          },
          legend: {
            display: true
          },
          tooltip: {
            backgroundColor: "rgb(255,255,255)",
            bodyColor: "#858796",
            titleMarginBottom: 10,
            titleColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            padding: 15,
            displayColors: false,
            callbacks: {
              label: function(context) {
                let value = context.raw;
                let bmiCategory = '';
                
                if (value < 18.5) {
                  bmiCategory = 'Bajo peso';
                } else if (value < 25) {
                  bmiCategory = 'Normal';
                } else if (value < 30) {
                  bmiCategory = 'Sobrepeso';
                } else {
                  bmiCategory = 'Obesidad';
                }
                
                return `IMC: ${value} (${bmiCategory})`;
              }
            }
          }
        }
      }
    });

    // Handle save measurement 
    document.getElementById('saveMeasurement').addEventListener('click', function() {
      // Here you would save the measurement to the server via AJAX
      const modal = bootstrap.Modal.getInstance(document.getElementById('newMeasurementModal'));
      
      // Show a success notification
      const successAlert = document.createElement('div');
      successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
      successAlert.setAttribute('role', 'alert');
      successAlert.innerHTML = `
        <strong>¡Medición guardada!</strong> Tus datos se han actualizado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      document.body.appendChild(successAlert);
      
      // Close the modal
      modal.hide();
      
      // Remove the alert after 3 seconds
      setTimeout(() => {
        successAlert.remove();
      }, 3000);
    });

    // Handle save goals
    document.getElementById('saveGoals').addEventListener('click', function() {
      // Here you would save the goals to the server via AJAX
      const modal = bootstrap.Modal.getInstance(document.getElementById('setGoalsModal'));
      
      // Show a success notification
      const successAlert = document.createElement('div');
      successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
      successAlert.setAttribute('role', 'alert');
      successAlert.innerHTML = `
        <strong>¡Objetivos actualizados!</strong> Tus objetivos se han guardado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      document.body.appendChild(successAlert);
      
      // Close the modal
      modal.hide();
      
      // Remove the alert after 3 seconds
      setTimeout(() => {
        successAlert.remove();
      }, 3000);
    });
  });
</script>
