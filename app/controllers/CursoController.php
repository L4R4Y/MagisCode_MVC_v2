<?php

require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Contenido.php';
require_once __DIR__ . '/../models/Evaluacion.php';
require_once __DIR__ . '/../helpers/Normalizador.php';
require_once __DIR__ . '/../helpers/VideoInfo.php';

class CursoController
{
    private const EXTENSIONES_PERMITIDAS = ['pdf', 'mp4', 'jpg', 'jpeg', 'png'];
    private const EXTENSIONES_IMAGENES = ['jpg', 'jpeg', 'png'];

    private function permitirRoles(int ...$roles): void
    {
        if (!in_array((int) $_SESSION['rol_id'], $roles, true)) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }
    }

    public function index(): void
    {
        $this->permitirRoles(1, 2);

        $modelo = new Curso();
        $c = (int) $_SESSION['rol_id'] === 2
            ? $modelo->delInstructor((int) $_SESSION['usuario'])
            : $modelo->todos();

        require __DIR__ . '/../views/instructor/cursos.php';
    }

    public function crear(): void
    {
        $this->permitirRoles(2);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagen = trim($_POST['imagen'] ?? '');

            $archivo = $_FILES['imagen'] ?? null;

            if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, self::EXTENSIONES_IMAGENES, true)) {
                    exit('Formato de imagen no permitido');
                }

                if ($archivo['size'] > 2 * 1024 * 1024) {
                    exit('La imagen es demasiado grande (máx. 2 MB).');
                }

                $directorio = __DIR__ . '/../../uploads/curso_imagenes';
                if (!is_dir($directorio)) {
                    mkdir($directorio, 0777, true);
                }

                $nombreArchivo = uniqid('curso_', true) . '.' . $extension;
                move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombreArchivo);

                $imagen = 'uploads/curso_imagenes/' . $nombreArchivo;
            }

            (new Curso())->crear([
                'titulo' => Normalizador::texto($_POST['titulo']),
                'descripcion' => Normalizador::texto($_POST['descripcion']),
                'imagen' => $imagen,
                'instructor' => (int) $_SESSION['usuario'],
            ]);

            header('Location: index.php?route=cursos');
            exit;
        }

        require __DIR__ . '/../views/instructor/crear_curso.php';
    }

    public function contenido(): void
    {
        $this->permitirRoles(1, 2, 3);

        $id = (int) ($_GET['id'] ?? 0);
        $modelo = new Curso();
        $curso = $modelo->uno($id);

        if (!$curso) {
            exit('Curso no encontrado');
        }

        $rol = (int) $_SESSION['rol_id'];
        $miAvance = $rol === 3
            ? $modelo->calcularAvance($id, (int) $_SESSION['usuario'])
            : null;
        $progresoVideos = $rol === 3
            ? $modelo->progresoVideos($id, (int) $_SESSION['usuario'])
            : null;

        if ($rol === 2 && (int) $curso['id_usuario_c'] !== (int) $_SESSION['usuario']) {
            exit('No tienes acceso a este curso.');
        }

        if ($rol === 3) {
            $inscrito = false;
            foreach ($modelo->asignados((int) $_SESSION['usuario']) as $asignacion) {
                if ((int) $asignacion['id_curso_c_a'] === $id) {
                    $inscrito = true;
                    break;
                }
            }

            if (!$inscrito) {
                exit('No tienes acceso a este curso.');
            }

            $modelo->actualizarAvance($id, (int) $_SESSION['usuario']);
            $recursosVistos = $modelo->recursosVistosCurso($id, (int) $_SESSION['usuario']);
        } else {
            $recursosVistos = [];
        }

        $modulos = (new Contenido())->modulos($id);
        $modeloEvaluacion = new Evaluacion();
        $evaluaciones = $modeloEvaluacion->porCurso(
            $id,
            $rol === 3 ? (int) $_SESSION['usuario'] : null
        );

        $totalRecursos = 0;
        $totalVideos = 0;
        foreach ($modulos as $modulo) {
            foreach ($modulo['lecciones'] as $leccion) {
                foreach ($leccion['recursos'] ?? [] as $recurso) {
                    $totalRecursos++;
                    if ($recurso['tipo_recurso'] === 'MP4') {
                        $totalVideos++;
                    }
                }
            }
        }

        require __DIR__ . '/../views/curso.php';
    }

    public function modulo(): void
    {
        $this->permitirRoles(2);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Contenido())->crearModulo([
                'nombre' => Normalizador::texto($_POST['nombre']),
                'orden' => (int) $_POST['orden'],
                'curso' => (int) $_POST['curso'],
            ]);

            header('Location: index.php?route=curso&id=' . (int) $_POST['curso']);
            exit;
        }
    }

    public function leccion(): void
    {
        $this->permitirRoles(2);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Contenido())->crearLeccion([
                'titulo' => Normalizador::texto($_POST['titulo']),
                'orden' => (int) $_POST['orden'],
                'modulo' => (int) $_POST['modulo'],
            ]);

            header('Location: index.php?route=curso&id=' . (int) $_POST['curso']);
            exit;
        }
    }

    public function recurso(): void
    {
        $this->permitirRoles(2);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $archivo = $_FILES['archivo'] ?? null;
        $ruta = trim($_POST['ruta'] ?? '');
        $tipo = 'PDF';
        $duracion = 0;

        if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, self::EXTENSIONES_PERMITIDAS, true)) {
                exit('Formato no permitido');
            }

            // Límite de 100 MB para recursos (videos y PDFs)
            if ($archivo['size'] > 100 * 1024 * 1024) {
                exit('El archivo es demasiado grande (máx. 100 MB).');
            }

            $directorio = __DIR__ . '/../../uploads/recursos';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $nombreArchivo = uniqid('recurso_', true) . '.' . $extension;
            move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombreArchivo);

            $ruta = 'uploads/recursos/' . $nombreArchivo;
            $tipo = strtoupper($extension);

            if ($tipo === 'MP4') {
                $duracion = VideoInfo::duracionSegundos($directorio . '/' . $nombreArchivo) ?? 0;
            }
        }

        (new Contenido())->crearRecurso([
            'nombre' => Normalizador::texto($_POST['nombre']),
            'tipo' => $tipo,
            'ruta' => $ruta,
            'duracion' => $duracion,
            'leccion' => (int) $_POST['leccion'],
        ]);

        (new Curso())->actualizarDuracion((int) $_POST['curso']);

        header('Location: index.php?route=curso&id=' . (int) $_POST['curso']);
        exit;
    }

    public function aprendices(): void
    {
        $this->permitirRoles(2);

        $modelo = new Curso();
        $cursos = $modelo->delInstructor((int) $_SESSION['usuario']);

        if (!$cursos) {
            $curso = null;
            $apr = [];
            $disponibles = [];
            require __DIR__ . '/../views/instructor/aprendices.php';
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $id = (int) $cursos[0]['id_curso'];
        }

        $curso = $modelo->uno($id);

        if (!$curso || (int) $curso['id_usuario_c'] !== (int) $_SESSION['usuario']) {
            http_response_code(403);
            exit('No autorizado');
        }

        $apr = $modelo->aprendicesCurso($id);
        $disponibles = $modelo->aprendicesDisponibles($id);

        require __DIR__ . '/../views/instructor/aprendices.php';
    }

    public function estado(): void
    {
        $this->permitirRoles(2);

        $id = (int) ($_POST['id'] ?? 0);
        $estado = trim($_POST['estado'] ?? '');

        $modelo = new Curso();
        $curso = $modelo->uno($id);

        if (!$curso || (int) $curso['id_usuario_c'] !== (int) $_SESSION['usuario']) {
            http_response_code(403);
            exit('No autorizado');
        }

        $modelo->cambiarEstado($id, $estado);

        header('Location: index.php?route=cursos');
        exit;
    }

    public function marcarVisto(): void
    {
        $this->permitirRoles(3);

        $id = (int) ($_GET['recurso'] ?? 0);
        $cursoId = (int) ($_GET['curso'] ?? 0);

        if ($id <= 0 || $cursoId <= 0) {
            http_response_code(400);
            exit('Parámetros inválidos');
        }

        $modelo = new Curso();
        $modelo->marcarRecursoVisto($id, (int) $_SESSION['usuario']);
        $modelo->actualizarAvance($cursoId, (int) $_SESSION['usuario']);

        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }

    public function progresoAjax(): void
    {
        $this->permitirRoles(3);

        $cursoId = (int) ($_GET['curso'] ?? 0);

        if ($cursoId <= 0) {
            http_response_code(400);
            exit('Parámetros inválidos');
        }

        $modelo = new Curso();
        $recursosVistos = $modelo->recursosVistosCurso($cursoId, (int) $_SESSION['usuario']);

        $avance = $modelo->calcularAvance($cursoId, (int) $_SESSION['usuario']);
        $progresoVideos = $modelo->progresoVideos($cursoId, (int) $_SESSION['usuario']);

        header('Content-Type: application/json');
        echo json_encode([
            'avance' => $avance,
            'progreso_videos' => $progresoVideos,
            'visto_ids' => $recursosVistos,
        ]);
        exit;
    }

    public function agregarAprendices(): void
    {
        $this->permitirRoles(2);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método no permitido');
        }

        $cursoId = (int) ($_POST['curso_id'] ?? 0);
        $aprendizIds = $_POST['aprendiz_ids'] ?? [];

        $modelo = new Curso();
        $curso = $modelo->uno($cursoId);

        if (!$curso || (int) $curso['id_usuario_c'] !== (int) $_SESSION['usuario']) {
            http_response_code(403);
            exit('No autorizado');
        }

        if (!empty($aprendizIds)) {
            foreach ($aprendizIds as $aprendizId) {
                $modelo->asignar($cursoId, (int) $aprendizId);
            }
        }

        header('Location: index.php?route=instructor-aprendices&id=' . $cursoId);
        exit;
    }
}
