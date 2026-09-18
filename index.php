<?php

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

$route = $_GET['route'] ?? '';

if ($route === 'login') {
    (new AuthController())->login();
    exit;
}

if ($route === 'logout') {
    (new AuthController())->logout();
    exit;
}

if (!isset($_SESSION['usuario'])) {
    require __DIR__ . '/app/views/auth/login.php';
    exit;
}

// Mapa de rutas: nombre de la ruta => [Controlador, método].
$rutas = [
    'dashboard' => ['DashboardController', 'index'],

    'usuarios' => ['AdminController', 'usuarios'],
    'nuevo-usuario' => ['AdminController', 'nuevoUsuario'],
    'editar-usuario' => ['AdminController', 'editarUsuario'],
    'importar-usuarios' => ['AdminController', 'importarUsuarios'],
    'asignar' => ['AdminController', 'asignar'],
    'estado-usuario' => ['AdminController', 'estado'],

    'cursos' => ['CursoController', 'index'],
    'crear-curso' => ['CursoController', 'crear'],
    'curso' => ['CursoController', 'contenido'],
    'crear-modulo' => ['CursoController', 'modulo'],
    'crear-leccion' => ['CursoController', 'leccion'],
    'crear-recurso' => ['CursoController', 'recurso'],
    'instructor-aprendices' => ['CursoController', 'aprendices'],
    'agregar-aprendices' => ['CursoController', 'agregarAprendices'],
    'cambiar-estado-curso' => ['CursoController', 'estado'],
    'marcar-video-visto' => ['CursoController', 'marcarVisto'],

    'evaluaciones-instructor' => ['EvaluacionController', 'instructor'],
    'crear-evaluacion' => ['EvaluacionController', 'crear'],
    'crear-pregunta' => ['EvaluacionController', 'crearPregunta'],
    'evaluaciones-aprendiz' => ['EvaluacionController', 'aprendiz'],
    'evaluacion' => ['EvaluacionController', 'ver'],
    'responder-evaluacion' => ['EvaluacionController', 'responder'],
    'cerrar-evaluacion' => ['EvaluacionController', 'cerrar'],

    'progreso' => ['AprendizController', 'progreso'],
    'mis-cursos' => ['AprendizController', 'cursos'],
    'certificados' => ['AprendizController', 'certificados'],
    'certificado' => ['AprendizController', 'verCertificado'],
    'descargar-certificado' => ['AprendizController', 'descargarCertificado'],

    'reportes' => ['ReportController', 'admin'],
    'reportes-instructor' => ['ReportController', 'instructor'],
    'configuracion' => ['ConfiguracionController', 'index'],
    'cambiar-password' => ['ConfiguracionController', 'password'],
    'actualizar-perfil' => ['ConfiguracionController', 'actualizarPerfil'],
];

if ($route === '') {
    $route = 'dashboard';
}

if (!isset($rutas[$route])) {
    http_response_code(404);
    exit('Ruta no encontrada');
}

[$controlador, $metodo] = $rutas[$route];

require_once __DIR__ . '/app/controllers/' . $controlador . '.php';
(new $controlador())->$metodo();
