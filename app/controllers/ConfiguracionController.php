<?php

require_once __DIR__ . '/../models/Usuario.php';

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

        if (!$u || (!password_verify($actual, $u['hash_contrasena']) && $actual !== $u['hash_contrasena'])) {
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
     * APRENDIZ: puede corregir sus propios datos de contacto (nombre,
     * apellido, correo, celular), pero no todos los campos del perfil.
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
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $celular = trim($_POST['celular'] ?? '');

            $nombreValido = preg_match('/^[\p{L}\s]{2,300}$/u', $nombre);
            $apellidoValido = preg_match('/^[\p{L}\s]{2,300}$/u', $apellido);
            $correoValido = filter_var($correo, FILTER_VALIDATE_EMAIL);
            $celularValido = $celular === '' || preg_match('/^[0-9]{7,15}$/', $celular);

            if (!$nombreValido || !$apellidoValido) {
                $error = 'El campo marcado con (*) "Nombre" y "Apellido" solo admite letras y debe tener al menos 2 caracteres.';
            } elseif (!$correoValido) {
                $error = 'Ingresa un correo electrónico válido.';
            } elseif (!$celularValido) {
                $error = 'El celular debe contener solo números (7 a 15 dígitos).';
            } else {
                try {
                    (new Usuario())->actualizarPerfilAprendiz((int) $_SESSION['usuario'], [
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'correo' => $correo,
                        'celular' => $celular !== '' ? $celular : null,
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
