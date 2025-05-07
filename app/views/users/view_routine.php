<?php
/**
 * Vista que muestra el detalle de una rutina asignada al usuario
 */

// Definir el título de la página para el header
$pageTitle = isset($data['title']) ? $data['title'] : 'Detalle de Rutina';

// Incluir el header principal
include_once APPROOT . '/views/shared/header/main.php';
?>

<style>
    .exercise-card {
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 20px;
    }
    .exercise-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .exercise-icon {
        height: 150px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fa;
    }
    .order-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?= htmlspecialchars($data['routine']->nom) ?></h1>
                <div>
                    <a href="<?= URLROOT ?>/userRoutine" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Volver a mis rutinas
                    </a>
                    <a href="<?= URLROOT ?>/userRoutine/downloadPDF/<?= $data['routine']->rutina_id ?>" class="btn btn-success">
                        <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                    </a>
                </div>
            </div>

            <?php if(isset($_SESSION['routine_message'])): ?>
                <div class="alert alert-<?= $_SESSION['routine_message_type'] ?> alert-dismissible fade show">
                    <?= $_SESSION['routine_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                    unset($_SESSION['routine_message']);
                    unset($_SESSION['routine_message_type']);
                ?>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información de la Rutina</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Fecha de creación:</strong> <?= date('d/m/Y', strtotime($data['routine']->creat_el)) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Creada por:</strong> <?= isset($data['staffName']) ? htmlspecialchars($data['staffName']) : 'Entrenador del gimnasio' ?></p>
                        </div>
                    </div>
                    <?php if (!empty($data['routine']->descripcio)): ?>
                        <div class="mb-3">
                            <h6>Descripción:</h6>
                            <p><?= nl2br(htmlspecialchars($data['routine']->descripcio)) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sección de ejercicios -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ejercicios de la Rutina</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($data['exercises'])): ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            Esta rutina aún no tiene ejercicios asignados. Contacta con el personal del gimnasio.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($data['exercises'] as $exercise): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="card exercise-card position-relative h-100">
                                        <div class="order-badge"><?= $exercise->ordre ?></div>
                                        
                                        <div class="exercise-icon">
                                            <i class="fas fa-dumbbell fa-4x text-secondary"></i>
                                        </div>
                                        
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($exercise->nom) ?></h5>
                                            <div class="card-text small text-muted mb-3">
                                                <div class="row">
                                                    <div class="col-4 text-center border-end">
                                                        <strong><?= $exercise->series ?></strong>
                                                        <div>Series</div>
                                                    </div>
                                                    <div class="col-4 text-center border-end">
                                                        <strong><?= $exercise->repeticions ?></strong>
                                                        <div>Reps</div>
                                                    </div>
                                                    <div class="col-4 text-center">
                                                        <strong><?= $exercise->descans ?>s</strong>
                                                        <div>Descanso</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="card-text exercise-description-truncate">
                                                <?= nl2br(htmlspecialchars($exercise->descripcio)) ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Modal de detalles del ejercicio -->
                                    <div class="modal fade" id="exerciseDetailModal<?= $exercise->exercici_id ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title"><?= htmlspecialchars($exercise->nom) ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="text-center p-5 bg-light rounded mb-3">
                                                                <i class="fas fa-dumbbell fa-5x text-secondary"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Detalles del ejercicio:</h6>
                                                            <ul class="list-group mb-3">
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Series
                                                                    <span class="badge bg-primary rounded-pill"><?= $exercise->series ?></span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Repeticiones
                                                                    <span class="badge bg-info rounded-pill"><?= $exercise->repeticions ?></span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Descanso
                                                                    <span class="badge bg-secondary rounded-pill"><?= $exercise->descans ?> segundos</span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Orden en rutina
                                                                    <span class="badge bg-dark rounded-pill"><?= $exercise->ordre ?></span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-3">
                                                        <h6>Descripción:</h6>
                                                        <p><?= nl2br(htmlspecialchars($exercise->descripcio)) ?></p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include_once APPROOT . '/views/shared/footer/main.php';
?>