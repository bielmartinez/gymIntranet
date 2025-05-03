<?php
/**
 * Vista para editar usuario (para administradores)
 * Permite modificar todos los datos del usuario
 */
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h1 class="h3 mb-0"><?php echo $data['title']; ?></h1>
        </div>
        <div class="card-body">
            <?php if(isset($_SESSION['admin_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['admin_message_type']; ?> alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['admin_message']; 
                        unset($_SESSION['admin_message']);
                        unset($_SESSION['admin_message_type']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo URLROOT; ?>/admin/updateUser" method="post">
                <input type="hidden" name="user_id" value="<?php echo $data['user']['id']; ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fullName" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control <?php echo isset($data['errors']['fullName_err']) ? 'is-invalid' : ''; ?>" 
                            id="fullName" name="fullName" value="<?php echo $data['user']['fullName']; ?>" required>
                        <div class="invalid-feedback">
                            <?php echo isset($data['errors']['fullName_err']) ? $data['errors']['fullName_err'] : ''; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control <?php echo isset($data['errors']['email_err']) ? 'is-invalid' : ''; ?>" 
                            id="email" name="email" value="<?php echo $data['user']['email']; ?>" required>
                        <div class="invalid-feedback">
                            <?php echo isset($data['errors']['email_err']) ? $data['errors']['email_err'] : ''; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nueva contraseña (dejar en blanco para mantener la actual)</label>
                        <input type="password" class="form-control <?php echo isset($data['errors']['password_err']) ? 'is-invalid' : ''; ?>" 
                            id="password" name="password">
                        <div class="invalid-feedback">
                            <?php echo isset($data['errors']['password_err']) ? $data['errors']['password_err'] : ''; ?>
                        </div>
                        <div class="form-text">Mínimo 8 caracteres</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" class="form-control <?php echo isset($data['errors']['confirm_password_err']) ? 'is-invalid' : ''; ?>" 
                            id="confirm_password" name="confirm_password">
                        <div class="invalid-feedback">
                            <?php echo isset($data['errors']['confirm_password_err']) ? $data['errors']['confirm_password_err'] : ''; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select <?php echo isset($data['errors']['role_err']) ? 'is-invalid' : ''; ?>" 
                            id="role" name="role" required>
                            <option value="user" <?php echo $data['user']['role'] === 'user' ? 'selected' : ''; ?>>Usuario</option>
                            <option value="staff" <?php echo $data['user']['role'] === 'staff' ? 'selected' : ''; ?>>Personal</option>
                            <option value="admin" <?php echo $data['user']['role'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                        <div class="invalid-feedback">
                            <?php echo isset($data['errors']['role_err']) ? $data['errors']['role_err'] : ''; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control <?php echo isset($data['errors']['phone_err']) ? 'is-invalid' : ''; ?>" 
                            id="phone" name="phone" value="<?php echo $data['user']['phone'] ?? ''; ?>">
                        <div class="invalid-feedback">
                            <?php echo isset($data['errors']['phone_err']) ? $data['errors']['phone_err'] : ''; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="birthDate" class="form-label">Fecha de nacimiento</label>
                        <input type="date" class="form-control <?php echo isset($data['errors']['birthDate_err']) ? 'is-invalid' : ''; ?>" 
                            id="birthDate" name="birthDate" value="<?php echo $data['user']['birthDate'] ?? ''; ?>">
                        <div class="invalid-feedback">
                            <?php echo isset($data['errors']['birthDate_err']) ? $data['errors']['birthDate_err'] : ''; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                        <?php echo $data['user']['isActive'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Usuario activo</label>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-secondary">Volver</a>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>