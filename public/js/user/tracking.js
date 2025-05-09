// Script para la página de seguimiento físico
document.addEventListener('DOMContentLoaded', function() {
  // Preparar datos para el gráfico
  const chartData = chartDataFromPhp;
  
  // Extraer fechas y pesos
  const labels = chartData.map(item => item.fecha_formateada);
  const weights = chartData.map(item => parseFloat(item.pes));
  
  // Configurar gráfico de peso
  const weightCtx = document.getElementById('weightHistoryChart').getContext('2d');
  const weightHistoryChart = new Chart(weightCtx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Peso (kg)',
        data: weights,
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
  
  // Manejar cambio de rango para el gráfico
  document.querySelectorAll('.chart-range').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const months = parseInt(this.getAttribute('data-months'));
      
      // Hacer petición AJAX para obtener datos del nuevo rango
      fetch(`${URLROOT}/user/getTrackingChartData/${months}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ months: months })
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          const newChartData = result.data;
          
          // Actualizar datos del gráfico
          weightHistoryChart.data.labels = newChartData.map(item => item.fecha_formateada);
          weightHistoryChart.data.datasets[0].data = newChartData.map(item => parseFloat(item.pes));
          
          // Actualizar gráfico
          weightHistoryChart.update();
        } else {
          showAlert('Error al cargar datos', 'danger');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
      });
    });
  });

  // Manejar guardado de mediciones
  document.getElementById('saveMeasurement').addEventListener('click', function() {
    const weight = document.getElementById('measurement-weight').value;
    const height = document.getElementById('measurement-height').value;
    const measurementId = document.getElementById('measurement-id').value;
    
    if (!weight || !height) {
      showAlert('El peso y la altura son obligatorios', 'danger');
      return;
    }
    
    // Enviar datos para guardar
    fetch(`${URLROOT}/user/addMeasurement`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        weight: weight,
        height: height,
        measurementId: measurementId
      })
    })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('newMeasurementModal'));
        modal.hide();
        
        // Mostrar mensaje de éxito
        showAlert('Medición guardada correctamente', 'success');
        
        // Recargar la página para mostrar los nuevos datos
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      } else {
        showAlert(result.message || 'Error al guardar la medición', 'danger');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showAlert('Error de conexión', 'danger');
    });
  });
  
  // Manejar botones de eliminación (preparar modal)
  document.querySelectorAll('.btn-delete-measurement').forEach(button => {
    button.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      document.getElementById('delete-measurement-id').value = id;
      
      // Mostrar el modal de confirmación
      const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
      modal.show();
    });
  });
  
  // Manejar confirmación de eliminación
  document.getElementById('confirmDelete').addEventListener('click', function() {
    const id = document.getElementById('delete-measurement-id').value;
    
    // Enviar solicitud para eliminar
    fetch(`${URLROOT}/user/deleteMeasurement`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        measurementId: id
      })
    })
    .then(response => response.json())
    .then(result => {
      // Cerrar modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
      modal.hide();
      
      if (result.success) {
        showAlert('Medición eliminada correctamente', 'success');
        // Recargar la página para actualizar los datos
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      } else {
        showAlert(result.message || 'Error al eliminar la medición', 'danger');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showAlert('Error de conexión', 'danger');
    });
  });

  // Función para mostrar alertas
  function showAlert(mensaje, tipo) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
      <strong>${tipo === 'success' ? '¡Éxito!' : '¡Error!'}</strong> ${mensaje}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(alertDiv);
    
    // Eliminar la alerta después de 3 segundos
    setTimeout(() => {
      alertDiv.remove();
    }, 3000);
  }
  
  // Reiniciar modal al cerrar
  document.getElementById('newMeasurementModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('newMeasurementForm').reset();
    document.getElementById('measurement-id').value = '';
    document.getElementById('newMeasurementModalLabel').textContent = 'Nueva Medición';
    
    // Restaurar altura por defecto si hay una última medición
    document.getElementById('measurement-height').value = lastHeight;
  });
});
