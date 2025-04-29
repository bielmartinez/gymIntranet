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
                    <h4 class="mb-0">Acceso a Gym Intranet</h4>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="<?php echo URLROOT; ?>/public/images/logo.png" alt="Gym Intranet Logo" width="120" class="mb-4">
                        <p class="text-muted">Introduce tus credenciales para acceder</p>
                    </div>
                    <?php if(isset($_SESSION['login_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['login_message_type']; ?> alert-dismissible fade show" role="alert">
                            <?php 
                                echo $_SESSION['login_message']; 
                                unset($_SESSION['login_message']);
                                unset($_SESSION['login_message_type']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                      
                    <form action="<?php echo URLROOT; ?>/auth/login" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control <?php echo (isset($_SESSION['login_errors']['email_err'])) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="Introduce tu correo electrónico" value="<?php echo isset($_SESSION['login_data']['email']) ? htmlspecialchars($_SESSION['login_data']['email']) : ''; ?>">
                                <?php if(isset($_SESSION['login_errors']['email_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $_SESSION['login_errors']['email_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control <?php echo (isset($_SESSION['login_errors']['password_err'])) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Introduce tu contraseña">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <?php if(isset($_SESSION['login_errors']['password_err'])): ?>
                                <div class="invalid-feedback"><?php echo $_SESSION['login_errors']['password_err']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                            <div>
                                <a href="<?php echo URLROOT; ?>/auth/forgotPassword" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn" style="background-color: #150000; color: #fff;">Iniciar Sesión</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <p class="text-muted mb-0">¿Problemas para acceder? Contacta con recepción</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        // Toggle password visibility
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle eye icon
        const eyeIcon = this.querySelector('i');
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });
});
</script>
