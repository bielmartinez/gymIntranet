-- Añadir columnas para gestión de recuperación de contraseña
-- Para la nueva versión del proyecto llamada "Gym Intranet"
ALTER TABLE usuaris 
ADD COLUMN reset_token VARCHAR(100) NULL,
ADD COLUMN reset_token_expiration DATETIME NULL;

-- Añadir un índice para mejorar búsquedas por token
ALTER TABLE usuaris
ADD INDEX idx_reset_token (reset_token);