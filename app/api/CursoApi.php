<?php

class CursoApi
{
    public function listar(): void
    {
        $cursoModel = new Curso();

        $cursos = $cursoModel->todos();

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'estado' => 'Exitoso',
            'mensaje' => 'Cursos consultados correctamente',
            'datos' => $cursos
        ]);

        exit;
    }
}