<?php

require_once __DIR__ . '/Model.php';

class Contenido extends Model
{
    public function modulos(int $cursoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM modulo WHERE id_curso_m = ? ORDER BY orden_modulo, id_modulo'
        );
        $stmt->execute([$cursoId]);
        $modulos = $stmt->fetchAll();

        foreach ($modulos as &$modulo) {
            $modulo['lecciones'] = $this->lecciones((int) $modulo['id_modulo']);
        }

        return $modulos;
    }

    public function lecciones(int $moduloId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM leccion WHERE id_modulo_l = ? ORDER BY orden_leccion, id_leccion'
        );
        $stmt->execute([$moduloId]);
        $lecciones = $stmt->fetchAll();

        foreach ($lecciones as &$leccion) {
            $leccion['recursos'] = $this->recursos((int) $leccion['id_leccion']);
        }

        return $lecciones;
    }

    public function recursos(int $leccionId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM recurso WHERE id_leccion_r = ? ORDER BY id_recurso'
        );
        $stmt->execute([$leccionId]);

        return $stmt->fetchAll();
    }

    public function crearModulo(array $datos): int
    {
        $datos = Normalizador::campos($datos, ['nombre']);
        $stmt = $this->db->prepare(
            'INSERT INTO modulo (nombre_modulo, orden_modulo, id_curso_m) VALUES (?, ?, ?)'
        );
        $stmt->execute([$datos['nombre'], $datos['orden'], $datos['curso']]);

        return (int) $this->db->lastInsertId();
    }

    public function crearLeccion(array $datos): int
    {
        $datos = Normalizador::campos($datos, ['titulo']);
        $stmt = $this->db->prepare(
            'INSERT INTO leccion (titulo_leccion, orden_leccion, id_modulo_l) VALUES (?, ?, ?)'
        );
        $stmt->execute([$datos['titulo'], $datos['orden'], $datos['modulo']]);

        return (int) $this->db->lastInsertId();
    }

    public function crearRecurso(array $datos): int
    {
        $datos = Normalizador::campos($datos, ['nombre']);
        $stmt = $this->db->prepare(
            'INSERT INTO recurso (nombre_recurso, tipo_recurso, ruta_archivo, duracion, id_leccion_r)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $datos['nombre'],
            $datos['tipo'],
            $datos['ruta'],
            $datos['duracion'] ?? 0,
            $datos['leccion'],
        ]);

        return (int) $this->db->lastInsertId();
    }
}
