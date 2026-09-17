<?php

require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Dashboard.php';

class ReportController
{
    private function permitirSoloAdmin(): void
    {
        if ((int) $_SESSION['rol_id'] !== 1) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }
    }

    private function permitirSoloInstructor(): void
    {
        if ((int) $_SESSION['rol_id'] !== 2) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }
    }

    public function admin(): void
    {
        $this->permitirSoloAdmin();

        $r = (new Dashboard())->reporteAdmin();

        require __DIR__ . '/../views/admin/reportes.php';
    }

    public function instructor(): void
    {
        $this->permitirSoloInstructor();

        $datos = (new Curso())->reporteInstructor((int) $_SESSION['usuario']);

        $c = $datos['cursos'];
        $reporte = $datos['reporte'];
        $totalApr = $datos['total_aprendices'];
        $promedio = $datos['promedio_general'];

        require __DIR__ . '/../views/instructor/reportes.php';
    }
}
