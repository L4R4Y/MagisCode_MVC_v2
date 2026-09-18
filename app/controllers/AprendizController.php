<?php

require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Evaluacion.php';
require_once __DIR__ . '/../models/Certificado.php';
require_once __DIR__ . '/../helpers/GeneradorCertificado.php';

class AprendizController
{
    private function permitirSoloAprendiz(): void
    {
        if ((int) $_SESSION['rol_id'] !== 3) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }
    }

    public function cursos(): void
    {
        $this->permitirSoloAprendiz();

        $c = (new Curso())->asignados((int) $_SESSION['usuario']);

        require __DIR__ . '/../views/aprendiz/cursos.php';
    }

    public function progreso(): void
    {
        $this->permitirSoloAprendiz();

        $c = (new Curso())->asignados((int) $_SESSION['usuario']);

        require __DIR__ . '/../views/aprendiz/progreso.php';
    }

    public function certificados(): void
    {
        $this->permitirSoloAprendiz();

        $certificados = (new Certificado())->usuario((int) $_SESSION['usuario']);

        require __DIR__ . '/../views/aprendiz/certificados.php';
    }

    public function verCertificado(): void
    {
        $this->permitirSoloAprendiz();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(404);
            exit('Certificado no encontrado');
        }

        $certificado = (new Certificado())->uno($id, (int) $_SESSION['usuario']);

        if (!$certificado) {
            http_response_code(404);
            exit('Certificado no encontrado');
        }

        $pageTitle = 'Certificado - ' . $certificado['titulo_curso'];
        require __DIR__ . '/../views/aprendiz/certificado.php';
    }

    public function descargarCertificado(): void
    {
        $this->permitirSoloAprendiz();

        $id = (int) ($_GET['id'] ?? 0);
        $cursoId = (int) ($_GET['curso'] ?? 0);

        if ($id <= 0 || $cursoId <= 0) {
            http_response_code(400);
            exit('Parámetros inválidos');
        }

        $pdf = GeneradorCertificado::generar($id, (int) $_SESSION['usuario'], $cursoId);

        if ($pdf === '') {
            http_response_code(404);
            exit('Certificado no encontrado');
        }

        $cert = (new Certificado())->uno($id, (int) $_SESSION['usuario']);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="certificado-' . $cert['codigo_validacion'] . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }
}
