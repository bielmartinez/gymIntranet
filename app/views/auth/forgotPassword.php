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
                    <h4 class="mb-0">Recuperar Contraseña</h4>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <p class="text-muted">Introduce tu correo electrónico para recibir un enlace de recuperación</p>
                    </div>
                    
                    <?php if(isset($_SESSION['forgot_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['forgot_message_type']; ?> alert-dismissible fade show" role="alert">
                            <?php 
                                echo $_SESSION['forgot_message']; 
                                unset($_SESSION['forgot_message']);
                                unset($_SESSION['forgot_message_type']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo URLROOT; ?>/auth/forgotPassword" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control <?php echo (isset($_SESSION['forgot_errors']['email_err'])) ? 'is-invalid' : ''; ?>" 
                                       id="email" name="email" placeholder="Introduce tu correo electrónico" 
                                       value="<?php echo isset($_SESSION['forgot_data']['email']) ? htmlspecialchars($_SESSION['forgot_data']['email']) : ''; ?>">
                                <?php if(isset($_SESSION['forgot_errors']['email_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $_SESSION['forgot_errors']['email_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Enviar Enlace</button>
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
