-- filepath: c:\xampp2\htdocs\gymIntranet\gymIntranet\add_role_column.sql
-- Añadir la columna role a la tabla usuaris
ALTER TABLE usuaris 
ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER cognoms;

-- Asegurarse de que todos los usuarios tienen un rol asignado
UPDATE usuaris SET role = 'user' WHERE role IS NULL;

-- Establecer el rol de administrador para el usuario con email a@a.com
UPDATE usuaris SET role = 'admin' WHERE correu = 'a@a.com';