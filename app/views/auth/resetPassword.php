<?php 
// Definir el directorio base para las inclusiones
$base_url = "/gymIntranet/gymIntranet";
$base_dir = $_SERVER['DOCUMENT_ROOT'] . $base_url;
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Restablecer Contraseña</h4>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <p class="text-muted">Introduce tu nueva contraseña</p>
                    </div>

                    <?php if(isset($_SESSION['reset_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['reset_message_type']; ?> alert-dismissible fade show" role="alert">
                            <?php 
                                echo $_SESSION['reset_message']; 
                                unset($_SESSION['reset_message']);
                                unset($_SESSION['reset_message_type']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo URLROOT; ?>/auth/resetPassword" method="post">
                        <input type="hidden" name="token" value="<?php echo isset($data['token']) ? htmlspecialchars($data['token']) : ''; ?>">
                        
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control <?php echo (isset($_SESSION['reset_errors']['password_err'])) ? 'is-invalid' : ''; ?>" 
                                       id="newPassword" name="newPassword" placeholder="Introduce tu nueva contraseña">
                                <?php if(isset($_SESSION['reset_errors']['password_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $_SESSION['reset_errors']['password_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control <?php echo (isset($_SESSION['reset_errors']['confirm_password_err'])) ? 'is-invalid' : ''; ?>" 
                                       id="confirmPassword" name="confirmPassword" placeholder="Confirma tu nueva contraseña">
                                <?php if(isset($_SESSION['reset_errors']['confirm_password_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $_SESSION['reset_errors']['confirm_password_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Restablecer Contraseña</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <p class="text-muted mb-0">¿Recordaste tu contraseña? <a href="<?php echo URLROOT; ?>/auth/login" class="text-decoration-none">Inicia sesión</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(event) {
        if (newPassword.value !== confirmPassword.value) {
            event.preventDefault();
            alert('Las contraseñas no coinciden');
        }
    });
});
</script>
