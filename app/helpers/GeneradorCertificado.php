<?php

require_once __DIR__ . '/../../config/database.php';

class GeneradorCertificado
{
    public static function generar(int $certificadoId, int $usuarioId, int $cursoId): string
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'SELECT ce.*, c.titulo_curso, c.descripcion_curso,
                    u.nombre, u.apellido, u.username
             FROM certificado ce
             JOIN curso c ON c.id_curso = ce.id_curso_ce
             JOIN usuario u ON u.id_usuario = ce.id_usuario_ce
             WHERE ce.id_certificado = ? AND ce.id_usuario_ce = ? AND ce.id_curso_ce = ?'
        );
        $stmt->execute([$certificadoId, $usuarioId, $cursoId]);
        $cert = $stmt->fetch();

        if (!$cert) {
            return '';
        }

        $nombre = $cert['nombre'] . ' ' . $cert['apellido'];
        $curso = $cert['titulo_curso'];
        $fecha = date('d/m/Y', strtotime($cert['fecha_emision']));
        $codigo = $cert['codigo_validacion'];

        return self::construirPDF($nombre, $curso, $fecha, $codigo);
    }

    private static function construirPDF(string $nombre, string $curso, string $fecha, string $codigo): string
    {
        $lines = [];
        $lines[] = 'BT';
        $lines[] = '/F1 36 Tf';
        $lines[] = '1 0 0 1 306 700 Tm';
        $lines[] = '(Certificado de) Tj';
        $lines[] = '/F1 48 Tf';
        $lines[] = '1 0 0 1 306 660 Tm';
        $lines[] = '(Finalización) Tj';
        $lines[] = '/F1 28 Tf';
        $lines[] = '1 0 0 1 306 620 Tm';
        $lines[] = '(MagisCode) Tj';
        $lines[] = '/F1 22 Tf';
        $lines[] = '1 0 0 1 306 560 Tm';
        $lines[] = "(Se certifica que) Tj";
        $lines[] = '/F1 32 Tf';
        $lines[] = '1 0 0 1 306 530 Tm';
        $lines[] = '(' . self::escapePDF($nombre) . ') Tj';
        $lines[] = '/F1 22 Tf';
        $lines[] = '1 0 0 1 306 490 Tm';
        $lines[] = '(ha completado satisfactoriamente el curso de) Tj';
        $lines[] = '/F1 26 Tf';
        $lines[] = '1 0 0 1 306 460 Tm';
        $lines[] = '(' . self::escapePDF($curso) . ') Tj';
        $lines[] = '/F1 18 Tf';
        $lines[] = '1 0 0 1 306 420 Tm';
        $lines[] = '(con aprobación en todas las evaluaciones.) Tj';
        $lines[] = '/F1 16 Tf';
        $lines[] = '1 0 0 1 306 380 Tm';
        $lines[] = '(Fecha de emisión: ' . $fecha . ') Tj';
        $lines[] = '/F1 14 Tf';
        $lines[] = '1 0 0 1 306 350 Tm';
        $lines[] = '(Código de validación: ' . $codigo . ') Tj';
        $lines[] = 'ET';

        $content = implode("\n", $lines);
        $contentBytes = strlen($content);

        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>';
        $objects[] = '<< /Length ' . $contentBytes . ' >>';

        $stream = "stream\n" . $content . "\nendstream";
        $objects[3] = '<< /Length ' . $contentBytes . ' >>' . "\n" . $stream;

        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        $pdf = "%PDF-1.0\n";

        $offsets = [];
        for ($i = 0; $i < count($objects); $i++) {
            $offsets[$i + 1] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n";
            $pdf .= $objects[$i] . "\n";
            $pdf .= "endobj\n";
        }

        $xrefOffset = strlen($pdf);
        $objectCount = count($objects) + 1;
        $pdf .= "xref\n0 " . $objectCount . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 0; $i < count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i + 1]);
        }

        $pdf .= "trailer\n";
        $pdf .= "<< /Size " . $objectCount . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n";
        $pdf .= $xrefOffset . "\n";
        $pdf .= "%%EOF\n";

        return $pdf;
    }

    private static function escapePDF(string $s): string
    {
        $s = str_replace('\\', '\\\\', $s);
        $s = str_replace('(', '\(', $s);
        $s = str_replace(')', '\)', $s);
        return $s;
    }
}
