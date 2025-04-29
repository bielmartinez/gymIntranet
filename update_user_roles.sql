-- Añadir columna de rol a la tabla de usuarios
-- Para la nueva versión del proyecto llamada "Gym Intranet"
ALTER TABLE usuaris 
ADD COLUMN rol VARCHAR(20) DEFAULT 'user';

-- Actualizar roles existentes a 'user' por defecto
UPDATE usuaris SET rol = 'user' WHERE rol IS NULL;