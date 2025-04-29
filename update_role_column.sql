-- Modificar la columna de rol para ser consistente con el código PHP
ALTER TABLE usuaris 
CHANGE COLUMN rol role VARCHAR(20) DEFAULT 'user';

-- Asegurarse de que todos los usuarios tienen un rol asignado
UPDATE usuaris SET role = 'user' WHERE role IS NULL;