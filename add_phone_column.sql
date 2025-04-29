-- Script para añadir la columna phone a la tabla usuaris
ALTER TABLE usuaris ADD COLUMN phone VARCHAR(20) NULL COMMENT 'Teléfono del usuario';