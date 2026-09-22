<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/Normalizador.php';

class ConfiguracionController
{
    public function index(): void
    {
        $usuario = (new Usuario())->buscarPorId((int) $_SESSION['usuario']);
        require __DIR__ . '/../views/configuracion.php';
    }

    public function password(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        $actual = $_POST['actual'] ?? '';
        $nueva = $_POST['nueva'] ?? '';
        $conf = $_POST['confirmar'] ?? '';

        $u = (new Usuario())->buscarPorUsername($_SESSION['username']);
        $error = '';
        $ok = '';

        if ($actual === '' && $nueva === '' && $conf === '') {
            $error = 'Ingresa la contraseña actual, la nueva y su confirmación.';
        } elseif ($actual === '') {
            $error = 'El campo contraseña actual es obligatorio.';
        } elseif ($nueva === '') {
            $error = 'El campo nueva contraseña es obligatorio.';
        } elseif ($conf === '') {
            $error = 'El campo confirmar contraseña es obligatorio.';
        } elseif (!$u || (!password_verify($actual, $u['hash_contrasena']) && $actual !== $u['hash_contrasena'])) {
            $error = 'La contraseña actual no es correcta.';
        } elseif (strlen($nueva) < 6) {
            $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
        } elseif ($nueva !== $conf) {
            $error = 'Las contraseñas nuevas no coinciden.';
        } else {
            (new Usuario())->cambiarPassword((int) $_SESSION['usuario'], $nueva);
            $ok = 'Contraseña actualizada correctamente.';
        }

        $usuario = (new Usuario())->buscarPorId((int) $_SESSION['usuario']);
        require __DIR__ . '/../views/configuracion.php';
    }

    /**
     * APRENDIZ: puede corregir sus propios datos personales (nombre,
     * apellido y correo), pero no todos los campos del perfil.
     * Documento, usuario, rol, estado y contraseña quedan fuera de este
     * formulario y solo pueden cambiarlos desde la gestión de un
     * administrador (o, para la contraseña, desde el panel de Seguridad).
     */
    public function actualizarPerfil(): void
    {
        if ((int) $_SESSION['rol_id'] !== 3) {
            http_response_code(403);
            exit('Acceso no autorizado');
        }

        $error = '';
        $ok = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = Normalizador::texto($_POST['nombre'] ?? '');
            $apellido = Normalizador::texto($_POST['apellido'] ?? '');
            $correo = Normalizador::texto($_POST['correo'] ?? '');

            if ($nombre === '' && $apellido === '' && $correo === '') {
                $error = 'Ingresa nombre, apellido y correo.';
            } elseif ($nombre === '') {
                $error = 'El campo nombre es obligatorio.';
            } elseif ($apellido === '') {
                $error = 'El campo apellido es obligatorio.';
            } elseif ($correo === '') {
                $error = 'El campo correo es obligatorio.';
            } elseif (!preg_match('/^[\p{L}\s]{2,300}$/u', $nombre)) {
                $error = 'El nombre solo admite letras y debe tener al menos 2 caracteres.';
            } elseif (!preg_match('/^[\p{L}\s]{2,300}$/u', $apellido)) {
                $error = 'El apellido solo admite letras y debe tener al menos 2 caracteres.';
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $error = 'Ingresa un correo electrónico válido.';
            } else {
                try {
                    (new Usuario())->actualizarPerfilAprendiz((int) $_SESSION['usuario'], [
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'correo' => $correo,
                    ]);

                    // El nombre en sesión se usa en el menú y encabezado.
                    $_SESSION['nombre'] = trim($nombre . ' ' . $apellido);
                    $ok = 'Tus datos se actualizaron correctamente.';
                } catch (Throwable $e) {
                    $error = 'No fue posible actualizar tus datos. Verifica que el correo no esté siendo usado por otra cuenta.';
                }
            }
        }

        $usuario = (new Usuario())->buscarPorId((int) $_SESSION['usuario']);
        require __DIR__ . '/../views/configuracion.php';
    }
}
