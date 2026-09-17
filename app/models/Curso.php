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
                ORDER BY ca.id_asignacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$aprendizId]);

        return $stmt->fetchAll();
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
}
