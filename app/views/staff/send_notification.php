<?php
/**
 * Vista para enviar notificaciones a los usuarios del gimnasio
 * Permite al personal enviar mensajes y notificaciones a usuarios individuales o grupos
 */
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><?php echo $data['title']; ?></h1>
        </div>
        <div class="card-body">
            <?php if(isset($_SESSION['staff_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['staff_message_type']; ?> alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['staff_message']; 
                        unset($_SESSION['staff_message']);
                        unset($_SESSION['staff_message_type']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo URLROOT; ?>/staff/sendNotification" method="post" id="notificationForm">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Tipo de destinatarios</h5>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="recipientType" id="recipientTypeAll" value="all" checked>
                                    <label class="form-check-label" for="recipientTypeAll">Todos los usuarios</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="recipientType" id="recipientTypeSpecific" value="specific">
                                    <label class="form-check-label" for="recipientTypeSpecific">Usuarios específicos</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="recipientType" id="recipientTypeClass" value="class">
                                    <label class="form-check-label" for="recipientTypeClass">Participantes de una clase</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Selector de usuarios específicos (oculto por defecto) -->
                <div class="row mb-4 d-none" id="specificUsersSection">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Seleccionar usuarios</h5>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="userSearch" placeholder="Buscar usuario por nombre o email">
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover" id="usersTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                                    </div>
                                                </th>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Teléfono</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Aquí se cargarían los usuarios desde la base de datos -->
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input user-checkbox" type="checkbox" name="selectedUsers[]" value="101">
                                                    </div>
                                                </td>
                                                <td>101</td>
                                                <td>Ana García</td>
                                                <td>ana@example.com</td>
                                                <td>612345678</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input user-checkbox" type="checkbox" name="selectedUsers[]" value="102">
                                                    </div>
                                                </td>
                                                <td>102</td>
                                                <td>Juan Pérez</td>
                                                <td>juan@example.com</td>
                                                <td>698765432</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input user-checkbox" type="checkbox" name="selectedUsers[]" value="103">
                                                    </div>
                                                </td>
                                                <td>103</td>
                                                <td>María López</td>
                                                <td>maria@example.com</td>
                                                <td>654321987</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Selector de clases (oculto por defecto) -->
                <div class="row mb-4 d-none" id="classSection">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Seleccionar clase</h5>
                                <div class="mb-3">
                                    <select class="form-select" id="classSelect" name="classId">
                                        <option value="">Seleccione una clase</option>
                                        <option value="1">Spinning - Lunes 18:00</option>
                                        <option value="2">Yoga - Martes 10:00</option>
                                        <option value="3">Pilates - Miércoles 19:00</option>
                                        <option value="4">Zumba - Jueves 20:00</option>
                                        <option value="5">Crossfit - Viernes 17:30</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contenido de la notificación -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Contenido de la notificación</h5>
                                
                                <div class="mb-3">
                                    <label for="notificationType" class="form-label">Tipo de notificación</label>
                                    <select class="form-select" id="notificationType" name="notificationType" required>
                                        <option value="info">Informativa</option>
                                        <option value="warning">Advertencia</option>
                                        <option value="important">Importante</option>
                                        <option value="class_change">Cambio de clase</option>
                                        <option value="reminder">Recordatorio</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notificationTitle" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="notificationTitle" name="notificationTitle" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notificationContent" class="form-label">Mensaje</label>
                                    <textarea class="form-control" id="notificationContent" name="notificationContent" rows="5" required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="sendEmail" name="sendEmail" value="1">
                                        <label class="form-check-label" for="sendEmail">
                                            Enviar también por email
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-end">
                        <a href="<?php echo URLROOT; ?>/staff/dashboard" class="btn btn-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning">Enviar notificación</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestionar visualización de secciones según el tipo de destinatario
        const recipientTypeRadios = document.querySelectorAll('input[name="recipientType"]');
        const specificUsersSection = document.getElementById('specificUsersSection');
        const classSection = document.getElementById('classSection');
        
        recipientTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'specific') {
                    specificUsersSection.classList.remove('d-none');
                    classSection.classList.add('d-none');
                } else if (this.value === 'class') {
                    specificUsersSection.classList.add('d-none');
                    classSection.classList.remove('d-none');
                } else {
                    specificUsersSection.classList.add('d-none');
                    classSection.classList.add('d-none');
                }
            });
        });
        
        // Filtro de búsqueda de usuarios
        const userSearch = document.getElementById('userSearch');
        userSearch.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#usersTable tbody tr');
            
            tableRows.forEach(row => {
                const userName = row.cells[2].textContent.toLowerCase();
                const userEmail = row.cells[3].textContent.toLowerCase();
                
                if (userName.includes(searchValue) || userEmail.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Seleccionar/deseleccionar todos los usuarios
        const selectAllUsers = document.getElementById('selectAllUsers');
        selectAllUsers.addEventListener('change', function() {
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Validación del formulario
        const notificationForm = document.getElementById('notificationForm');
        notificationForm.addEventListener('submit', function(event) {
            const recipientType = document.querySelector('input[name="recipientType"]:checked').value;
            
            if (recipientType === 'specific') {
                const selectedUsers = document.querySelectorAll('input[name="selectedUsers[]"]:checked');
                if (selectedUsers.length === 0) {
                    event.preventDefault();
                    alert('Por favor, selecciona al menos un usuario para enviar la notificación.');
                }
            } else if (recipientType === 'class') {
                const selectedClass = document.getElementById('classSelect').value;
                if (!selectedClass) {
                    event.preventDefault();
                    alert('Por favor, selecciona una clase para enviar la notificación.');
                }
            }
        });
    });
</script>