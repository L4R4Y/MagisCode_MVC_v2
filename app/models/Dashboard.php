<?php

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Curso.php';

class Dashboard extends Model
{
    public function admin(): array
    {
        return [
            'total' => (int) $this->db->query('SELECT COUNT(*) FROM usuario')->fetchColumn(),
            'activos' => (int) $this->db->query('SELECT COUNT(*) FROM usuario WHERE id_estado_u = 1')->fetchColumn(),
            'instructores' => (int) $this->db->query('SELECT COUNT(*) FROM usuario WHERE id_rol_u = 2')->fetchColumn(),
            'aprendices' => (int) $this->db->query('SELECT COUNT(*) FROM usuario WHERE id_rol_u = 3')->fetchColumn(),
            'cursos' => (int) $this->db->query('SELECT COUNT(*) FROM curso')->fetchColumn(),
        ];
    }

    public function instructor(int $instructorId): array
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM curso WHERE id_usuario_c = ?');
        $stmt->execute([$instructorId]);
        $cursos = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(DISTINCT ca.id_usuario_c_a)
             FROM curso_aprendiz ca
             JOIN curso c ON c.id_curso = ca.id_curso_c_a
             WHERE c.id_usuario_c = ?'
        );
        $stmt->execute([$instructorId]);
        $aprendices = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM evaluacion e
             JOIN curso c ON c.id_curso = e.id_curso_e
             WHERE c.id_usuario_c = ?'
        );
        $stmt->execute([$instructorId]);
        $evaluaciones = (int) $stmt->fetchColumn();

        return [
            'cursos' => $cursos,
            'aprendices' => $aprendices,
            'evaluaciones' => $evaluaciones,
        ];
    }

    public function aprendiz(int $aprendizId): array
    {
        $cursoModelo = new Curso();

        $stmt = $this->db->prepare(
            'SELECT ca.id_curso_c_a, ca.avance
             FROM curso_aprendiz ca
             WHERE ca.id_usuario_c_a = ?
               AND ca.estado_c_a <> \'Inactivo\''
        );
        $stmt->execute([$aprendizId]);
        $asignaciones = $stmt->fetchAll();
        $cursos = count($asignaciones);

        $sumaAvance = 0;
        $totalCursos = 0;
        foreach ($asignaciones as $asignacion) {
            $cursoId = (int) $asignacion['id_curso_c_a'];
            $sumaAvance += $cursoModelo->calcularAvance($cursoId, $aprendizId);
            $totalCursos++;
        }

        $progreso = $totalCursos > 0 ? round($sumaAvance / $totalCursos) : 0;

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM resultado_evaluacion WHERE id_usuario_r = ?');
        $stmt->execute([$aprendizId]);
        $evaluaciones = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM certificado WHERE id_usuario_ce = ?');
        $stmt->execute([$aprendizId]);
        $certificados = (int) $stmt->fetchColumn();

        return [
            'cursos' => $cursos,
            'progreso' => $progreso,
            'evaluaciones' => $evaluaciones,
            'certificados' => $certificados,
        ];
    }

    /**
     * Conteos generales que se muestran en el reporte del administrador.
     */
    public function reporteAdmin(): array
    {
        return [
            'usuarios' => (int) $this->db->query('SELECT COUNT(*) FROM usuario')->fetchColumn(),
            'cursos' => (int) $this->db->query('SELECT COUNT(*) FROM curso')->fetchColumn(),
            'asignaciones' => (int) $this->db->query('SELECT COUNT(*) FROM curso_aprendiz')->fetchColumn(),
            'certificados' => (int) $this->db->query('SELECT COUNT(*) FROM certificado')->fetchColumn(),
            'admins' => (int) $this->db->query('SELECT COUNT(*) FROM usuario WHERE id_rol_u = 1')->fetchColumn(),
            'instructores' => (int) $this->db->query('SELECT COUNT(*) FROM usuario WHERE id_rol_u = 2')->fetchColumn(),
            'aprendices' => (int) $this->db->query('SELECT COUNT(*) FROM usuario WHERE id_rol_u = 3')->fetchColumn(),
        ];
    }
}
