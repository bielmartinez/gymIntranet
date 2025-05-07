<?php
/**
 * Vista que muestra el listado de rutinas asignadas al usuario
 */

// Definir el título de la página para el header
$pageTitle = isset($data['title']) ? $data['title'] : 'Mis Rutinas';

// Incluir el header principal
include_once APPROOT . '/views/shared/header/main.php';
?>

<style>
    .routine-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .routine-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .badge-status {
        font-weight: normal;
        padding: 5px 10px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?= $pageTitle ?></h1>
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

            <?php if (empty($data['routines'])): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No tienes rutinas asignadas actualmente. Contacta con el personal del gimnasio para que te asignen una rutina personalizada.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($data['routines'] as $routine): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card routine-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title"><?= htmlspecialchars($routine->nom) ?></h5>
                                    </div>
                                    <p class="card-text text-muted small">
                                        <i class="fas fa-calendar-alt me-1"></i> Creada el <?= date('d/m/Y', strtotime($routine->creat_el)) ?>
                                    </p>
                                    <p class="card-text">
                                        <?= !empty($routine->descripcio) ? nl2br(htmlspecialchars($routine->descripcio)) : 'Sin descripción' ?>
                                    </p>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="<?= URLROOT ?>/userRoutine/view/<?= $routine->rutina_id ?>" class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i> Ver detalles
                                        </a>
                                        <a href="<?= URLROOT ?>/userRoutine/downloadPDF/<?= $routine->rutina_id ?>" class="btn btn-success">
                                            <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                                        </a>
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

<?php
// Incluir el footer
include_once APPROOT . '/views/shared/footer/main.php';
?>