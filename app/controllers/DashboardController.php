<?php

require_once __DIR__ . '/../models/Dashboard.php';
require_once __DIR__ . '/../models/Curso.php';

class DashboardController
{
    public function index(): void
    {
        $rol = (int) $_SESSION['rol_id'];
        $dashboard = new Dashboard();
        $curso = new Curso();

        if ($rol === 1) {
            $stats = $dashboard->admin();
            $datos = $curso->todos();
        } elseif ($rol === 2) {
            $stats = $dashboard->instructor((int) $_SESSION['usuario']);
            $datos = $curso->delInstructor((int) $_SESSION['usuario']);
        } else {
            $stats = $dashboard->aprendiz((int) $_SESSION['usuario']);
            $datos = $curso->asignados((int) $_SESSION['usuario']);
        }

        require __DIR__ . '/../views/dashboard.php';
    }
}
