<?php 
// En modo desarrollo, permitimos el acceso directo
// Cuando la aplicación esté en producción, habilita este código:
/*
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . URLROOT);
    exit;
}
*/
?>

<!-- Incluir estilos específicos para la página de registro -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/register.css">

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Registrar Nuevo Usuario</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['register_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['register_message_type']; ?> alert-dismissible fade show" role="alert">
                            <?php 
                                echo $_SESSION['register_message']; 
                                unset($_SESSION['register_message']);
                                unset($_SESSION['register_message_type']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>                    <?php endif; ?>
                    
                    <form action="<?php echo URLROOT; ?>/admin/register" method="post" id="registerForm">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo (isset($_SESSION['register_errors']['fullName_err'])) ? 'is-invalid' : ''; ?>" id="fullName" name="fullName" placeholder="Nombre completo" value="<?php echo isset($_SESSION['register_data']['fullName']) ? htmlspecialchars($_SESSION['register_data']['fullName']) : ''; ?>" required>
                            <div class="invalid-feedback"><?php echo isset($_SESSION['register_errors']['fullName_err']) ? $_SESSION['register_errors']['fullName_err'] : ''; ?></div>
                        </div>
                        
                        <!-- Campo de usuario (username) -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo (isset($_SESSION['register_errors']['username_err'])) ? 'is-invalid' : ''; ?>" id="username" name="username" placeholder="Nombre de usuario" value="<?php echo isset($_SESSION['register_data']['username']) ? htmlspecialchars($_SESSION['register_data']['username']) : ''; ?>" required>
                            <div class="invalid-feedback"><?php echo isset($_SESSION['register_errors']['username_err']) ? $_SESSION['register_errors']['username_err'] : ''; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?php echo (isset($_SESSION['register_errors']['email_err'])) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="correo@ejemplo.com" value="<?php echo isset($_SESSION['register_data']['email']) ? htmlspecialchars($_SESSION['register_data']['email']) : ''; ?>" required>
                            <div class="invalid-feedback"><?php echo isset($_SESSION['register_errors']['email_err']) ? $_SESSION['register_errors']['email_err'] : ''; ?></div>
                        </div>                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <input type="password" class="form-control <?php echo (isset($_SESSION['register_errors']['password_err'])) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Contraseña (mínimo 8 caracteres)" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="invalid-feedback"><?php echo isset($_SESSION['register_errors']['password_err']) ? $_SESSION['register_errors']['password_err'] : ''; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirmPassword" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?php echo (isset($_SESSION['register_errors']['confirm_password_err'])) ? 'is-invalid' : ''; ?>" id="confirmPassword" name="confirmPassword" placeholder="Confirmar contraseña" required>
                                <div class="invalid-feedback"><?php echo isset($_SESSION['register_errors']['confirm_password_err']) ? $_SESSION['register_errors']['confirm_password_err'] : ''; ?></div>
                            </div>
                        </div>                        <div class="mb-3">
                            <label for="role" class="form-label">Rol de Usuario <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo (isset($_SESSION['register_errors']['role_err'])) ? 'is-invalid' : ''; ?>" id="role" name="role" required>
                                <option value="" disabled selected>Seleccionar rol</option>
                                <option value="user" <?php echo (isset($_SESSION['register_data']['role']) && $_SESSION['register_data']['role'] === 'user') ? 'selected' : ''; ?>>Usuario</option>
                                <option value="staff" <?php echo (isset($_SESSION['register_data']['role']) && $_SESSION['register_data']['role'] === 'staff') ? 'selected' : ''; ?>>Personal</option>
                                <option value="admin" <?php echo (isset($_SESSION['register_data']['role']) && $_SESSION['register_data']['role'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                            <div class="invalid-feedback"><?php echo isset($_SESSION['register_errors']['role_err']) ? $_SESSION['register_errors']['role_err'] : ''; ?></div>
                        </div>
                          <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Número de teléfono" value="">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="birthDate" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="birthDate" name="birthDate" value="">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="sendWelcomeEmail" name="sendWelcomeEmail" checked>
                            <label class="form-check-label" for="sendWelcomeEmail">
                                Enviar correo de bienvenida con las credenciales
                            </label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir JavaScript específico para la página de registro -->
<script src="<?php echo URLROOT; ?>/public/js/admin/register.js"></script>
