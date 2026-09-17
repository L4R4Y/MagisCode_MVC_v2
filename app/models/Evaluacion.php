<?php

require_once __DIR__ . '/Model.php';

class Evaluacion extends Model
{
    public function uno(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM evaluacion WHERE id_evaluacion = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Evaluación junto con el título del curso al que pertenece.
     */
    public function conCurso(int $id): ?array
    {
        $sql = 'SELECT e.*, c.titulo_curso
                FROM evaluacion e
                JOIN curso c ON c.id_curso = e.id_curso_e
                WHERE e.id_evaluacion = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Id del curso al que pertenece una evaluación.
     */
    public function cursoDe(int $id): int
    {
        $stmt = $this->db->prepare('SELECT id_curso_e FROM evaluacion WHERE id_evaluacion = ?');
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn();
    }

    public function porCurso(int $cursoId): array
    {
        $sql = 'SELECT e.*, ee.estado,
                       (SELECT COUNT(*) FROM pregunta p WHERE p.id_evaluacion_p = e.id_evaluacion) preguntas
                FROM evaluacion e
                JOIN estado_evaluacion ee ON ee.id_estado = e.id_estado_e
                WHERE e.id_curso_e = ?
                ORDER BY e.id_evaluacion';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cursoId]);

        return $stmt->fetchAll();
    }

    public function porAprendiz(int $usuarioId): array
    {
        $sql = "SELECT e.*, c.titulo_curso, ee.estado,
                       (SELECT MAX(re.calificacion) FROM resultado_evaluacion re
                         WHERE re.id_evaluacion_r = e.id_evaluacion AND re.id_usuario_r = ?) calificacion,
                       (SELECT MAX(re.aprobado) FROM resultado_evaluacion re
                         WHERE re.id_evaluacion_r = e.id_evaluacion AND re.id_usuario_r = ?) aprobado
                FROM evaluacion e
                JOIN curso c ON c.id_curso = e.id_curso_e
                JOIN estado_evaluacion ee ON ee.id_estado = e.id_estado_e
                JOIN curso_aprendiz ca ON ca.id_curso_c_a = e.id_curso_e AND ca.id_usuario_c_a = ?
                WHERE ca.estado_c_a <> 'Inactivo'
                ORDER BY e.id_evaluacion";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $usuarioId, $usuarioId]);

        return $stmt->fetchAll();
    }

    public function preguntas(int $evaluacionId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM pregunta WHERE id_evaluacion_p = ? ORDER BY id_pregunta');
        $stmt->execute([$evaluacionId]);
        $preguntas = $stmt->fetchAll();

        foreach ($preguntas as &$pregunta) {
            $respuestas = $this->db->prepare('SELECT * FROM respuesta WHERE id_pregunta_r = ? ORDER BY id_respuesta');
            $respuestas->execute([$pregunta['id_pregunta']]);
            $pregunta['respuestas'] = $respuestas->fetchAll();
        }

        return $preguntas;
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO evaluacion (titulo_evaluacion, descripcion_evaluacion, puntaje_aprobacion, id_estado_e, id_curso_e)
             VALUES (?, ?, ?, 1, ?)'
        );
        $stmt->execute([$datos['titulo'], $datos['descripcion'], $datos['aprobacion'], $datos['curso']]);

        return (int) $this->db->lastInsertId();
    }

    public function crearPregunta(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO pregunta (tipo_pregunta, pregunta, id_evaluacion_p) VALUES (?, ?, ?)'
        );
        $stmt->execute([$datos['tipo'], $datos['pregunta'], $datos['evaluacion']]);
        $preguntaId = (int) $this->db->lastInsertId();

        $respuesta = $this->db->prepare(
            'INSERT INTO respuesta (respuesta, es_correcta, id_pregunta_r) VALUES (?, ?, ?)'
        );

        foreach ($datos['respuestas'] as $r) {
            $respuesta->execute([$r['texto'], $r['correcta'] ? 1 : 0, $preguntaId]);
        }

        return $preguntaId;
    }

    public function resultado(int $evaluacionId, int $usuarioId): ?array
    {
        $sql = 'SELECT re.*, e.titulo_evaluacion, e.puntaje_aprobacion
                FROM resultado_evaluacion re
                JOIN evaluacion e ON e.id_evaluacion = re.id_evaluacion_r
                WHERE re.id_evaluacion_r = ? AND re.id_usuario_r = ?
                ORDER BY re.id_resultado DESC
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$evaluacionId, $usuarioId]);

        return $stmt->fetch() ?: null;
    }

    public function insertarResultado(int $evaluacionId, int $usuarioId, float $nota, bool $aprobado): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO resultado_evaluacion (calificacion, aprobado, id_evaluacion_r, id_usuario_r)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$nota, $aprobado ? 1 : 0, $evaluacionId, $usuarioId]);

        return (int) $this->db->lastInsertId();
    }
}
