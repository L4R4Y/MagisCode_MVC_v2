<?php

require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Evaluacion.php';
require_once __DIR__ . '/../models/Certificado.php';

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
}
