<?php
/**
 * Vista para crear una nueva rutina por parte del staff
 */

// Definir el título de la página para el header
$pageTitle = isset($data['title']) ? $data['title'] : 'Crear Nueva Rutina';

// Incluir el header principal
include_once APPROOT . '/views/shared/header/main.php';
?>

<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .card-shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
</style>

<div class="row">
    <div class="col-12 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><?= $pageTitle ?></h1>
            <div>
                <a href="<?= URLROOT ?>/staffRoutine" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver a rutinas
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

        <div class="form-container">
            <div class="card card-shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información de la Rutina</h6>
                </div>
                <div class="card-body">
                    <form action="<?= URLROOT ?>/staffRoutine/create" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la rutina <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <div class="form-text">Asigna un nombre descriptivo a la rutina</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="usuario_id" class="form-label">Usuario asignado <span class="text-danger">*</span></label>
                            <select class="form-select" id="usuario_id" name="usuari_id" required>
                                <option value="" selected disabled>Selecciona un usuario</option>
                                <?php foreach ($data['users'] as $usuario): ?>
                                    <option value="<?= $usuario->id ?>"><?= htmlspecialchars($usuario->fullName) ?> (<?= htmlspecialchars($usuario->email) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Usuario al que se asignará esta rutina</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Describe brevemente la rutina, objetivos, duración recomendada, etc."></textarea>
                            <div class="form-text">Información adicional sobre la rutina</div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-undo me-1"></i> Restablecer
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Rutina
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include_once APPROOT . '/views/shared/footer/main.php';
?>