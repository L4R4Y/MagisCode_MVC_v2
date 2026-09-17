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
}
