<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../helpers/Correo.php';

class AdminController
{
    private function ok(): void
    {
        if ((int) $_SESSION['rol_id'] !== 1) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }
    }

    public function usuarios(): void
    {
        $this->ok();
        $q = trim($_GET['q'] ?? '');
        $rol = trim($_GET['rol'] ?? '');
        $usuarios = (new Usuario())->listar($q, $rol);
        require __DIR__ . '/../views/admin/usuarios.php';
    }

    public function nuevoUsuario(): void
    {
        $this->ok();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $doc = (string) ($_POST['id_usuario'] ?? '');
            $baseN = preg_replace('/[^a-zA-Z]/', '', $nombre);
            $baseA = preg_replace('/[^a-zA-Z]/', '', $apellido);
            $username = strtolower(substr($baseN, 0, 3) . substr($baseA, 0, 3) . substr($doc, -3));

            $d = [
                'id_usuario' => (int) $doc,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => trim($_POST['correo'] ?? ''),
                'username' => $username,
                'password' => $_POST['password'] ?? 'hash123',
                'rol' => (int) $_POST['rol'],
                'tipo_documento' => (int) ($_POST['tipo_documento'] ?? 1),
            ];

            if ($nombre === '' || $apellido === '' || $d['correo'] === '' || $doc === '') {
                $error = 'Todos los campos marcados con (*) son obligatorios.';
            } else {
                try {
                    (new Usuario())->crear($d);

                    // APRENDIZ: se le avisa por correo que ya tiene un usuario en MagisCode.
                    $correoEnviado = false;
                    if ($d['rol'] === 3) {
                        $correoEnviado = Correo::enviarCuentaCreada(
                            $d['correo'],
                            trim($d['nombre'] . ' ' . $d['apellido']),
                            $d['username'],
                            $d['password']
                        );
                    }

                    $qs = 'ok=1' . ($correoEnviado ? '&correo=1' : '');
                    header('Location:index.php?route=usuarios&' . $qs);
                    exit;
                } catch (Throwable $e) {
                    $error = 'No fue posible crear el usuario. Verifica que documento, correo y usuario no estén repetidos.';
                }
            }
        }

        require __DIR__ . '/../views/admin/nuevo_usuario.php';
    }

    public function asignar(): void
    {
        $this->ok();
        $m = new Curso();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $m->asignar((int) $_POST['curso'], (int) $_POST['aprendiz']);
                header('Location:index.php?route=asignar&ok=1');
                exit;
            } catch (Throwable $e) {
                $error = 'El aprendiz ya tiene asignado ese curso.';
            }
        }

        $c = $m->todos();
        $apr = (new Usuario())->aprendices();
        require __DIR__ . '/../views/admin/asignar.php';
    }

    public function estado(): void
    {
        $this->ok();
        (new Usuario())->actualizarEstado((int) $_POST['id'], (int) $_POST['estado']);
        header('Location:index.php?route=usuarios');
        exit;
    }

    public function instructores(): void
    {
        $this->ok();
        $items = (new Usuario())->instructores();
        $titulo = 'Instructores';
        $subtitulo = 'Consulta los instructores registrados en MagisCode.';
        require __DIR__ . '/../views/admin/lista.php';
    }

    public function aprendices(): void
    {
        $this->ok();
        $items = (new Usuario())->aprendices();
        $titulo = 'Aprendices';
        $subtitulo = 'Consulta los aprendices registrados en MagisCode.';
        require __DIR__ . '/../views/admin/lista.php';
    }
}
