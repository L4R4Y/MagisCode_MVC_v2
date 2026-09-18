-- MIGRACIÓN: duración de recursos de video y duración total del curso
-- Ejecutar UNA VEZ en MySQL Workbench sobre la base `magiscode`.
--
-- Agrega la columna `duracion` a la tabla recurso para almacenar
-- la duración en segundos de los archivos MP4, y la columna
-- `duracion_total` a la tabla curso para almacenar la suma de todas
-- las duraciones de recursos MP4 del curso.

USE magiscode;

ALTER TABLE recurso
    ADD COLUMN duracion INT NOT NULL DEFAULT 0 AFTER id_leccion_r;

ALTER TABLE curso
    ADD COLUMN duracion_total INT NOT NULL DEFAULT 0 AFTER estado_curso;

-- Populate duracion_total from existing MP4 resources
UPDATE curso c
SET duracion_total = COALESCE((
    SELECT SUM(r.duracion)
    FROM recurso r
    JOIN leccion l ON l.id_leccion = r.id_leccion_r
    JOIN modulo m ON m.id_modulo = l.id_modulo_l
    WHERE m.id_curso_m = c.id_curso
), 0);
