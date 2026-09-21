<?php

require_once __DIR__ . '/Model.php';

class Curso extends Model
{
    public function todos(): array
    {
        $sql = "SELECT c.*,
                       CONCAT(u.nombre, ' ', u.apellido) instructor,
                       (SELECT COUNT(*) FROM curso_aprendiz ca WHERE ca.id_curso_c_a = c.id_curso) aprendices,
                       (SELECT COUNT(*) FROM modulo m WHERE m.id_curso_m = c.id_curso) modulos
                FROM curso c
                JOIN usuario u ON u.id_usuario = c.id_usuario_c
                ORDER BY c.id_curso DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function delInstructor(int $instructorId): array
    {
        $sql = "SELECT c.*,
                       (SELECT COUNT(*) FROM curso_aprendiz ca WHERE ca.id_curso_c_a = c.id_curso) aprendices,
                       (SELECT COUNT(*) FROM modulo m WHERE m.id_curso_m = c.id_curso) modulos
                FROM curso c
                WHERE c.id_usuario_c = ?
                ORDER BY c.id_curso DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$instructorId]);

        return $stmt->fetchAll();
    }

    public function uno(int $id): ?array
    {
        $sql = "SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) instructor
                FROM curso c
                JOIN usuario u ON u.id_usuario = c.id_usuario_c
                WHERE c.id_curso = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO curso (titulo_curso, descripcion_curso, ruta_imagen, estado_curso, id_usuario_c)
             VALUES (?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $datos['titulo'],
            $datos['descripcion'],
            $datos['imagen'] ?? '',
            'Activo',
            $datos['instructor'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE curso SET estado_curso = ? WHERE id_curso = ?'
        );

        return $stmt->execute([$estado, $id]);
    }

    public function actualizarDuracion(int $cursoId): bool
    {
        try {
            $stmt = $this->db->prepare(
                'UPDATE curso c
                 SET duracion_total = COALESCE((
                     SELECT SUM(r.duracion)
                     FROM recurso r
                     JOIN leccion l ON l.id_leccion = r.id_leccion_r
                     JOIN modulo m ON m.id_modulo = l.id_modulo_l
                     WHERE m.id_curso_m = c.id_curso
                 ), 0)
                 WHERE c.id_curso = ?'
            );

            return $stmt->execute([$cursoId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function asignar(int $cursoId, int $aprendizId): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO curso_aprendiz (estado_c_a, avance, id_curso_c_a, id_usuario_c_a)
             VALUES ('Activo', 0, ?, ?)"
        );

        return $stmt->execute([$cursoId, $aprendizId]);
    }

    public function asignados(int $aprendizId): array
    {
        $sql = "SELECT ca.*, c.titulo_curso, c.descripcion_curso, c.ruta_imagen,
                       CONCAT(u.nombre, ' ', u.apellido) instructor
                FROM curso_aprendiz ca
                JOIN curso c ON c.id_curso = ca.id_curso_c_a
                JOIN usuario u ON u.id_usuario = c.id_usuario_c
                WHERE ca.id_usuario_c_a = ?
                  AND ca.estado_c_a <> 'Inactivo'
                ORDER BY ca.id_asignacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$aprendizId]);

        $resultado = $stmt->fetchAll();

        foreach ($resultado as &$fila) {
            $fila['avance'] = $this->calcularAvance((int) $fila['id_curso_c_a'], $aprendizId);
        }
        unset($fila);

        return $resultado;
    }

    public function aprendicesCurso(int $cursoId): array
    {
        $sql = 'SELECT u.*, ca.avance, ca.estado_c_a
                FROM curso_aprendiz ca
                JOIN usuario u ON u.id_usuario = ca.id_usuario_c_a
                WHERE ca.id_curso_c_a = ?
                ORDER BY u.nombre, u.apellido';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cursoId]);

        return $stmt->fetchAll();
    }

    public function aprendicesDisponibles(int $cursoId): array
    {
        $sql = 'SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.username
                FROM usuario u
                WHERE u.id_rol_u = 3
                  AND u.id_estado_u = 1
                  AND u.id_usuario NOT IN (
                      SELECT ca.id_usuario_c_a
                      FROM curso_aprendiz ca
                      WHERE ca.id_curso_c_a = ?
                  )
                ORDER BY u.nombre, u.apellido';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cursoId]);

        return $stmt->fetchAll();
    }

    /**
     * Reporte de rendimiento de los cursos de un instructor: cantidad de
     * aprendices y avance promedio por curso, además de los totales
     * generales que se muestran en la vista de reportes del instructor.
     */
    public function reporteInstructor(int $instructorId): array
    {
        $cursos = $this->delInstructor($instructorId);
        $reporte = [];
        $totalAprendices = 0;
        $sumaPromedios = 0.0;

        foreach ($cursos as $curso) {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*), COALESCE(AVG(avance), 0)
                 FROM curso_aprendiz
                 WHERE id_curso_c_a = ?'
            );
            $stmt->execute([$curso['id_curso']]);
            [$cantidad, $promedio] = $stmt->fetch(PDO::FETCH_NUM);

            $totalAprendices += (int) $cantidad;
            $sumaPromedios += (float) $promedio;

            $reporte[] = [
                'titulo_curso' => $curso['titulo_curso'],
                'cantidad' => (int) $cantidad,
                'promedio' => (float) $promedio,
            ];
        }

        $totalCursos = count($cursos);

        return [
            'cursos' => $cursos,
            'reporte' => $reporte,
            'total_aprendices' => $totalAprendices,
            'promedio_general' => $totalCursos ? round($sumaPromedios / $totalCursos) : 0,
        ];
    }

    public function marcarRecursoVisto(int $recursoId, int $usuarioId): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO recurso_visto (id_recurso, id_usuario)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE fecha_visto = CURRENT_TIMESTAMP'
        );

        return $stmt->execute([$recursoId, $usuarioId]);
    }

    public function recursosVistosCurso(int $cursoId, int $usuarioId): array
    {
        $sql = 'SELECT DISTINCT r.id_recurso
                FROM recurso_visto rv
                JOIN recurso r ON r.id_recurso = rv.id_recurso
                JOIN leccion l ON l.id_leccion = r.id_leccion_r
                JOIN modulo m ON m.id_modulo = l.id_modulo_l
                WHERE m.id_curso_m = ? AND rv.id_usuario = ?';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$cursoId, $usuarioId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function calcularAvance(int $cursoId, int $usuarioId): int
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT COALESCE(SUM(CASE WHEN r.tipo_recurso = \'MP4\' THEN 1 ELSE 0 END), 0) total_videos
                 FROM recurso r
                 JOIN leccion l ON l.id_leccion = r.id_leccion_r
                 JOIN modulo m ON m.id_modulo = l.id_modulo_l
                 WHERE m.id_curso_m = ?'
            );
            $stmt->execute([$cursoId]);
            $totalVideos = (int) $stmt->fetchColumn();

            $stmt = $this->db->prepare(
                'SELECT COALESCE(COUNT(DISTINCT rv.id_recurso), 0) videos_vistos
                 FROM recurso_visto rv
                 JOIN recurso r ON r.id_recurso = rv.id_recurso
                 JOIN leccion l ON l.id_leccion = r.id_leccion_r
                 JOIN modulo m ON m.id_modulo = l.id_modulo_l
                 WHERE m.id_curso_m = ? AND rv.id_usuario = ?'
            );
            $stmt->execute([$cursoId, $usuarioId]);
            $videosVistos = (int) $stmt->fetchColumn();

            $stmt = $this->db->prepare(
                'SELECT COUNT(*) FROM evaluacion WHERE id_curso_e = ?'
            );
            $stmt->execute([$cursoId]);
            $totalEvaluaciones = (int) $stmt->fetchColumn();

            $stmt = $this->db->prepare(
                'SELECT COUNT(DISTINCT re.id_evaluacion_r)
                 FROM resultado_evaluacion re
                 WHERE re.id_usuario_r = ?
                   AND re.aprobado = 1
                   AND re.id_evaluacion_r IN (SELECT id_evaluacion FROM evaluacion WHERE id_curso_e = ?)'
            );
            $stmt->execute([$usuarioId, $cursoId]);
            $evaluacionesAprobadas = (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }

        $progresoVideos = $totalVideos > 0
            ? (int) round(($videosVistos / $totalVideos) * 90)
            : 0;

        $progresoEvaluaciones = $totalEvaluaciones > 0
            ? (int) round(($evaluacionesAprobadas / $totalEvaluaciones) * 10)
            : 0;

        return $progresoVideos + $progresoEvaluaciones;
    }

    public function progresoVideos(int $cursoId, int $usuarioId): int
    {
        $sql = 'SELECT
                    COALESCE(SUM(CASE WHEN r.tipo_recurso = \'MP4\' THEN 1 ELSE 0 END), 0) total_videos,
                    COALESCE(COUNT(DISTINCT rv.id_recurso), 0) videos_vistos
                FROM recurso r
                JOIN leccion l ON l.id_leccion = r.id_leccion_r
                JOIN modulo m ON m.id_modulo = l.id_modulo_l
                LEFT JOIN recurso_visto rv ON rv.id_recurso = r.id_recurso
                                          AND rv.id_usuario = ?
                WHERE m.id_curso_m = ?';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuarioId, $cursoId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return 0;
        }

        if (!$row || (int) $row['total_videos'] === 0) {
            return 0;
        }

        return (int) round(((int) $row['videos_vistos'] / (int) $row['total_videos']) * 100);
    }

    public function actualizarAvance(int $cursoId, int $usuarioId): bool
    {
        $avance = $this->calcularAvance($cursoId, $usuarioId);

        $stmt = $this->db->prepare(
            'UPDATE curso_aprendiz SET avance = ?
             WHERE id_curso_c_a = ? AND id_usuario_c_a = ?'
        );

        return $stmt->execute([$avance, $cursoId, $usuarioId]);
    }
}
