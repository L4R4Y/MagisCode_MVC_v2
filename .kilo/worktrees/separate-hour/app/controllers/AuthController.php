<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    public function login(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $username = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Debes ingresar usuario y contraseña.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $modelo = new Usuario();
        $usuario = $modelo->buscarPorUsername($username);

        if (!$usuario) {
            $error = 'Usuario o contraseña incorrectos.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if ((int) $usuario['id_estado_u'] !== 1) {
            $error = 'El usuario no está activo o se encuentra bloqueado.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Las contraseñas se almacenan únicamente mediante password_hash().
        $passwordCorrecta = password_verify(
            $password,
            $usuario['hash_contrasena']
        );

        if (!$passwordCorrecta) {
            $intentos = $modelo->registrarIntentoFallido((int) $usuario['id_usuario']);

            if ($intentos >= 5) {
                $modelo->actualizarEstado((int) $usuario['id_usuario'], 3);
                $error = 'Cuenta bloqueada por superar 5 intentos fallidos.';
            } else {
                $error = 'Usuario o contraseña incorrectos. Intentos restantes: ' . (5 - $intentos);
            }

            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Un acceso correcto reinicia el contador de intentos fallidos.
        $modelo->reiniciarIntentos((int) $usuario['id_usuario']);

        session_regenerate_id(true);

        $_SESSION['usuario'] = $usuario['id_usuario'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
        $_SESSION['rol_id'] = $usuario['id_rol_u'];
        $_SESSION['rol'] = $usuario['nombre_rol'];

        header('Location: index.php?route=dashboard');
        exit;
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();
        header('Location: index.php');
        exit;
    }
}
