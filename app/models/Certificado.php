<?php

require_once __DIR__ . '/Model.php';

class Certificado extends Model
{
    public function usuario(int $usuarioId): array
    {
        $sql = 'SELECT ce.*, c.titulo_curso, u.nombre, u.apellido
                FROM certificado ce
                JOIN curso c ON c.id_curso = ce.id_curso_ce
                JOIN usuario u ON u.id_usuario = ce.id_usuario_ce
                WHERE ce.id_usuario_ce = ?
                ORDER BY ce.fecha_emision DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId]);

        return $stmt->fetchAll();
    }

    public function uno(int $id, int $usuarioId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ce.*, c.titulo_curso, u.nombre, u.apellido
             FROM certificado ce
             JOIN curso c ON c.id_curso = ce.id_curso_ce
             JOIN usuario u ON u.id_usuario = ce.id_usuario_ce
             WHERE ce.id_certificado = ? AND ce.id_usuario_ce = ?'
        );
        $stmt->execute([$id, $usuarioId]);

        return $stmt->fetch() ?: null;
    }

    public function crear(int $cursoId, int $usuarioId): int
    {
        $sql = 'SELECT COUNT(*) total, COALESCE(SUM(CASE WHEN re.aprobado = 1 THEN 1 ELSE 0 END), 0) aprobadas
                FROM evaluacion e
                JOIN curso c ON c.id_curso = e.id_curso_e
                LEFT JOIN resultado_evaluacion re ON re.id_evaluacion_r = e.id_evaluacion AND re.id_usuario_r = ?
                WHERE e.id_curso_e = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $cursoId]);
        $row = $stmt->fetch();

        if (!$row || (int) $row['total'] === 0 || (int) $row['total'] !== (int) $row['aprobadas']) {
            return 0;
        }

        $codigo = $this->generarCodigo($cursoId, $usuarioId);

        $stmt = $this->db->prepare(
            'INSERT INTO certificado (codigo_validacion, ruta_pdf, id_curso_ce, id_usuario_ce)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $codigo,
            'certificados/cert_' . $codigo . '.html',
            $cursoId,
            $usuarioId,
        ]);

        return (int) $this->db->lastInsertId();
    }

    private function generarCodigo(int $cursoId, int $usuarioId): string
    {
        return 'CERT-' . date('Y') . '-' . str_pad((string) $cursoId, 4, '0', STR_PAD_LEFT) . '-' . str_pad((string) $usuarioId, 6, '0', STR_PAD_LEFT);
    }
}
