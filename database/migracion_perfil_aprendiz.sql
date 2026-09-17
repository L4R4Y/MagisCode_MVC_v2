-- MIGRACIÓN: soporte para edición de perfil del Aprendiz
-- Ejecutar UNA VEZ en MySQL Workbench sobre la base `magiscode`,
-- igual que se hizo con migracion_hashes_prueba.sql.
--
-- Agrega la columna `celular`, que faltaba en el esquema original,
-- para que el Aprendiz pueda mantenerla actualizada desde su perfil.

USE magiscode;

ALTER TABLE usuario
    ADD COLUMN celular VARCHAR(20) NULL AFTER correo;
