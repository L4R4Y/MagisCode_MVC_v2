-- MIGRACIÓN: estado "Cerrada" para evaluaciones
-- Ejecutar UNA VEZ en MySQL Workbench sobre la base `magiscode`.
--
-- Agrega el estado "Cerrada" (id=3) a la tabla estado_evaluacion.
-- Las evaluaciones cerradas no permiten agregar más preguntas.

USE magiscode;

INSERT INTO estado_evaluacion (id_estado, estado) VALUES (3, 'Cerrada')
    ON DUPLICATE KEY UPDATE estado = 'Cerrada';
