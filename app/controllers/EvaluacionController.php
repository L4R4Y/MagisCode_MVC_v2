<?php

require_once __DIR__ . '/../models/Evaluacion.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Certificado.php';
require_once __DIR__ . '/../helpers/Normalizador.php';

class EvaluacionController
{
    private function permitirSoloInstructor(): void
    {
        if ((int) $_SESSION['rol_id'] !== 2) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }
    }

    private function permitirSoloAprendiz(): void
    {
        if ((int) $_SESSION['rol_id'] !== 3) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }
    }

    public function crear(): void
    {
        $this->permitirSoloInstructor();

        $curso = (int) $_GET['curso'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Evaluacion())->crear([
                'titulo' => Normalizador::texto($_POST['titulo']),
                'descripcion' => Normalizador::texto($_POST['descripcion']),
                'aprobacion' => (float) $_POST['aprobacion'],
                'curso' => $curso,
            ]);

            header('Location: index.php?route=curso&id=' . $curso);
            exit;
        }

        require __DIR__ . '/../views/instructor/crear_evaluacion.php';
    }

    public function crearPregunta(): void
    {
        $this->permitirSoloInstructor();

        $modelo = new Evaluacion();
        $evaluacionId = (int) $_GET['evaluacion'];
        $curso = $modelo->cursoDe($evaluacionId);

        if (!$modelo->estaAbierta($evaluacionId)) {
            header('Location: index.php?route=curso&id=' . $curso);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = trim($_POST['tipo'] ?? '');
            $pregunta = Normalizador::texto($_POST['pregunta'] ?? '');
            $respuestas = [];

            foreach (($_POST['respuesta'] ?? []) as $indice => $texto) {
                if (trim($texto) !== '') {
                    $respuestas[] = [
                        'texto' => Normalizador::texto($texto),
                        'correcta' => (string) $indice === (string) ($_POST['correcta'] ?? ''),
                    ];
                }
            }

            $error = '';
            if ($tipo === '' || ($tipo !== 'Seleccion Multiple' && $tipo !== 'Verdadero/Falso' && $tipo !== 'Boolean')) {
                $error = 'Selecciona un tipo de pregunta válido.';
            } elseif ($pregunta === '') {
                $error = 'El campo pregunta es obligatorio.';
            } elseif (count($respuestas) < 2) {
                $error = 'Debes definir al menos dos respuestas.';
            }

            if ($error !== '') {
                require __DIR__ . '/../views/instructor/crear_pregunta.php';
                return;
            }

            $modelo->crearPregunta([
                'tipo' => $tipo,
                'pregunta' => $pregunta,
                'evaluacion' => $evaluacionId,
                'respuestas' => $respuestas,
            ]);

            header('Location: index.php?route=curso&id=' . $curso);
            exit;
        }

        require __DIR__ . '/../views/instructor/crear_pregunta.php';
    }

    public function ver(): void
    {
        $this->permitirSoloAprendiz();

        $id = (int) $_GET['id'];
        $modelo = new Evaluacion();
        $preguntas = $modelo->preguntas($id);
        $resultado = $modelo->resultado($id, (int) $_SESSION['usuario']);
        $eval = $modelo->conCurso($id);

        require __DIR__ . '/../views/aprendiz/evaluacion.php';
    }

    public function responder(): void
    {
        $this->permitirSoloAprendiz();

        $id = (int) $_POST['evaluacion'];
        $usuarioId = (int) $_SESSION['usuario'];
        $modelo = new Evaluacion();

        if ($modelo->resultado($id, $usuarioId)) {
            header('Location: index.php?route=evaluacion&id=' . $id);
            exit;
        }

        $preguntas = $modelo->preguntas($id);

        $correctas = 0;
        foreach ($preguntas as $pregunta) {
            $seleccionada = (int) ($_POST['p_' . $pregunta['id_pregunta']] ?? 0);

            foreach ($pregunta['respuestas'] as $respuesta) {
                if ((int) $respuesta['id_respuesta'] === $seleccionada && (int) $respuesta['es_correcta'] === 1) {
                    $correctas++;
                    break;
                }
            }
        }

        $total = count($preguntas);
        $nota = $total ? round($correctas / $total * 5, 2) : 0;

        $evaluacion = $modelo->uno($id);
        $modelo->insertarResultado(
            $id,
            (int) $_SESSION['usuario'],
            $nota,
            $nota >= (float) $evaluacion['puntaje_aprobacion']
        );

        $certificadoModelo = new Certificado();
        $certificadoModelo->crear((int) $evaluacion['id_curso_e'], (int) $_SESSION['usuario']);

        header('Location: index.php?route=evaluacion&id=' . $id);
        exit;
    }

    public function aprendiz(): void
    {
        $this->permitirSoloAprendiz();

        $evaluaciones = (new Evaluacion())->porAprendiz((int) $_SESSION['usuario']);

        require __DIR__ . '/../views/aprendiz/evaluaciones.php';
    }

    public function instructor(): void
    {
        $this->permitirSoloInstructor();

        $cursos = (new Curso())->delInstructor((int) $_SESSION['usuario']);
        $modelo = new Evaluacion();
        $evaluaciones = [];

        foreach ($cursos as $curso) {
            $evaluaciones = array_merge($evaluaciones, $modelo->porCurso((int) $curso['id_curso']));
        }

        require __DIR__ . '/../views/instructor/evaluaciones.php';
    }

    public function cerrar(): void
    {
        $this->permitirSoloInstructor();

        $id = (int) $_POST['evaluacion'] ?? 0;
        $cursoId = (int) $_POST['curso'] ?? 0;

        if ($id <= 0 || $cursoId <= 0) {
            http_response_code(400);
            exit('Parámetros inválidos');
        }

        $modelo = new Evaluacion();
        $eval = $modelo->uno($id);

        if (!$eval || (int) $eval['id_curso_e'] !== $cursoId) {
            http_response_code(403);
            exit('No autorizado');
        }

        $modelo->cerrar($id);

        header('Location: index.php?route=curso&id=' . $cursoId);
        exit;
    }
}
