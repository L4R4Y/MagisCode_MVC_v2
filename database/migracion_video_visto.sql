-- MIGRACIÓN: seguimiento de videos vistos por estudiantes
-- Ejecutar UNA VEZ en MySQL Workbench sobre la base `magiscode`.
--
-- Crea la tabla recurso_visto para registrar cuándo un aprendiz
-- ha completado la visualización de un recurso MP4.
-- También crea un índice único para evitar duplicados.

USE magiscode;

CREATE TABLE recurso_visto (
    id_registro INT NOT NULL AUTO_INCREMENT,
    id_recurso INT NOT NULL,
    id_usuario BIGINT NOT NULL,
    fecha_visto DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_registro),
    UNIQUE KEY uniq_recurso_usuario (id_recurso, id_usuario),
    CONSTRAINT recurso_visto_ibfk_1
        FOREIGN KEY (id_recurso) REFERENCES recurso (id_recurso)
        ON DELETE CASCADE,
    CONSTRAINT recurso_visto_ibfk_2
        FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
